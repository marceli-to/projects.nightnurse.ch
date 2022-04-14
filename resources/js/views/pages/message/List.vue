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
    <div v-for="(entries, day) in data" :key="day" class="relative">
      <feed-item-timestamp>{{ day }}</feed-item-timestamp>
      <feed-item v-for="(message, index) in entries" :key="index" :item="message" :class="getStateClasses(message)">
        <div v-if="!message.deleted_at" class="relative">
          <shield-check-icon class="icon-card absolute right-1 top-1" aria-hidden="true" v-if="message.private" />
          <feed-item-header :class="[message.private ? 'text-slate-100': '']">
            Nachricht von 
            <span class="font-bold" v-if="message.sender">{{message.sender.full_name}}</span>
            <span v-else>[deleted user]</span>
            um {{message.message_time}} an 
            <span v-if="message.users.length == 1">{{message.users[0].full_name}}</span>
            <span v-else class="has-tooltip">
              <div class='tooltip'>
                <span v-for="(user, idx) in message.users" :key="user.uuid">
                  {{user.full_name}}<span v-if="idx < message.users.length-1">,</span>
                </span>
              </div>
              <a href="javascript:;" class="underline underline-offset-4 text-gray-400 decoration-dotted">{{ message.users.length }} Empfänger</a>
            </span>
          </feed-item-header>
          <feed-item-body v-if="message.subject || message.body">
            <div :class="[message.body ? 'font-bold' : '', 'text-sm']">{{ message.subject }}</div>
            <div class="text-sm" v-html="message.body"></div>
          </feed-item-body>
          <div v-if="message.files" :class="[message.subject || message.body ? 'mt-2 lg:mt-4' : 'mt-1 lg:mt-2']">
            <div v-for="file in message.files" :key="file.uuid" class="first:border-t-2 border-b-2 border-gray-100 py-2 lg:py-3 last:border-b-0">
              <a :href="`/img/original/${file.name}`" target="_blank" class="flex items-center no-underline hover:text-highlight" v-if="file.preview">
                <img 
                :src="`/img/thumbnail/${file.name}`" 
                height="100" 
                width="100"
                class="!mt-0 !mb-0 mr-2 sm:mr-3 lg:mr-4 block h-auto max-w-[50px] bg-light rounded-sm"
                v-if="file.preview" />
                <div class="font-mono text-xs">
                  <div class="mb-1">{{ file.original_name | truncate(50, '...') }}</div>
                  <div>{{ file.size | filesize(file.size) }}</div>
                </div>
              </a>
              <a :href="`/storage/uploads/${file.name}`" target="_blank" class="flex items-center no-underline hover:text-highlight" v-else>
                <div class="font-mono text-xs">
                  <div class="mb-1">{{ file.original_name | truncate(50, '...') }}</div>
                  <div>{{ file.size | filesize(file.size) }}</div>
                </div>
              </a>
            </div>
          </div>
        </div>
        <div v-else>
          <feed-item-body>
            <div class="text-xs text-gray-400 font-mono italic sm:pt-1">Nachricht gelöscht durch {{message.sender.full_name}}</div>
          </feed-item-body>
        </div>
        <a href="javascript:;" @click.prevent="destroy(message.uuid)" class="feed-item-delete" v-if="message.can_delete && !message.deleted_at">Nachricht löschen</a>
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
import { PlusCircleIcon, PencilAltIcon, TrashIcon, ShieldCheckIcon } from "@vue-hero-icons/outline";
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
import FeedItemBody from "@/components/ui/feed/Body.vue";

export default {

  components: {
    PlusCircleIcon,
    TrashIcon,
    PencilAltIcon,
    ShieldCheckIcon,
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
    FeedItemBody
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {

      // Data
      data: [],

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
        this.data = responses[0].data.data ? responses[0].data.data : responses[0].data;
        this.project = responses[1].data;
        this.isFetched = true;
        this.isLoading = false;
        console.log(this.data);
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

    getStateClasses(item) {
      let cls = '';
      if (item.can_delete && !item.deleted_at) {
        cls += 'has-delete ';
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