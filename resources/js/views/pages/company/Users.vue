<template>
  <div v-if="isFetched">

    <content-header>
      <template #title>
        {{ translate('Alle Benutzer') }}
      </template>
    </content-header>

    <list v-if="data.length">
      <list-item v-for="d in data" :key="d.uuid">
        <div class="flex items-center">
          <div v-if="d.register_complete">
            {{ d.name }}, {{d.firstname}}
            <!-- <span class="hidden sm:inline"><separator />{{ d.email }}</span> -->
            <span class="inline"><separator />{{ d.company.name }}</span>
            <!-- <pill v-if="d.role_id == 1">{{ translate('Admin') }}</pill>
            <pill v-if="d.role_id == 3" class="bg-green-500">{{ translate('Admin') }}</pill>
            <pill v-if="d.team_id" class="bg-blue-500">{{ getTeam(d.id) }}</pill> -->
          </div>
          <div v-else>
            {{ d.email }}
            <pill v-if="d.register_complete == 0" class="bg-yellow-500">{{ translate('Pendent') }}</pill>
          </div>
        </div>
        <list-action>
          <router-link :to="{name: 'user-update', params: { companyUuid: d.company.uuid, uuid: d.uuid, redirect: '/companies/users' }}">
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

    <content-footer>
      <router-link :to="{ name: 'companies'}" class="btn-primary">
        <span>{{ translate('Zurück') }}</span>
      </router-link>
    </content-footer>

  </div>
</template>
<script>
import { PlusCircleIcon, PlusSmIcon, PencilAltIcon, UsersIcon, TrashIcon, MailIcon, LinkIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import PageTitle from "@/mixins/PageTitle";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
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
    MailIcon,
    LinkIcon,
    ContentHeader,
    ContentFooter,
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
        list: '/api/users',
        destroy: '/api/user',
      },

      // States
      isLoading: false,
      isFetched: false,
    };
  },

  created() {
    this.fetch();
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

    destroy(uuid, event) {
      if (confirm(this.translate('Bitte löschen bestätigen'))) {
        NProgress.start();
        this.axios.delete(`${this.routes.destroy}/${uuid}`).then(response => {
          this.fetch();
          NProgress.done();
        });
      }
    },

    getTeam(userId) {
      const user = this.data.find(x => x.id === userId);
      if (user) {
        const team = user.company.teams.find(x => x.id === user.team_id);
        return team.description
      }
    }
  },

}
</script>