<template>
<div v-if="isFetched">
  <header class="mb-4 sm:mb-8 lg:mb-9 pt-4 sm:pt-5 lg:pt-7 pb-2 sm:pb-4 flex items-start sm:items-start sticky top-0 bg-white z-50 border-bottom sm:max-w-4xl">
    <div>
      <div class="text-2xl font-bold mb-2 sm:mb-3 flex items-center">
        <span class="text-dark">{{project.company.name}} – {{project.number}} {{project.name}}</span>
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
    <feed-item v-for="d in data" :key="d.uuid" :internal="d.internal">
      <feed-item-timestamp>{{ d.feed_date }}</feed-item-timestamp>
      <div v-if="!d.deleted_at">
        <feed-item-sender>
          Nachricht von 
          <span class="font-bold" v-if="d.sender">{{d.sender.short_name}}</span>
          <span v-else>[deleted user]</span>
        </feed-item-sender>
        <feed-item-body v-if="d.subject || d.body">
          <div :class="[d.body ? 'font-bold' : '', 'text-sm']">{{ d.subject }}</div>
          <div class="text-sm" v-html="d.body"></div>
        </feed-item-body>
        <div v-if="d.files" :class="[d.subject || d.body ? 'mt-2 lg:mt-4' : 'mt-1 lg:mt-2']">
          <div v-for="file in d.files" :key="file.uuid" class="first:border-t-2 border-b-2 border-gray-100 py-2 lg:py-3 last:border-b-0">
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
            <a :href="`/storage/uploads/${file.name}`" class="flex items-center no-underline hover:text-highlight" v-else>
              <div class="font-mono text-xs">
                <div class="mb-1">{{ file.original_name | truncate(50, '...') }}</div>
                <div>{{ file.size | filesize(file.size) }}</div>
              </div>
            </a>
          </div>
        </div>
        <a href="javascript:;" @click.prevent="destroy(d.uuid)" class="feed-item-delete" v-if="d.can_delete">Löschen</a>
      </div>
      <div v-else>
        <feed-item-body>
          <div class="text-xs text-gray-400 font-mono italic sm:pt-1">Nachricht wurde gelöscht</div>
        </feed-item-body>
      </div>
    </feed-item>
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
import { PlusCircleIcon, PencilAltIcon, TrashIcon } from "@vue-hero-icons/outline";
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
import FeedItemSender from "@/components/ui/feed/Sender.vue";
import FeedItemTimestamp from "@/components/ui/feed/TimeStamp.vue";
import FeedItemBody from "@/components/ui/feed/Body.vue";

export default {

  components: {
    PlusCircleIcon,
    TrashIcon,
    PencilAltIcon,
    ContentHeader,
    ContentFooter,
    Separator,
    List,
    ListItem,
    ListAction,
    ListEmpty,
    Feed,
    FeedItem,
    FeedItemSender,
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
  },

  computed: {
    title() {
      return `${this.project.number} – <span class="text-highlight">${this.project.name}</span>`;
    }
  }
}
</script>