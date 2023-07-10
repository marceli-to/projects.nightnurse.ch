<template>
  <div 
    :class="[ message.private ? 'is-private' : message.internal ? 'is-internal' : '', 'feed-item group']" 
    v-if="isLoaded">
    <div class="sm:max-w-[70%] lg:max-w-[60%] w-full relative">

      <div class="p-3 lg:py-2 lg:pb-3 bg-white border-2 border-gray-100 text-sm sm:text-base text-dark relative rounded group-[.is-internal]:border-transparent group-[.is-internal]:bg-gray-100 group-[.is-private]:bg-alice-blue group-[.is-private]:border-alice-blue">
        
        <template v-if="message.deleted_at">
          <feed-item-body>
            <div class="text-xs text-gray-400 font-mono italic sm:pt-1">
              {{ translate('Nachricht gelöscht durch') }} {{message.sender.full_name}}
            </div>
          </feed-item-body>
        </template>

        <template v-else>
          <shield-check-icon 
            class="icon-card absolute right-1 top-1 sm:w-5 sm:h-5" 
            aria-hidden="true" 
            v-if="message.private">
          </shield-check-icon>

          <feed-item-header :class="[message.private ? 'text-dark': '', 'relative']">
            <span class="font-bold">{{message.sender.full_name}}</span>
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

          <feed-item-reply
            :originalMessage="message.original_message"
            @scrollTo="$emit('scrollTo', $event)"
            v-if="message.is_reply">
          </feed-item-reply>

          <feed-item-body v-if="message.subject || message.body">
            <a 
              href="javascript:;"
              class="no-underline font-mono text-gray-400 text-xs hover:text-highlight flex items-center text-sm mt-1 mb-3" 
              @click.prevent="toggleMessageBody(message.uuid)"
              v-if="hideMessageBody">
              <plus-sm-icon class="h-4 w-4" aria-hidden="true" />
              <span class="block ml-1">{{ translate('Nachricht anzeigen') }}</span>
            </a>
            <template v-if="!hideMessageBody">
              <div :class="[message.body ? 'font-bold' : '', 'text-sm']" v-if="message.subject">
                {{ message.subject }}
              </div>
              <div class="text-sm" v-html="message.body"></div>
            </template>
          </feed-item-body>

          <div 
            :class="[message.subject || message.body ? 'mt-2 lg:mt-4' : 'mt-1 lg:mt-3', '']"
            v-if="message.files.length > 0">
            <feed-item-attachement 
              v-for="(file, idx) in message.files"
              :key="file.uuid"
              :index="idx"
              :file="file"
              :truncate="hasTruncateFiles"
              :private="message.private">
            </feed-item-attachement>
            <span class="sm:flex sm:items-center justify-between text-xs font-mono pb-1 pt-4" v-if="(message.files.length > 3)">
              <a 
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private || message.internal ? 'text-gray-400' : 'text-dark', 'flex items-center no-underline hover:text-highlight mt-3 sm:mt-0']"
                v-if="hasTruncateFiles">
                <chevron-down-icon class="h-4 w-4" aria-hidden="true" />
                <span class="inline-block ml-2">{{ translate('Mehr anzeigen') }} ({{message.files.length - 3}})</span>
              </a>
              <a
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private || message.internal ? 'text-gray-400' : 'text-dark', 'flex items-center no-underline hover:text-highlight mt-3 sm:mt-0']"
                v-else>
                <chevron-up-icon class="h-4 w-4" aria-hidden="true" />
                <span :class="[message.private || message.internal ? 'text-gray-400' : 'text-dark', 'hover:text-highlight inline-block ml-2']">{{ translate('Weniger anzeigen') }}</span>
              </a>

              <a 
                :href="`/download/zip/${message.uuid}`" 
                target="_blank" 
                :class="[message.private || message.internal ? 'text-gray-400' : 'text-dark', 'flex items-center no-underline hover:text-highlight mt-3 sm:mt-0']"
                v-if="message.files.length > 1">
                <folder-download-icon class="h-5 w-5" aria-hidden="true" />
                <span :class="[message.private || message.internal ? 'text-gray-400' : 'text-dark', 'hover:text-highlight inline-block ml-2']">{{ translate('Download als ZIP') }}</span>
              </a>
              
            </span>
          </div>

          <feed-item-reactions 
            :reactions="message.reactions">
          </feed-item-reactions>

        </template>
      </div>

      <div class="flex justify-between">
        <feed-item-actions 
          :message="message"
          :canDelete="(message.can_delete && !message.deleted_at)"
          :canReply="$props.projectState.id == 1 ? true : false"
          @delete="destroy"
          @translate="deeplFy"
          @reply="$emit('reply', $event)"
          v-if="!message.deleted_at">
        </feed-item-actions>

        <feed-item-reactions-menu
          :message="message"
          :canReact="$props.projectState.id == 1 ? true : false"
          @reactionStored="fetch()"
          v-if="!message.deleted_at">
        </feed-item-reactions-menu>
      </div>
    </div>
  </div>
