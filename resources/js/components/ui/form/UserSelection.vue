<template>
<div>
  
  <div class="form-group relative">
    <a href="" @click.prevent="$refs.widgetUserSelection.show()" class="absolute right-0 top-0 text-xs font-mono text-gray-400 flex items-center no-underline hover:text-highlight">
      <plus-sm-icon class="h-4 w-4" aria-hidden="true" />
      <span class="block ml-1">{{ translate('Hinzufügen') }}</span>
    </a>
    <label class="mb-1">{{ translate('Empfänger') }}</label>
    <div class="text-dark text-sm lg:text-base mt-0 block w-full px-0 pt-1.5 pb-1.5 border-0 border-bottom focus:ring-0 focus:border-dark">
      <template v-if="($props.recipients.length > 0)">
        <a href="javascript:;" 
          class="rounded-full inline-flex w-auto items-center bg-dark hover:bg-highlight border-2 border-dark border-solid hover:border-highlight p-1.5 px-2.5 text-white font-mono mr-3 mb-2 text-xs sm:text-xs no-underline"
          @click.prevent="addOrRemoveRecipient(false, recipient)"
          v-for="recipient in $props.recipients" :key="recipient.uuid">
          <span class="inline-block mr-2">
            {{ recipient.name ? `${recipient.firstname} ${recipient.name}` : recipient.email }}
          </span>
          <x-icon class="h-4 w-4" aria-hidden="true"></x-icon>
        </a>
      </template>
    </div>
  </div>

  <lightbox ref="widgetUserSelection" :maxWidth="'w-auto min-w-min sm:min-w-[480px] max-w-[300px] sm:max-w-[70%]'">
    <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-2 sm:mb-3">{{ translate('Empfänger auswählen') }}</h2>
    <nav class="flex max-h-96 overflow-auto">
      <div v-for="team in owner.teams" :key="team.uuid" class="flex-auto pr-6">
        <div class="mr-6">
          <user-selector 
            :client="{name: `Nightnurse ${team.description}`, uuid: team.uuid}"
            :users="team.users"
            :associates="getTeamAssociates($props.associates, team.id)"
            :canToggleAll="$store.state.user.admin ? true : false"
            @addOrRemoveRecipient="addOrRemoveRecipient">
          </user-selector>
        </div>
      </div>
      <div class="flex-auto pr-6" v-if="!$props.private">
        <div v-for="client in $props.clients" :key="client.uuid">
          <user-selector 
            :client="{name: client.data.name, uuid: client.data.uuid}" 
            :users="client.users"
            @addOrRemoveRecipient="addOrRemoveRecipient">
          </user-selector>
        </div>
      </div>
    </nav>
  </lightbox>

</div>
</template>

<script>
import { XIcon, PlusSmIcon } from "@vue-hero-icons/outline";
import Lightbox from "@/components/ui/layout/Lightbox.vue";
import UserSelector from "@/components/ui/form/UserSelector.vue";
import i18n from "@/i18n";

export default {

  mixins: [i18n],

  components: {
    Lightbox,
    PlusSmIcon,
    XIcon,
    UserSelector
  },

  props: {

    recipients: {
      type: Array,
      default: () => [],
    },

    associates: {
      type: Array,
      default: () => [],
    },

    owner: {
      type: Object,
      default: () => {},
    },

    clients: {
      type: Object,
      default: () => {},
    },

    private: {
      type: [Number, Boolean],
      default: false,
    }
  },

  mounted() {
  },

  methods: {
    addOrRemoveRecipient(state, uuid) {
      this.$emit('addOrRemoveRecipient', state, uuid);
    },

    getUsersWithoutPreselected(users, teamId = null) {
      this.$props.recipients.forEach(recipient => {
        const index = users.findIndex(x => x.uuid === recipient.uuid);
        if (index > -1) {
          users.splice(index, 1);
        }
      });
      return users;
    },

    getTeamAssociates(users, teamId) {
      const filteredUser = users.filter((user) => user.team_id === teamId);
      return filteredUser;
    }

  },
}
</script>