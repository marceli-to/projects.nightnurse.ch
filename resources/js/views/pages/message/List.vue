<template>
<div v-if="isFetched">
  <header class="mb-4 sm:mb-8 lg:mb-9 pt-4 sm:pt-5 lg:pt-7 pb-2 sm:pb-4 flex items-start sm:items-start sticky top-0 bg-white z-50 border-bottom sm:max-w-4xl">
    <div>
      <div class="text-2xl font-bold mb-2 sm:mb-3 flex items-center">
        <span class="text-dark" v-if="project.company">{{project.company.name}} – {{project.number}} {{project.name}}</span>
        <span class="text-dark" v-else>{{project.number}} {{project.name}}</span>
      </div>
      <div class="flex">
        <div class="text-gray-400 mr-4 sm:mr-6 lg:mr-10">
          <div class="text-xs font-mono pb-0.5">Projektleiter</div>
          <div class="text-sm text-dark">{{project.manager.full_name}}</div>
        </div>
        <div class="text-gray-400 mr-4 sm:mr-6 lg:mr-10">
          <div class="text-xs font-mono pb-0.5">Projektstart</div>
          <div class="text-sm text-dark">{{project.date_start}}</div>
        </div>
        <div class="text-gray-400 mr-4 sm:mr-6 lg:mr-10">
          <div class="text-xs font-mono pb-0.5">Abgabetermin</div>
          <div class="text-sm text-dark">{{project.date_end}}</div>
        </div>
      </div>
    </div>
  </header>
  <feed>
    <div v-for="(items, day) in feedItems" :key="day" class="relative">

      <feed-item-timestamp>{{ day }}</feed-item-timestamp>
      
      <feed-item 
        v-for="(message, index) in items" 
        :key="index" 
        :item="message" 
        :class="getStateClasses(message)">
        
        <div v-if="!message.deleted_at" class="relative">
          <shield-check-icon class="icon-card absolute right-1 top-1" aria-hidden="true" v-if="message.private" />

          <feed-item-header :class="[message.private ? 'text-slate-100': '', 'relative']">
            <span class="font-bold" v-if="message.sender">{{message.sender.full_name}}</span>
            <span v-else>[deleted user]</span>
            an 
            <span v-if="message.users.length == 1">{{message.users[0].full_name}}</span>
            <span v-else class="has-tooltip">
              <div class='tooltip'>
                <span v-for="(user, idx) in message.users" :key="user.uuid">
                  {{user.full_name}}<span v-if="idx < message.users.length-1">,</span>
                </span>
              </div>
              <a href="javascript:;" 
                class="underline underline-offset-4 text-gray-400 decoration-dotted">
                {{ message.users.length }} Empfänger
              </a>
            </span>
            um {{message.message_time}} Uhr
          </feed-item-header>

          <feed-item-body v-if="message.subject || message.body">
            <div :class="[message.body ? 'font-bold' : '', 'text-sm']">{{ message.subject }}</div>
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

            <span class="flex items-center justify-between text-xs font-mono pb-1 pt-4">
              <a 
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private ? 'text-slate-100' : 'text-gray-400', 'flex items-center no-underline hover:underline']"
                v-if="message.truncate_files">
                <chevron-down-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">Mehr anzeigen ({{message.files.length - 3}})</span>
              </a>
              <a
                href="javascript:;" 
                @click="toggle(message.uuid)"
                :class="[message.private ? 'text-slate-100' : 'text-gray-400', 'flex items-center no-underline hover:underline']"
                v-else-if="message.files.length > 3">
                <chevron-up-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">Weniger anzeigen</span>
              </a>
              <a 
                :href="`/download/zip/${message.uuid}`" 
                target="_blank" 
                :class="[message.private ? 'text-white' : 'text-gray-400', 'flex items-center no-underline hover:underline']">
                <folder-download-icon class="h-5 w-5" aria-hidden="true" />
                <span class="inline-block ml-2">Download als ZIP</span>
              </a>
            </span>

          </div>

        </div>

        <div v-else>
          <feed-item-body>
            <div class="text-xs text-gray-400 font-mono italic sm:pt-1">
              Nachricht gelöscht durch {{message.sender.full_name}}
            </div>
          </feed-item-body>
        </div>

        <a href="javascript:;" 
          @click.prevent="destroy(message.uuid)" 
          class="feed-item-delete" 
          v-if="message.can_delete && !message.deleted_at">
          Nachricht löschen
        </a>
      </feed-item>

    </div>
  </feed>
  <content-footer>
    <router-link :to="{ name: 'message-create' }" class="btn-create">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
      <span class="block ml-2">Erstellen</span>
    </router-link>
    <router-link :to="{ name: 'projects' }" class="form-helper form-helper-footer">
      <span>Zurück</span>
    </router-link>
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
  ChevronUpIcon

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
import FileType from "@/components/ui/misc/FileType.vue";

export default {

  components: {
    PlusCircleIcon,
    TrashIcon,
    PencilAltIcon,
    ShieldCheckIcon,
    CloudDownloadIcon,
    FolderDownloadIcon,
    ChevronUpIcon,
    ChevronDownIcon,
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
    FileType
  },

  mixins: [ErrorHandling, Helpers],

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

      // Messages
      messages: {
        emptyData: 'Es sind noch keine Nachrichten vorhanden...',
        confirmDestroy: 'Bitte löschen bestätigen!',
      }
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.isFetched = false;
      this.isLoading = true;
      this.axios.all([
        this.axios.get(`${this.routes.list}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.project}/${this.$route.params.uuid}`),
      ]).then(axios.spread((...responses) => {
        this.feedItems = responses[0].data.data ? responses[0].data.data : responses[0].data;
        this.project = responses[1].data;
        this.isFetched = true;
        this.isLoading = false;
      }));
    },

    destroy(uuid, event) {
      if (confirm(this.messages.confirmDestroy)) {
        this.isLoading = true;
        this.axios.delete(`${this.routes.destroy}/${uuid}`).then(response => {
          this.fetch();
          this.isLoading = false;
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
  },

  computed: {
    title() {
      return `${this.project.number} – <span class="text-highlight">${this.project.name}</span>`;
    },
  }
}
</script>