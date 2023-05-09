<template>
<div v-if="isFetched">
  <content-header>
    <template #icon>
      <router-link :to="{ name: 'notification-create' }" class="btn-add">
        <plus-sm-icon class="h-5 w-5" aria-hidden="true" />
      </router-link>
    </template>
    <template #title>
      {{ translate('Notifications') }}
    </template>
  </content-header>
  <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        <span v-if="d.title">{{ d.title }}</span>
        <span v-else>{{ d.text | truncate(75, '...') }}</span>
        <pill v-if="d.publish" class="bg-green-500">{{ translate('aktiv') }}</pill>
        <pill v-else class="bg-gray-300">{{ translate('inaktiv') }}</pill>
      </div>
      <list-action>
        <router-link :to="{name: 'notification-update', params: { id: d.id }}">
          <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <a href="" @click.prevent="destroy(d.id)">
          <trash-icon class="icon-list" aria-hidden="true" />
        </a>
      </list-action>
    </list-item>
  </list>
  <list-empty v-else>
    {{ translate('Es sind noch keine Daten vorhanden') }}
  </list-empty>
</div>
</template>
<script>
import { PlusCircleIcon, PlusSmIcon, PencilAltIcon, TrashIcon, UsersIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import PageTitle from "@/mixins/PageTitle";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
import List from "@/components/ui/layout/List.vue";
import ListItem from "@/components/ui/layout/ListItem.vue";
import ListAction from "@/components/ui/layout/ListAction.vue";
import ListEmpty from "@/components/ui/layout/ListEmpty.vue";
import Pill from "@/components/ui/misc/Pill.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    PlusSmIcon,
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
    Pill,
    NProgress
  },

  mixins: [ErrorHandling, Helpers, PageTitle, i18n],

  data() {
    return {

      // Data
      data: [],

      // Routes
      routes: {
        list: '/api/notifications',
        toggle: '/api/notification/state',
        destroy: '/api/notification'
      },

      // States
      isLoading: false,
      isFetched: false,
    };
  },

  created() {
    this.fetch();
    this.setPageTitle('Notifications');
  },

  methods: {

    fetch() {
      NProgress.start();
      this.axios.get(this.routes.list).then(response => {
        this.data = response.data.data;
        this.isFetched = true;
        NProgress.done();
      });
    },

    toggle(id,event) {
      NProgress.start();
      this.axios.get(`${this.routes.toggle}/${id}`).then(response => {
        const index = this.data.findIndex(x => x.id === id);
        this.data[index].publish = response.data;
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
    },

    destroy(id, event) {
      if (confirm(this.translate('Bitte löschen bestätigen'))) {
        NProgress.start();
        this.axios.delete(`${this.routes.destroy}/${id}`).then(response => {
          this.fetch();
          NProgress.done();
        });
      }
    },
  },
}
</script>