<template>
<div v-if="isFetched">
  <content-header>
    <template #icon>
      <router-link :to="{ name: 'project-create' }" class="btn-add" v-if="$store.state.user.admin">
        <plus-sm-icon class="h-5 w-5" aria-hidden="true" />
      </router-link>
    </template>
    <template #title>
      {{ translate('Projekte') }}
    </template>

  </content-header>

  <div class="flex justify-between w-full mb-4 sm:mb-6 lg:mb-8">
    <a href="" @click.prevent="toggleIntermediates()" class="text-xs font-mono text-gray-400 flex justify-end items-center no-underline hover:text-highlight">
      <switch-horizontal-icon class="h-4 w-4" aria-hidden="true" />
      <span class="block ml-2">{{ translate('Zwischenstände anzeigen') }}</span>
    </a>
    <a href="" 
      @click.prevent="archived()" 
      class="text-xs font-mono text-gray-400 flex justify-end items-center no-underline hover:text-highlight"
      v-if="$store.state.user.admin">
      <folder-icon class="h-4 w-4" aria-hidden="true" />
      <span v-if="isArchive" class="block ml-2">{{ translate('Aktive Projekte') }}</span>
      <span v-else class="block ml-2">{{ translate('Archivierte Projekte') }}</span>
    </a>

  </div>

  <div v-if="data.user_projects.length" class="max-w-5xl">
    <div v-for="d in data.user_projects" :key="d.uuid" class="grid grid-cols-12 mb-6 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          <span class="font-bold">{{ d.name }}</span>
          <span v-if="d.company">
            <separator class="hidden sm:inline-block" />
            <br class="sm:hidden">{{ d.company.name }}
          </span>
        </router-link>
        <div v-if="d.preview_messages" :class="[showIntermediates ? 'flex' : '', '']">
          <template v-if="showIntermediates">
            <div v-for="(message, iteration) in d.preview_messages" :key="message.uuid" :class="[message.intermediate ? 'mt-2' : '', '']">
              <project-thumbnail-item :message="message" :project="d" v-if="message.intermediate"/>
            </div>
          </template>
          <template v-else>
            <div v-for="(message, iteration) in d.preview_messages" :key="message.uuid" class="mt-2">
              <project-list-item :message="message" :project="d" />
            </div>
          </template>
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
    <hr class="max-w-5xl border-bottom !mt-10 !my-8 !lg:mt-12 !my-10">
  </div>
  
  <div v-if="data.projects.length" class="max-w-5xl">
    <div v-for="d in data.projects" :key="d.uuid" class="grid grid-cols-12 mb-6 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          <span class="font-bold">{{ d.name }}</span>
          <span v-if="d.company">
            <separator class="hidden sm:inline-block" />
            <br class="sm:hidden">{{ d.company.name }}
          </span>
        </router-link>

        <div v-if="d.preview_messages" :class="[showIntermediates ? 'flex' : '', '']">
          <template v-if="showIntermediates">
            <div v-for="(message, iteration) in d.preview_messages" :key="message.uuid" :class="[message.intermediate ? 'mt-2' : '', '']">
              <project-thumbnail-item :message="message" :project="d" v-if="message.intermediate"/>
            </div>
          </template>
          <template v-else>
            <div v-for="(message, iteration) in d.preview_messages" :key="message.uuid" class="mt-2">
              <project-list-item :message="message" :project="d" />
            </div>
          </template>
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


</div>
</template>
<script>
import { PlusCircleIcon, PlusSmIcon, PencilAltIcon, TrashIcon, AnnotationIcon, ArchiveIcon, SwitchHorizontalIcon, FolderIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import PageTitle from "@/mixins/PageTitle";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ProjectListItem from "@/views/pages/project/components/ListItem.vue";
import ProjectThumbnailItem from "@/views/pages/project/components/ThumbnailItem.vue";

import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    PlusCircleIcon,
    PlusSmIcon,
    PencilAltIcon,
    TrashIcon,
    AnnotationIcon,
    ArchiveIcon,
    FolderIcon,
    SwitchHorizontalIcon,
    ContentHeader,
    Separator,
    NProgress,
    ProjectListItem,
    ProjectThumbnailItem
  },

  mixins: [ErrorHandling, Helpers, i18n, PageTitle],

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
        listArchive: '/api/projects-archive',
        toggle: '/api/project/state',
        destroy: '/api/project'
      },

      // States
      isLoading: false,
      isFetched: false,
      isArchive: false,
      showIntermediates: false,

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
    this.setPageTitle();
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
    },

    toggleIntermediates() {
      this.showIntermediates = this.showIntermediates ? false : true;
    },
  },
}
</script>