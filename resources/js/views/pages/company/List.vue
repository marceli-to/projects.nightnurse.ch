<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'company-create' }" class="btn-icon">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>
  <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        {{d.name}}<separator />{{ d.city }}
        <pill v-if="d.owner">Owner</pill>
      </div>
      <list-action>
        <router-link :to="{name: 'users', params: { companyUuid: d.uuid }}">
          <users-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <router-link :to="{name: 'company-update', params: { uuid: d.uuid }}">
          <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <a href="" @click.prevent="destroy(d.uuid)">
          <trash-icon class="icon-list" aria-hidden="true" />
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
import { PlusCircleIcon, PencilAltIcon, TrashIcon, UsersIcon } from "@vue-hero-icons/outline";
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
    UsersIcon,
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
        list: '/api/companies',
        toggle: '/api/company/state',
        destroy: '/api/company'
      },

      // States
      isLoading: false,
      isFetched: false,

      // Messages
      messages: {
        emptyData: 'Es sind noch keine Firmen vorhanden...',
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
      return "Kunden";
    }
  }
}
</script>