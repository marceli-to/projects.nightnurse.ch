<template>
<div v-if="isFetched" class="max-w-5xl">

  <header class="mb-4 pt-2 sm:pt-3 pb-2 sm:pb-4 flex items-start sm:items-start sticky top-0 bg-white z-40 border-bottom relative -ml-[1px] pl-[1px]">
    <div>
      <div class="text-xl lg:text-2xl font-bold mb-2 sm:mb-3 flex items-end sm:max-w-4xl leading-snug sm:leading-normal">
        <div class="text-dark" v-if="project.company">
          <div class="font-normal text-sm">
            {{project.company.name}}
          </div>
          <span :style="`color: ${project.color}`">{{project.number}} – {{project.name}}</span>
        </div>
        <div class="text-dark" v-else>
          {{project.number}} {{project.name}}
        </div>
        <router-link :to="{name: 'project-update', params: { uuid: project.uuid, redirect: 'messages' }}" v-if="$store.state.user.admin">
          <pencil-alt-icon class="icon-list mb-1 ml-1 sm:ml-2" aria-hidden="true" />
        </router-link>
      </div>
      <div class="flex">
        <div class="text-gray-400 mr-4 sm:mr-6 lg:mr-10">
          <div class="text-xs font-mono pb-0.5">{{ translate('Projektleiter') }}</div>
          <div class="text-sm text-dark">
            {{project.manager.full_name}}
            <a :href="`tel:${project.manager.phone}`" class="flex items-center text-dark hover:text-highlight no-underline" v-if="project.manager.phone">
              <phone-icon class="w-4 h-4 mr-1" /> {{project.manager.phone}}
            </a>
          </div>
        </div>
        <div class="text-gray-400 mr-4 sm:mr-6 lg:mr-10">
          <div class="text-xs font-mono pb-0.5">{{ translate('Projektstart') }}</div>
          <div class="text-sm text-dark">{{project.date_start}}</div>
        </div>
        <div class="text-gray-400 mr-4 sm:mr-6 lg:mr-10">
          <div class="text-xs font-mono pb-0.5">{{ translate('Abgabetermin') }}</div>
          <div class="text-sm text-dark">{{project.date_end}}</div>
        </div>
      </div>
    </div>
  </header>

  <message-form ref="messageForm" @cancelMessage="hideForm()"></message-form>
  
  <div class="flex justify-end">
    <a href="javascript:;" 
      class="text-xs font-mono text-gray-400 flex items-center no-underline hover:text-highlight"
      @click="switchViewType()">
      <switch-horizontal-icon class="h-4 w-4" aria-hidden="true" />
      <span class="block ml-2" v-if="viewType == 'standard'">{{ translate('Anhänge Filtern') }}</span>
      <span class="block ml-2" v-else>{{ translate('Alle Nachrichten') }}</span>
    </a>
  </div>

  <feed>

    <div v-for="(items, day) in feedItems" :key="day" class="relative">

      <feed-item-timestamp>{{ day }}</feed-item-timestamp>
      
      <feed-item 
        v-for="(message, index) in items" 
        :key="index" 
        :item="message" 
        :class="getStateClasses(message)"
        :data-has-files="message.files.length > 0 && !message.deleted_at ? true : false">
        
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
              v-if="viewType == 'files-only'">
              {{ message.body | truncate(25, '...') }}
            </a>
            <div :class="[message.body ? 'font-bold' : '', 'text-sm']" v-if="message.subject">{{ message.subject }}</div>
            <div class="text-sm" v-html="message.body"></div>
          </feed-item-body>

          <div v-if="message.files.length > 0" :class="[message.subject || message.body ? 'mt-2 lg:mt-4' : 'mt-1 lg:mt-3']">
            
            <feed-item-attachement 
              v-for="(file, idx) in message.files"
              :key="file.uuid"
              :index="idx"
              :file="file"
              :truncate="message.truncate_files"
              :private="message.private">
            </feed-item-attachement>

            <span class="sm:flex sm:items-center justify-between text-xs font-mono pb-1 pt-4">
              <a 
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private ? 'text-slate-100' : 'text-gray-400', 'flex items-center no-underline hover:underline mt-3 sm:mt-0']"
                v-if="message.truncate_files">
                <chevron-down-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">{{ translate('Mehr anzeigen') }} ({{message.files.length - 3}})</span>
              </a>
              <a
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private ? 'text-slate-100' : 'text-gray-400', 'flex items-center no-underline hover:underline mt-3 sm:mt-0']"
                v-else-if="message.files.length > 3">
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
      </feed-item>

    </div>
  </feed>
  <content-footer v-if="!hasForm">
    <router-link :to="{ name: 'projects' }" class="form-helper form-helper-footer">
      <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
      <span>{{ translate('Zurück') }}</span>
    </router-link>
    <a href="javascript:;" @click="toggleForm()" class="btn-create">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
      <span class="block ml-2">{{ translate('Neue Nachricht') }}</span>
    </a>
  </content-footer>
</div>
</template>

<script>

