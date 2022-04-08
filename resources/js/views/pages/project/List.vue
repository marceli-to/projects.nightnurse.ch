<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'project-create' }" class="btn-icon" v-if="$store.state.user.admin">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>
  <list v-if="data.length">
    <list-item v-for="d in data" :key="d.uuid">
      <div class="flex items-center">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}<separator />{{ d.name }}<separator /><span v-if="d.company">{{ d.company.name }}</span>
        </router-link>
      </div>
      <list-action v-if="$store.state.user.admin">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative">
          <span v-if="d.messages.length" class="rounded-full bg-highlight absolute -top-[2px] right-[7px] h-2 w-2 block"></span>
          <annotation-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <router-link :to="{name: 'project-update', params: { uuid: d.uuid }}">
          <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
        <a href="" @click.prevent="destroy(d.uuid)">
          <trash-icon class="icon-list" aria-hidden="true" />
        </a>
      </list-action>
      <list-action v-else>
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative">
          <span v-if="d.messages" class="rounded-full bg-highlight absolute -top-[2px] right-[7px] h-2 w-2 block"></span>
          <annotation-icon class="icon-list mr-2" aria-hidden="true" />
        </router-link>
      </list-action>
    </list-item>
  </list>
  <list-empty v-else>
    {{messages.emptyData}}
  </list-empty>
</div>
</template>
<script>
import { PlusCircleIcon, PencilAltIcon, TrashIcon, AnnotationIcon } from "@vue-hero-icons/outline";
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
    AnnotationIcon,
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
        list: '/api/projects',
        toggle: '/api/project/state',
        destroy: '/api/project'
      },

      // States
      isLoading: false,
      isFetched: false,

      // Messages
      messages: {
        emptyData: 'Es sind noch keine Projekte vorhanden...',
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
        this.data = response.data.data ? response.data.data : response.data;
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
      return "Projekte";
    }
  }
}
</script>