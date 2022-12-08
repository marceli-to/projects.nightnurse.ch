<template>
  <div :class="`flex text-dark relative feed-item ${getStateClasses()}`" :data-has-files="message.files.length > 0 && !message.deleted_at ? true : false" v-if="isLoaded">
    <div class="sm:max-w-[70%] lg:max-w-[60%] w-full p-3 lg:py-2 lg:pb-3 bg-white border-2 border-zinc-100 text-sm sm:text-base text-dark rounded">
      <div v-if="!message.deleted_at" class="relative">
          <shield-check-icon class="icon-card absolute right-1 top-1" aria-hidden="true" v-if="message.private" />


          <feed-item-header :class="[message.private ? 'text-slate-100': '', 'relative']">
            <span class="font-bold" v-if="message.sender">{{message.sender.full_name}}</span>
            <span v-else>[{{ translate('gelöschter Benutzer') }}]</span>
            {{ translate('an') }}
            <span class="has-tooltip" v-if="message.users && message.users.length > 2">
              <span class='tooltip'>
                {{ message.users.map(x => x.full_name).join(", ") }}
              </span>
              <a href="javascript:;" class="underline underline-offset-4 text-gray-400 decoration-dotted">{{ message.users.length }} {{ translate('Empfänger') }}</a>
            </span>
            <span v-else>
             {{ message.users.map(x => x.full_name).join(", ") }}
            </span>
            {{ translate('um') }} {{message.message_time}}
          </feed-item-header>

          <feed-item-body v-if="message.subject || message.body" :data-item-uuid="message.uuid">
            <a href="" 
              class="no-underline hover:text-highlight flex items-center text-sm mb-2"
              @click.prevent="toggleMessageBody(message.uuid)"
              v-if="$props.viewType == 'files-only'">
              {{ message.body | truncate(25, '...') }}
            </a>
            <div 
              :class="[message.body ? 'font-bold' : '', 'text-sm']" 
              v-if="message.subject">
              {{ message.subject }}
            </div>
            <div class="text-sm" v-html="message.body"></div>

            <div class="flex justify-end w-full">
              <a href="javascript:;" class="block mr-1" @click.prevent="deeplFy(message.body, 'en-GB')">
                <flag-en-icon />
              </a>
              <a href="javascript:;" class="block" @click.prevent="deeplFy(message.body, 'de')">
                <flag-de-icon />
              </a>
            </div>
          </feed-item-body>

          <div v-if="message.files.length > 0" :class="[message.subject || message.body ? 'mt-2 lg:mt-4' : 'mt-1 lg:mt-3']">

            <feed-item-attachement 
              v-for="(file, idx) in message.files"
              :key="file.uuid"
              :index="idx"
              :file="file"
              :truncate="truncateFiles"
              :private="message.private">
            </feed-item-attachement>

            <span class="sm:flex sm:items-center justify-between text-xs font-mono pb-1 pt-4" v-if="(message.files.length > 3)">

              <a 
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private ? 'text-slate-100' : 'text-gray-400', 'flex items-center no-underline hover:underline mt-3 sm:mt-0']"
                v-if="truncateFiles">
                <chevron-down-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">{{ translate('Mehr anzeigen') }} ({{message.files.length - 3}})</span>
              </a>

              <a
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private ? 'text-slate-100' : 'text-gray-400', 'flex items-center no-underline hover:underline mt-3 sm:mt-0']"
                v-else>
                <chevron-up-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">{{ translate('Weniger anzeigen') }}</span>
              </a>

              <a 
                :href="`/download/zip/${message.uuid}`" 
                target="_blank" 
                :class="[message.private ? 'text-white' : 'text-gray-400', 'flex items-center no-underline hover:underline mt-3 sm:mt-0']"
                v-if="message.files.length > 1">
                <folder-download-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">{{ translate('Download als ZIP') }}</span>
              </a>

            </span>

          </div>

        </div>

        <div v-else>
          <feed-item-body>
            <div class="text-xs text-gray-400 font-mono italic sm:pt-1">
              {{ translate('Nachricht gelöscht durch') }} {{message.sender.full_name}}
            </div>
          </feed-item-body>
        </div>

        <a href="javascript:;" 
          @click.prevent="destroy(message.uuid)" 
          class="feed-item-delete flex items-center leading-none" 
          v-if="message.can_delete && !message.deleted_at">
          <trash-icon class="w-4 h-4 mr-2" />
          {{ translate('Nachricht löschen') }}
        </a>
    </div>
  </div>
