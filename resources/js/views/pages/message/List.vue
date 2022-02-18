<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'message-create' }" class="btn-icon">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>

  <feed>
    <feed-item v-for="d in data" :key="d.uuid">
      <div>{{ d.created_at }}</div>
      <div class="mb-1">{{d.sender.short_name}}</div>
      {{ d.subject }}
    </feed-item>
  </feed>

  <!-- <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        {{ d.created_at }}<separator />{{d.sender.short_name}}<separator />{{ d.subject }}
      </div>
      <list-action>
        <a href="" @click.prevent="destroy(d.uuid)">
          <trash-icon class="icon-list" aria-hidden="true" />
        </a>
      </list-action>
    </list-item>
  </list>
  <list-empty v-else>
    {{messages.emptyData}}
  </list-empty> -->
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
    FeedItem
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