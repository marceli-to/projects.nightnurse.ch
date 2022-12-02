<template>
<div v-if="isFetched">
  <content-header>
    <template #icon>
      <router-link :to="{ name: 'user-register', params: {companyUuid: $route.params.companyUuid} }" class="btn-add">
        <plus-sm-icon class="h-5 w-5" aria-hidden="true" />
      </router-link>
    </template>
    <template #title>
      {{ title }}
    </template>
  </content-header>

  <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        <div v-if="d.register_complete">
          {{d.firstname}} {{ d.name }}
          <span class="hidden sm:inline"><separator />{{ d.email }}</span>
          <pill v-if="d.role_id == 1">{{ translate('Admin') }}</pill>
          <pill v-if="d.team_id" class="bg-blue-500">{{ getTeam(d.id) }}</pill>
        </div>
        <div v-else>
          {{ d.email }}
          <pill v-if="d.register_complete == 0" class="bg-yellow-500">{{ translate('Pendent') }}</pill>
        </div>
      </div>
      <list-action>
        <router-link :to="{name: 'user-update', params: { uuid: d.uuid }}" v-if="d.register_complete">
          <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <a href="" @click.prevent="invite(d.uuid)" v-if="!d.register_complete">
          <mail-icon class="icon-list mr-2" aria-hidden="true" />
        </a>
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
import { PlusCircleIcon, PlusSmIcon, PencilAltIcon, TrashIcon, MailIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
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
    TrashIcon,
    MailIcon,
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

  mixins: [ErrorHandling, Helpers, i18n],

  data() {
    return {

      // Data
      data: [],

      // Routes
      routes: {
        list: '/api/users',
        toggle: '/api/user/state',
        destroy: '/api/user',
        invite: '/api/user/invite'
      },

      // States
      isLoading: false,
      isFetched: false,
      isOwner: false,
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      NProgress.start();
      this.axios.get(`${this.routes.list}/${this.$route.params.companyUuid}`).then(response => {
        this.data = response.data.data;
        if (this.data[0] && this.data[0].company.owner) {
          this.isOwner = true;
        }
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

    invite(uuid) {
      NProgress.start();
      this.axios.get(`${this.routes.invite}/${uuid}`).then(response => {
        this.$notify({ type: "success", text: this.translate('Einladung versendet') });
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

  computed: {
    title() {
      if (this.data.length > 0) {
        return `${this.translate('Benutzer für')} ${this.data[0].company.name}`
      }
      return this.translate('Benutzer');
    },
  }
}
</script>