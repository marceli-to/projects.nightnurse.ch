<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'user-create' }" class="btn-icon">
      <PlusCircleIcon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>
  <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        {{d.firstname}} {{ d.name }}
        <span class="hidden sm:inline"><separator />{{ d.email }}</span>
        <separator />{{ d.company.name }}
        <pill v-if="d.role_id == 1">Admin</pill>
      </div>
      <list-action>
        <router-link :to="{name: 'user-update', params: { uuid: d.uuid }}">
          <PencilAltIcon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <a href="" @click.prevent="destroy(d.uuid)">
          <TrashIcon class="icon-list" aria-hidden="true" />
        </a>
      </list-action>
    </list-item>
  </list>
  <list-empty v-else>
    {{messages.emptyData}}
  </list-empty>
</div>
</template>
<script>
import { PlusCircleIcon, PencilAltIcon, TrashIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
import List from "@/components/ui/layout/List.vue";
import ListItem from "@/components/ui/layout/ListItem.vue";
import ListAction from "@/components/ui/layout/ListAction.vue";
import ListEmpty from "@/components/ui/layout/ListEmpty.vue";
import Pill from "@/components/ui/misc/Pill.vue";

export default {

  components: {
    PlusCircleIcon,
    PencilAltIcon,
    TrashIcon,
    ContentHeader,
    Separator,
    List,
    ListItem,
    ListAction,
    ListEmpty,
    Pill
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {

      // Data
      data: [],

      // Routes
      routes: {
        list: '/api/users',
        toggle: '/api/user/state',
        destroy: '/api/user'
      },

      // States
      isLoading: false,
      isFetched: false,

      // Messages
      messages: {
        emptyData: 'Es sind noch keine Texte vorhanden...',
        confirmDestroy: 'Bitte löschen bestätigen!',
        updated: 'Status geändert',
      }
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(this.routes.list).then(response => {
        this.data = response.data.data;
        this.isFetched = true;
      });
    },

    toggle(uuid,event) {
      this.isLoading = true;
      this.axios.get(`${this.routes.toggle}/${uuid}`).then(response => {
        const index = this.data.findIndex(x => x.uuid === uuid);
        this.data[index].publish = response.data;
        this.$notify({ type: "success", text: this.messages.updated });
        this.isLoading = false;
      });
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
      return "Benutzer";
    }
  }
}
</script>