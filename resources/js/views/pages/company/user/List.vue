<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'user-register', params: {companyUuid: $route.params.companyUuid} }" class="btn-icon">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>
  <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        <div v-if="d.register_complete">
          {{d.firstname}} {{ d.name }}
          <span class="hidden sm:inline"><separator />{{ d.email }}</span>
          <pill v-if="d.role_id == 1">{{translate('Admin')}}</pill>
        </div>
        <div v-else>
          {{ d.email }}
          <pill v-if="d.register_complete == 0" class="bg-yellow-500">{{translate('Pendent')}}</pill>
        </div>
      </div>
      <list-action>
        <router-link :to="{name: 'user-update', params: { uuid: d.uuid }}" v-if="d.register_complete">
          <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <a href="" @click.prevent="destroy(d.uuid)">
          <trash-icon class="icon-list" aria-hidden="true" />
        </a>
      </list-action>
    </list-item>
  </list>
  <list-empty v-else>
    {{translate('Es sind noch keine Daten vorhanden')}}
  </list-empty>
  <content-footer>
    <router-link :to="{ name: 'companies'}" class="btn-primary">
      <span>{{translate('Zurück')}}</span>
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
import Pill from "@/components/ui/misc/Pill.vue";
import i18n from "@/i18n";

export default {

  components: {
    PlusCircleIcon,
    PencilAltIcon,
    TrashIcon,
    ContentHeader,
    ContentFooter,
    Separator,
    List,
    ListItem,
    ListAction,
    ListEmpty,
    Pill
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
        destroy: '/api/user'
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
      this.axios.get(`${this.routes.list}/${this.$route.params.companyUuid}`).then(response => {
        this.data = response.data.data;
        if (this.data[0] && this.data[0].company.owner) {
          this.isOwner = true;
        }
        this.isFetched = true;
      });
    },

    toggle(uuid,event) {
      this.isLoading = true;
      this.axios.get(`${this.routes.toggle}/${uuid}`).then(response => {
        const index = this.data.findIndex(x => x.uuid === uuid);
        this.data[index].publish = response.data;
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        this.isLoading = false;
      });
    },

    destroy(uuid, event) {
      if (confirm(this.translate('Bitte löschen bestätigen'))) {
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
      if (this.data.length > 0) {
        return `${this.translate('Benutzer für')} <span class="text-highlight">${this.data[0].company.name}</span>`
      }
      return this.translate('Benutzer');
    }
  }
}
</script>