</template>
<script>
import { 
  TrashIcon, 
  ShieldCheckIcon, 
  CloudDownloadIcon, 
  FolderDownloadIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  ChevronUpIcon,
} 
from "@vue-hero-icons/outline";
import FeedItemHeader from "@/components/ui/feed/Header.vue";
import FeedItemTimestamp from "@/components/ui/feed/TimeStamp.vue";
import FeedItemAttachement from "@/components/ui/feed/Attachement.vue";
import FeedItemBody from "@/components/ui/feed/Body.vue";
import FlagDeIcon from '@/components/ui/icons/flag-de';
import FlagEnIcon from '@/components/ui/icons/flag-en';
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    TrashIcon,
    ShieldCheckIcon,
    FolderDownloadIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    CloudDownloadIcon,
    FlagDeIcon,
    FlagEnIcon,
    FeedItemHeader,
    FeedItemTimestamp,
    FeedItemAttachement,
    FeedItemBody,
  },

  mixins: [i18n],

  data() {
    return {

      message: null,
      truncateFiles: false,

      // Routes
      routes: {
        destroy: '/api/message',
        translate: '/api/translate'
      },

      // States
      isLoaded: false,
    };
  },

  props: {
    item: {
      type: Object,
      default: null
    },

    viewType: {
      type: String,
      default: 'standard'
    }
  },

  mounted() {
    this.message = this.$props.item;
    this.truncateFiles = this.message.files.length > 3 ? true : false;
    this.isLoaded = true;
  },

  methods: {

    destroy(uuid, event) {
      if (confirm(this.translate('Bitte löschen bestätigen'))) {
        NProgress.start();
        this.axios.delete(`${this.routes.destroy}/${uuid}`).then(response => {
          this.$emit('itemDeleted');
          NProgress.done();
        });
      }
    },

    toggle() {
      this.truncateFiles = this.truncateFiles ? false : true;
    },

    toggleMessageBody(itemUuid) {
      const item = document.querySelector('[data-item-uuid="'+itemUuid+'"]');
      item.classList.toggle('is-truncated');
    },

    getStateClasses() {
      let cls = '';
      if (this.message.can_delete && !this.message.deleted_at) {
        cls += 'can-delete ';
      }
      if (this.message.private) {
        cls += 'is-private '
      }
      if (this.message.internal) {
        cls += 'feed-item--left '
      }
      else {
        cls += 'feed-item--right '
      }
      return cls;
    },

    deeplFy(message, language) {
      NProgress.start();
      this.axios.post(this.routes.translate, {message: message, language: language}).then(response => {
        this.message.body = response.data.message;
        NProgress.done();
      });
    }
  },
}
</script>
<style>
.feed-item ul {
  @apply mb-4
}

.feed-item li {
  @apply !mt-1 !mb-1
}

.feed-item.is-private > div {
  @apply bg-slate-400 border-slate-400 !text-slate-100
}

.feed-item.is-private a {
  @apply text-slate-100
}

.feed-item.is-private .feed-item__body > div {
  @apply !text-slate-100
}

.feed-item--left {
  @apply justify-start
}

.feed-item--right {
  @apply justify-end
}

.feed-item--left > div {
  @apply bg-zinc-50
}

.feed-item-delete {
  @apply absolute -bottom-[30px] text-xs !text-gray-400 no-underline pl-0 font-mono
}

.feed-item-delete:hover {
  @apply !text-highlight
}

.feed-item--right .feed-item-delete {
  @apply right-0
}

.feed-item--left .feed-item-delete {
  @apply left-0
}

.feed-item {
  @apply mb-8 lg:mb-10
}

.feed-item.can-delete {
  @apply mb-14 lg:mb-16
}
</style>