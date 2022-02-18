<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'message-create' }" class="btn-icon">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>
  <feed>
    <feed-item v-for="d in data" :key="d.uuid">
      <feed-item-timestamp>{{ d.feed_date }}</feed-item-timestamp>
      <feed-item-sender>
        Nachricht von <span class="font-bold">{{d.sender.short_name}}</span>
      </feed-item-sender>
      <feed-item-body>
        <div class="text-sm font-bold">{{ d.subject }}</div>
        <div class="text-sm" v-html="d.body"></div>
      </feed-item-body>
    </feed-item>
  </feed>
</div>
</template>
<script>
import { PlusCircleIcon, TrashIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
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
    ContentHeader,
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
        this.data = responses[0].data.data;
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
      return `<span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    }
  }
}
</script>