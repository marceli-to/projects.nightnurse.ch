<template>
  <div v-if="isFetched">

    <content-header>
      <template #title>
        {{ translate('Alle Benutzer') }}
      </template>
    </content-header>

    <div class="flex justify-start w-full mb-2 sm:mb-4 lg:mb-6">
      <a href="javascript:;" @click.prevent="switchView()" class="text-xs font-mono text-gray-400 flex justify-end items-center no-underline hover:text-highlight">
        <template v-if="isGrouped">
          <view-list-icon class="h-4 w-4" aria-hidden="true" />
          <span class="block ml-2">{{ translate('Einfache Liste') }}</span>
        </template>
        <template v-else>
          <view-grid-icon class="h-4 w-4" aria-hidden="true" />
          <span class="block ml-2">{{ translate('Gruppiert nach Firmen') }}</span>
        </template>
      </a>
    </div>

    <template v-if="isGrouped">
      <div v-for="(group, index) in data" :key="index">
        <h4>{{ group.length && group[0].company ? group[0].company.name : null }}</h4>
        <list v-if="group.length">
          <list-item v-for="d in group" :key="d.uuid">
            <div class="flex items-center">
              <div v-if="d.register_complete">
                {{ d.name }}, {{d.firstname}}
                <span class="hidden sm:inline"><separator />{{ d.email }}</span>
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
      </div>
    </template>
    <template v-else>
      <list v-if="data.length">
        <list-item v-for="d in data" :key="d.uuid">
          <div class="flex items-center">
            <div v-if="d.register_complete">
              {{ d.name }}, {{d.firstname}}
              <span class="hidden sm:inline"><separator />{{ d.email }}</span>
              <span class="inline"><separator />{{ d.company.name }}</span>
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
    </template>

    <content-footer>
      <router-link :to="{ name: 'companies'}" class="btn-primary">
        <span>{{ translate('Zurück') }}</span>
      </router-link>
    </content-footer>

  </div>
</template>
<script>
import { PlusCircleIcon, PlusSmIcon, PencilAltIcon, UsersIcon, TrashIcon, MailIcon, LinkIcon, ViewGridIcon, ViewListIcon } from "@vue-hero-icons/outline";
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
    ViewGridIcon,
    ViewListIcon,
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
        list: '/api/users/all',
        destroy: '/api/user',
      },

      // States
      isLoading: false,
      isFetched: false,
      isGrouped: false,
    };
  },

  created() {
    this.fetch();
    this.isGrouped = this.$store.state.userListIsGrouped ? true : false;
  },

  methods: {

    fetch() {
      NProgress.start();
      const uri = this.isGrouped || this.$store.state.userListIsGrouped ? `${this.routes.list}/grouped` : this.routes.list;
      this.axios.get(uri).then(response => {
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
    },

    switchView() {
      this.isGrouped = !this.isGrouped;
      this.$store.commit('userListIsGrouped', this.isGrouped ? true : false);
      this.fetch();
    }
  },
}
</script>