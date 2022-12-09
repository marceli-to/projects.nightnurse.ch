<template>
  <div v-if="$props.users.length > 0" class="mb-6 lg:mb-8">
    
    <template v-if="canToggleAll">
      <div class="mb-2">
        <div class="form-check">
          <input 
            type="checkbox" 
            class="checkbox" 
            :id="$props.client.uuid" 
            @change="toggleAll($event, $props.client.uuid)">
          <label class="inline-block text-gray-800 font-bold" :for="$props.client.uuid">
            {{ $props.client.name }}
          </label>
        </div>
      </div>
    </template>
    <template v-else>
      <div class="inline-block text-sm text-dark font-sans font-bold mb-1">
        {{ $props.client.name }}
      </div>
    </template>

    <div class="mb-1">
      <template v-if="$props.associates">
        <div v-for="(user, index) in $props.associates" :key="user.uuid" class="mb-2">
          <div class="form-check">
            <input 
              type="checkbox" 
              class="checkbox" 
              :value="user.uuid" 
              :id="user.uuid" 
              :checked="isChecked(user)"
              :data-team-uuid="$props.client.uuid"
              :data-user-email="user.email"
              :data-user-name="user.name"
              :data-user-firstname="user.firstname"
              @change="toggleOne($event, user)">
            <label class="inline-block text-gray-800" :for="user.uuid">
              {{ user.name ? `${user.firstname} ${user.name}` : user.email }}
            </label>
          </div>
        </div>
      </template>

      <div v-for="(user, index) in $props.users" :key="user.uuid" class="mb-2">
        <div :class="[index < 6 ? 'flex' : 'hidden', 'form-check']" :data-truncatable="$props.client.uuid" :data-truncatable-index="index">
          <input 
            type="checkbox" 
            class="checkbox" 
            :value="user.uuid" 
            :id="user.uuid"
            :checked="isChecked(user)" 
            :data-team-uuid="$props.client.uuid"
            :data-user-email="user.email"
            :data-user-name="user.name"
            :data-user-firstname="user.firstname"
            @change="toggleOne($event, user)">
            <label class="inline-block text-gray-800" :for="user.uuid">
              {{ user.name ? `${user.firstname} ${user.name}` : user.email }}
            </label>
        </div>
      </div>
    </div>

    <template>
      <a 
        href="javascript:;" 
        @click="showOverflow($props.client.uuid)"
        :data-truncatable-more="$props.client.uuid"
        class="text-gray-400 flex items-center no-underline hover:underline mt-3 sm:mt-0"
        v-if="$props.users.length > 10">
        <chevron-down-icon class="h-5 w-5" aria-hidden="true" />
        <span class="inline-block ml-2 text-xs font-mono">{{ translate('Mehr anzeigen') }}</span>
      </a>
      <a
        href="javascript:;" 
        @click="hideOverflow($props.client.uuid)"
        :data-truncatable-less="$props.client.uuid"
        class="text-gray-400 hidden items-center no-underline hover:underline mt-3 sm:mt-0">
        <chevron-up-icon class="h-5 w-5" aria-hidden="true" />
        <span class="inline-block ml-2 text-xs font-mono">{{ translate('Weniger anzeigen') }}</span>
      </a>
    </template>
  </div>
</template>

<script>
import { ChevronDownIcon, ChevronUpIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";

export default {

  components: {
    ChevronUpIcon,
    ChevronDownIcon
  },

  mixins: [i18n],

  props: {

    client: {
      type: Object,
      default: {
        uuid: null,
        name: null,
      },
    },

    associates: {
      type: Array,
      default: () => [],
    },

    users: {
      type: Array,
      default: () => [],
    },

    canToggleAll: {
      type: Boolean,
      default: true
    }
  },

  data() {
    return {
    }
  },

  mounted() {
  },

  methods: {

    toggleAll(event, uuid) {
      const _this = this;
      const state = event.target.checked ? true : false;
      const boxes = document.querySelectorAll('[data-team-uuid="'+uuid+'"]');
      boxes.forEach(function(box){
        box.checked = state;
        const user = {
          uuid: box.value,
          name: box.dataset.userName, 
          firstname: box.dataset.userFirstname, 
          email: box.dataset.userEmail
        };
        _this.addOrRemoveRecipient(state, user);
      });
    },

    toggleOne(event, user) {
      const state = event.target.checked ? true : false;
      this.addOrRemoveRecipient(state, user);
    },

    addOrRemoveRecipient(state, user) {
      this.$emit('addOrRemoveRecipient', state, user);
    },

    showOverflow(uuid) {
      const recipients = document.querySelectorAll('[data-truncatable="'+uuid+'"]');
      recipients.forEach(function(recipient){
        recipient.classList.remove('hidden');
        recipient.classList.add('flex');
      });

      const btnMore = document.querySelector('[data-truncatable-more="'+uuid+'"]');
      btnMore.classList.remove('flex');
      btnMore.classList.add('hidden');

      const btnLess = document.querySelector('[data-truncatable-less="'+uuid+'"]');
      btnLess.classList.remove('hidden');
      btnLess.classList.add('flex');
    },

    hideOverflow(uuid) {
      const recipients = document.querySelectorAll('[data-truncatable="'+uuid+'"]');
      recipients.forEach(function(recipient){
        if (recipient.dataset.truncatableIndex >= 6) {
          recipient.classList.remove('flex');
          recipient.classList.add('hidden');
        }
      });

      const btnLess = document.querySelector('[data-truncatable-less="'+uuid+'"]');
      btnLess.classList.remove('flex');
      btnLess.classList.add('hidden');

      const btnMore = document.querySelector('[data-truncatable-more="'+uuid+'"]');
      btnMore.classList.remove('hidden');
      btnMore.classList.add('flex');

    },

    isChecked(user) {
      return this.$store.state.recipients.find(x => x.uuid === user.uuid) ? true : false;
    }
  }
}
</script>