<template>
<div>
  <div :class="[$props.hasErrors ? 'is-invalid' : '', 'form-group relative']">
    <a href="" @click.prevent="show()" class="absolute right-0 top-0 text-xs font-mono text-gray-400 flex items-center no-underline hover:text-highlight">
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

  <lightbox ref="widgetUserSelection" :maxWidth="'w-full max-w-[300px] sm:max-w-[400px] lg:max-w-[500px]'" :styles="'top-4 lg:top-12 translate-y-0'">
    <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">{{ translate('Empfänger auswählen') }}</h2>
    <div class="mt-3 mb-6 pr-5 relative">
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
        <search-icon class="w-5 h-5 text-gray-400" />
      </div>  
      <input type="text" v-model="keyword" :placeholder="translate('Suche...')" class="!text-base placeholder:text-sm placeholder:text-gray-400 !pl-7 !border-b !border-t !border-gray-200 focus:!border-gray-200" />
    </div>
    <nav class="max-h-96 lg:max-h-[42rem] pr-4">
      <div v-for="team in filterOwnerTeams(owner.teams)" :key="team.uuid">
        <user-selector 
          :keyword="keyword"
          :client="{name: `Nightnurse ${team.description}`, uuid: team.uuid}"
          :users="team.users"
          :associates="$store.state.user.admin ? getTeamAssociates($props.associates, team.id) : $props.associates"
          :canToggleAll="$store.state.user.admin ? true : false"
          @addOrRemoveRecipient="addOrRemoveRecipient">
        </user-selector>
      </div>
      <template v-if="!$props.private">
        <div v-for="client in $props.clients" :key="client.uuid">
          <user-selector 
            :keyword="keyword"
            :client="{name: client.data.name, uuid: client.data.uuid}" 
            :users="client.users"
            @addOrRemoveRecipient="addOrRemoveRecipient">
          </user-selector>
        </div>
      </template>
    </nav>
  </lightbox>

</div>
</template>

<script>
import { XIcon, PlusSmIcon, SearchIcon } from "@vue-hero-icons/outline";
import Lightbox from "@/components/ui/layout/Lightbox.vue";
import UserSelector from "@/components/ui/form/UserSelector.vue";
import i18n from "@/i18n";

export default {

  mixins: [i18n],

  components: {
    Lightbox,
    PlusSmIcon,
    SearchIcon,
    XIcon,
    UserSelector
  },

  data() {
    return {
      keyword: null,
    }
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
      type: [Object, Array],
      default: () => {},
    },

    manager: {
      type: Object,
      default: () => {},
    },

    private: {
      type: [Number, Boolean],
      default: false,
    },

    hasErrors: {
      type: [Number, Boolean],
      default: false,
    }
  },

  mounted() {
    this.keyword = null;
  },

  methods: {
    
    addOrRemoveRecipient(state, uuid) {
      this.$emit('addOrRemoveRecipient', state, uuid);
    },

    getTeamAssociates(users, teamId) {
      const filteredUser = users.filter((user) => user.team_id == teamId);
      return filteredUser;
    },

    filterOwnerTeams(teams) {
      // filter out owner teams, if $props.private is true, show all teams
      if (this.$props.private) {
        return teams;
      }
      // otherwise filter out team with id 2 (Buenos Aires)
      const filteredTeams = teams.filter((team) => team.id !== 2);
      return filteredTeams;
    },

    show() {
      this.keyword = null;
      this.$refs.widgetUserSelection.show()
    }

  },
}
</script>