<template>
<div v-if="isFetched">
  <content-header>
    <template #icon>
      <router-link :to="{ name: 'company-create' }" class="btn-add">
        <plus-sm-icon class="h-5 w-5" aria-hidden="true" />
      </router-link>
    </template>
    <template #title>
      {{ translate('Kunden') }}
    </template>
  </content-header>

  <div class="flex justify-start w-full mb-2 sm:mb-4 lg:mb-6">
    <router-link :to="{ name: 'companies-users' }" class="text-xs font-mono text-gray-400 flex justify-end items-center no-underline hover:text-highlight">
      <users-icon class="h-4 w-4" aria-hidden="true" />
      <span class="block ml-2">{{ translate('Liste aller Benutzer') }}</span>
    </router-link>
  </div>

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
        list: '/api/companies',
        toggle: '/api/company/state',
        destroy: '/api/company'
      },

      // States
      isLoading: false,
      isFetched: false,
    };
  },

  created() {
    this.fetch();
    this.setPageTitle('Kunden');
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

    toggle(uuid,event) {
      NProgress.start();
      this.axios.get(`${this.routes.toggle}/${uuid}`).then(response => {
        const index = this.data.findIndex(x => x.uuid === uuid);
        this.data[index].publish = response.data;
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
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
  },

}
</script>