</template>
<script>
import { 
  PlusSmIcon,
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
import FeedItemActions from "@/components/ui/feed/Actions.vue";
import FeedItemReply from "@/components/ui/feed/Reply.vue";
import FeedItemReactions from "@/components/ui/feed/Reactions.vue";
import FeedItemReactionsMenu from "@/components/ui/feed/ReactionsMenu.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    PlusSmIcon,
    TrashIcon,
    ShieldCheckIcon,
    FolderDownloadIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    CloudDownloadIcon,
    FeedItemHeader,
    FeedItemTimestamp,
    FeedItemAttachement,
    FeedItemBody,
    FeedItemActions,
    FeedItemReactions,
    FeedItemReactionsMenu,
    FeedItemReply
  },

  mixins: [i18n],

  data() {
    return {

      message: null,

      // Routes
      routes: {
        fetch: '/api/message',
        destroy: '/api/message',
        translate: '/api/translate'
      },

      // States
      isLoaded: false,
      hasTruncateFiles: false,
      hideMessageBody: false,
    };
  },

  props: {
    item: {
      type: Object,
      default: null
    },

    filesOnly: {
      type: Boolean,
      default: false,
    },

    projectState: null,
  },

  mounted() {
    this.message = this.$props.item;
    this.hasTruncateFiles = this.message.files.length > 3 ? true : false;
    this.isLoaded = true;
    this.hideMessageBody = this.$props.filesOnly;
  },

  methods: {

    fetch() {
      this.axios.get(`${this.routes.fetch}/${this.message.uuid}`).then(response => {
        this.message = response.data;
      });
    },

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
      this.hasTruncateFiles = this.hasTruncateFiles ? false : true;
    },

    toggleMessageBody(itemUuid) {
      this.hideMessageBody = this.hideMessageBody ? false : true;
    },

    deeplFy(language) {
      NProgress.start();
      this.axios.post(this.routes.translate, {message: this.message.body, language: language}).then(response => {
        this.message.body = response.data.message;
        NProgress.done();
      });
    },
  },

  watch: {
    filesOnly() {
      this.hideMessageBody = this.$props.filesOnly;
    },
    item() {
      this.message = this.$props.item;
    },
    message() {
    }
  }
}
</script>
<style>
.feed-item {
  @apply flex justify-end  mb-8 lg:mb-10
}

.feed-item.is-internal,
.feed-item.is-private {
  @apply flex justify-start
}

/* .feed-item.is-private a,
.feed-item.is-private strong {
  @apply text-slate-100
}

.feed-item.is-private .feed-item__body > div {
  @apply !text-slate-100
} */

.feed-item ul {
  @apply mb-4
}

.feed-item li {
  @apply !mt-1 !mb-1
}

.feed-item p {
  @apply !mt-0 !text-sm
}

.feed-item p:last-of-type {
  @apply !mb-0
}
</style>