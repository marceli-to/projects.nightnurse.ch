<template>
<div v-if="isFetched">
  <content-header :title="title">
    <router-link :to="{ name: 'project-create' }" class="btn-icon" v-if="$store.state.user.admin">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
    </router-link>
  </content-header>

  <div v-if="data.user_projects.length" class="max-width-content">
    <div v-for="d in data.user_projects" :key="d.uuid" class="grid grid-cols-12 mb-4 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
         <span class="font-bold">{{ d.name }}</span><span v-if="d.company"><separator />{{ d.company.name }}</span>
        </router-link>
        <div v-if="d.messages">
          <div v-for="(message, iteration) in d.messages" :key="message.uuid">
            <div v-if="iteration < 3" class="pt-1">
              <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
                <span v-if="message.subject">{{message.subject}}</span>
                <span v-else-if="message.body_preview">{{ message.body_preview }}</span>
                <span v-else>–</span>
                <span v-if="message.sender" class="text-gray-400 text-xs font-mono"><separator />{{message.sender.full_name}}</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-2 md:col-span-2">
        <div class="flex items-center justify-end" v-if="$store.state.user.admin">
          <router-link :to="{name: 'project-update', params: { uuid: d.uuid }}">
            <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
          </router-link>
          <a href="" @click.prevent="destroy(d.uuid)">
            <trash-icon class="icon-list" aria-hidden="true" />
          </a>
        </div>
      </div>
    </div>
    <hr class="max-width-content border-bottom !mt-10 !my-8 !lg:mt-12 !my-10">
  </div>
  
  <div v-if="data.projects.length" class="max-width-content">
    <div v-for="d in data.projects" :key="d.uuid" class="grid grid-cols-12 mb-4 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
         <span class="font-bold">{{ d.name }}</span><span v-if="d.company"><separator />{{ d.company.name }}</span>
        </router-link>
        <div v-if="d.messages">
          <div v-for="(message, iteration) in d.messages" :key="message.uuid">
            <div v-if="iteration < 3" class="pt-1.5">
              <span v-if="message.subject">{{message.subject}}</span>
              <span v-else-if="message.body_preview">{{ message.body_preview }}</span>
              <span v-else>–</span>
              <span v-if="message.sender" class="text-gray-400 text-xs font-mono"><separator />{{message.sender.full_name}}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-2 md:col-span-2">
        <div class="flex items-center justify-end" v-if="$store.state.user.admin">
          <router-link :to="{name: 'project-update', params: { uuid: d.uuid }}">
            <pencil-alt-icon class="icon-list mr-2" aria-hidden="true" />
          </router-link>
          <a href="" @click.prevent="destroy(d.uuid)">
            <trash-icon class="icon-list" aria-hidden="true" />
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="max-width-content">
    <a href="" @click.prevent="archived()" class="text-gray-600 text-sm font-mono flex items-center">
      <archive-icon class="mr-2" aria-hidden="true" />
      <span v-if="isArchive">{{ translate('Show archive') }}</span>
      <span v-else>{{ translate('Show active') }}</span>
    </a>
  </div>
</div>
</template>
<script>
import { PlusCircleIcon, PencilAltIcon, TrashIcon, AnnotationIcon, ArchiveIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
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
    PlusCircleIcon,
    PencilAltIcon,
    TrashIcon,
    AnnotationIcon,
    ArchiveIcon,
    ContentHeader,
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
      data: {
        user_projects: [],
        all_projects: [],
      },

      // Routes
      routes: {
        list: '/api/projects',
        listArchive: '/api/projects/archive',
        toggle: '/api/project/state',
        destroy: '/api/project'
      },

      // States
      isLoading: false,
      isFetched: false,
      isArchive: false,

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
      let route = this.routes.list;
      if (this.isArchive) {
        route = this.routes.listArchive;
      }

      NProgress.start();
      this.axios.get(route).then(response => {
        this.data.user_projects = response.data.user_projects ? response.data.user_projects : [];
        this.data.projects = response.data.projects ? response.data.projects : [];
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

    archived() {
      this.isArchive = this.isArchive ? false : true;
      this.fetch();
    }
  },

  computed: {
    title() {
      return this.translate('Projekte');
    }
  }
}
</script>