import { 
  PlusCircleIcon, 
  PencilAltIcon, 
  TrashIcon, 
  ShieldCheckIcon, 
  CloudDownloadIcon, 
  FolderDownloadIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  ChevronUpIcon,
  ArrowLeftIcon,
  PhoneIcon,
  SwitchHorizontalIcon,
} 
from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import List from "@/components/ui/layout/List.vue";
import ListItem from "@/components/ui/layout/ListItem.vue";
import ListAction from "@/components/ui/layout/ListAction.vue";
import ListEmpty from "@/components/ui/layout/ListEmpty.vue";
import Feed from "@/components/ui/feed/Feed.vue";
import FeedItem from "@/components/ui/feed/Item.vue";
import FeedItemHeader from "@/components/ui/feed/Header.vue";
import FeedItemTimestamp from "@/components/ui/feed/TimeStamp.vue";
import FeedItemAttachement from "@/components/ui/feed/Attachement.vue";
import FeedItemBody from "@/components/ui/feed/Body.vue";
import MessageForm from "@/views/pages/message/partials/Form.vue";
import FileType from "@/components/ui/misc/FileType.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    PhoneIcon,
    PlusCircleIcon,
    TrashIcon,
    PencilAltIcon,
    ShieldCheckIcon,
    CloudDownloadIcon,
    FolderDownloadIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    SwitchHorizontalIcon,
    ArrowLeftIcon,
    ContentHeader,
    ContentFooter,
    Separator,
    List,
    ListItem,
    ListAction,
    ListEmpty,
    Feed,
    FeedItem,
    FeedItemHeader,
    FeedItemTimestamp,
    FeedItemAttachement,
    FeedItemBody,
    FileType,
    MessageForm,
    NProgress
  },

  mixins: [ErrorHandling, Helpers, i18n],

  data() {
    return {

      // Data
      feedItems: [],

      // Project data
      project: [],

      // Routes
      routes: {
        list: '/api/messages',
        destroy: '/api/message',
        project: '/api/project'
      },

      // States
      isLoading: false,
      isFetched: false,
      hasForm: false,
      viewType: 'standard',

    };
  },

  created() {
    this.fetch();

    // Listen to channel 'timeline' an push new messages to the top
    window.Echo.private('timeline').listen('MessageSent', (e) => {
      this.feedItems['Today'].unshift(e.message);
      if (!('Notification' in window)) {
        console.log('Web Notification is not supported');
        return;
      }
      Notification.requestPermission(permission => {
        let notification = new Notification('Projekte Nightnurse', {
          body: 'Neue Nachricht...',
          icon: 'https://staging.projects.nightnurse.ch/notification.png'
        });
        notification.onclick = () => {
          window.open(window.location.href);
        };
      });
    });
  },

  methods: {

    fetch() {
      this.isFetched = false;
      NProgress.start();
      this.axios.all([
        this.axios.get(`${this.routes.list}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.project}/${this.$route.params.uuid}`),
      ]).then(axios.spread((...responses) => {
        this.feedItems = responses[0].data.data ? responses[0].data.data : responses[0].data;
        this.project = responses[1].data;
        this.isFetched = true;
        NProgress.done();
      }));
    },

    destroy(uuid, event) {
      if (confirm(this.translate('Bitte löschen bestätigen'))) {
        NProgress.start();
        this.axios.delete(`${this.routes.destroy}/${uuid}`).then(response => {
          this.fetch();
          NProgress.done();
        });
      }
    },

    toggle(uuid) {
      Object.keys(this.feedItems).forEach(key => {
        const index = this.feedItems[key].findIndex(x => x.uuid === uuid);
        if (index > -1) {
          this.feedItems[key][index].truncate_files = this.feedItems[key][index].truncate_files ? false : true;
        }
      });
    },

    switchViewType() {
      this.viewType = this.viewType == 'standard' ? 'files-only' : 'standard';
    },

    getStateClasses(item) {
      let cls = '';
      if (item.can_delete && !item.deleted_at) {
        cls += 'can-delete ';
      }
      if (item.private) {
        cls += 'is-private'
      }
      return cls;
    },

    toggleForm() {
      this.$refs.messageForm.toggle();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      this.hasForm = this.hasForm ? false : true;
    },

    hideForm() {
      this.hasForm = false;
    },

    toggleMessageBody(itemUuid) {
      const item = document.querySelector('[data-item-uuid="'+itemUuid+'"]');
      item.classList.toggle('is-truncated');
    }
  },

  watch: {
    feedItems() { },

    viewType() {

      if (this.viewType == 'files-only') {
        const items = document.querySelectorAll('.feed-item');
        items.forEach((element) => {
          if (!element.dataset || !element.dataset.hasFiles) {
            element.style.display = 'none';
          }
          else {
            const el = element.querySelector('.feed-item__body');
            el.classList.add('is-truncated');
          }
        });
      }
      else {
        const items = document.querySelectorAll('.feed-item');
        items.forEach((element) => {
          if (!element.dataset || !element.dataset.hasFiles) {
            element.style.display = 'block';
          }
          else {
            const el = element.querySelector('.feed-item__body');
            el.classList.remove('is-truncated');
          }
        });
      }
    }
  }
}
</script>