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
    <div>
      <select 
        v-model="type" 
        @change="selectType()"
        class="!border-none text-xs font-mono text-gray-400 min-w-[215px] text-right pr-2">
        <option value="active">{{ translate('Aktive Projekte') }}</option>
        <option value="archived">{{ translate('Archivierte Projekte') }}</option>
      </select>
    </div>  
  </div>

  <!-- user projects -->
  <div v-if="data.user_projects.length" class="max-w-5xl">
    <div v-for="d in data.user_projects" :key="d.uuid" class="grid grid-cols-12 mb-6 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          <span class="font-bold">{{ d.name }}</span>
          <span v-if="d.company">
            <separator class="hidden sm:inline-block" />
            <br class="sm:hidden">{{ d.company.name }}
          </span>
        </router-link>
        <div v-if="d.preview_messages" :class="[showIntermediates ? 'flex flex-wrap gap-x-6' : '', '']">
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
        <div class="flex items-center justify-end gap-x-2">
          <template v-if="$store.state.user.admin">
            <router-link :to="{name: 'project-update', params: { slug: d.slug, uuid: d.uuid }}" :title="translate('Bearbeiten')">
              <pencil-alt-icon class="icon-list" aria-hidden="true" />
            </router-link>
            <template v-if="type == 'trashed'">
              <a href="" @click.prevent="restore(d.uuid)" :title="translate('Wiederherstellen')">
                <refresh-icon class="icon-list" aria-hidden="true" />
              </a>
            </template>
            <a href="" @click.prevent="destroy(d.uuid)" :title="translate('Löschen')">
              <trash-icon class="icon-list" aria-hidden="true" />
            </a>
          </template>
          <template v-else-if="$store.state.user.client_admin">
            <router-link :to="{name: 'project-access', params: { slug: d.slug, uuid: d.uuid }}" :title="translate('Zugriffe verwalten')">
              <users-icon class="icon-list" aria-hidden="true" />
            </router-link>
          </template>
        </div>
      </div>
    </div>
    <hr class="max-w-5xl border-bottom !mt-10 !lg:mt-12 !my-10">
  </div>
  
  <!-- non user projects -->
  <div v-if="data.projects.length" class="max-w-5xl">
    <div v-for="d in data.projects" :key="d.uuid" class="grid grid-cols-12 mb-6 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
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
        <div class="flex items-center justify-end gap-x-2">
          <template v-if="$store.state.user.admin">
            <router-link :to="{name: 'project-update', params: { slug: d.slug, uuid: d.uuid }}">
              <pencil-alt-icon class="icon-list" aria-hidden="true" />
            </router-link>
            <template v-if="type == 'trashed'">
              <a href="" @click.prevent="restore(d.uuid)" :title="translate('Wiederherstellen')">
                <refresh-icon class="icon-list" aria-hidden="true" />
              </a>
            </template>
            <a href="" @click.prevent="destroy(d.uuid)" :title="translate('Löschen')">
              <trash-icon class="icon-list" aria-hidden="true" />
            </a>
          </template>
          <template v-else-if="$store.state.user.client_admin && d.state == 'active'">
            <router-link :to="{name: 'project-access', params: { slug: d.slug, uuid: d.uuid }}" :title="translate('Zugriffe verwalten')">
              <users-icon class="icon-list" aria-hidden="true" />
            </router-link>
          </template>
        </div>
      </div>
    </div>
    <hr class="max-w-5xl border-bottom !mt-10 !lg:mt-12 !my-10">
  </div>

  <!-- concluded projects -->
  <div v-if="data.concluded_projects.length" class="max-w-5xl opacity-40">
    <div v-for="d in data.concluded_projects" :key="d.uuid" class="grid grid-cols-12 mb-6 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
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
        <div class="flex items-center justify-end gap-x-2">
          <template v-if="$store.state.user.admin">
            <router-link :to="{name: 'project-update', params: { slug: d.slug, uuid: d.uuid }}">
              <pencil-alt-icon class="icon-list" aria-hidden="true" />
            </router-link>
            <template v-if="type == 'trashed'">
              <a href="" @click.prevent="restore(d.uuid)" :title="translate('Wiederherstellen')">
                <refresh-icon class="icon-list" aria-hidden="true" />
              </a>
            </template>
            <a href="" @click.prevent="destroy(d.uuid)" :title="translate('Löschen')">
              <trash-icon class="icon-list" aria-hidden="true" />
            </a>
          </template>
          <template v-else-if="$store.state.user.client_admin && d.state == 'active'">
            <router-link :to="{name: 'project-access', params: { slug: d.slug, uuid: d.uuid }}" :title="translate('Zugriffe verwalten')">
              <users-icon class="icon-list" aria-hidden="true" />
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </div>

  <!-- archived projects -->
  <div v-if="data.archived_projects.length" class="max-w-5xl opacity-40">
    <div v-for="d in data.archived_projects" :key="d.uuid" class="grid grid-cols-12 mb-6 sm:mb-8 lg:mb-10 text-xs sm:text-sm text-dark relative">
      <div class="absolute top-0 left-0 h-full w-[4px] rounded" :style="`background-color: ${d.color}`"></div>
      <div class="col-span-2 md:col-span-1 pl-2 sm:pl-3">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
          {{d.number}}
        </router-link>
      </div>
      <div class="col-span-8 md:col-span-9">
        <router-link :to="{name: 'messages', params: { slug: d.slug, uuid: d.uuid }}" class="relative text-dark font-normal no-underline">
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
        <div class="flex items-center justify-end gap-x-2">
          <template v-if="$store.state.user.admin">
            <router-link :to="{name: 'project-update', params: { slug: d.slug, uuid: d.uuid }}">
              <pencil-alt-icon class="icon-list" aria-hidden="true" />
            </router-link>
            <template v-if="type == 'trashed'">
              <a href="" @click.prevent="restore(d.uuid)" :title="translate('Wiederherstellen')">
                <refresh-icon class="icon-list" aria-hidden="true" />
              </a>
            </template>
            <a href="" @click.prevent="destroy(d.uuid)" :title="translate('Löschen')">
              <trash-icon class="icon-list" aria-hidden="true" />
            </a>
          </template>
          <template v-else-if="$store.state.user.client_admin && d.state == 'active'">
            <router-link :to="{name: 'project-access', params: { slug: d.slug, uuid: d.uuid }}" :title="translate('Zugriffe verwalten')">
              <users-icon class="icon-list" aria-hidden="true" />
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </div>

  <confirm-destroy ref="confirmDestroy" @softDelete="softDelete()" @forceDelete="forceDelete()" />
</div>
</template>
<script>
import { 
  PlusCircleIcon, 
  PlusSmIcon, 
  PencilAltIcon, 
  TrashIcon, 
  AnnotationIcon, 
  ArchiveIcon, 
  SwitchHorizontalIcon, 
  FolderIcon,
  RefreshIcon,
  UsersIcon
} from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";
import PageTitle from "@/mixins/PageTitle";
import Separator from "@/components/ui/misc/Separator.vue";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ProjectListItem from "@/views/pages/project/components/ListItem.vue";
import ProjectThumbnailItem from "@/views/pages/project/components/ThumbnailItem.vue";
import ConfirmDestroy from '@/views/pages/project/components/ConfirmDestroy.vue';
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
    RefreshIcon,
    UsersIcon,
    ContentHeader,
    Separator,
    NProgress,
    ProjectListItem,
    ProjectThumbnailItem,
    ConfirmDestroy
  },

  mixins: [ErrorHandling, Helpers, i18n, PageTitle],

  data() {
    return {

      // Data
      data: {
        user_projects: [],
        projects: [],
        concluded_projects: [],
        archived_projects: [],
      },

      // Routes
      routes: {
        list: {
          active: '/api/projects',
          archived: '/api/projects/archive',
        },
        toggle: '/api/project/state',
        destroy: {
          soft: '/api/project',
          force: '/api/project/force',
        },
        restore: '/api/project/restore',
      },

      type: 'active',

      itemToDelete: null,

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
    this.setPageTitle();
    this.setType();
  },

  methods: {

    fetch() {
      NProgress.start();
      this.axios.get(this.routes.list[this.type]).then(response => {
        this.data.user_projects = response.data.user_projects ? response.data.user_projects : [];
        this.data.concluded_projects = response.data.concluded_projects ? response.data.concluded_projects : [];
        this.data.projects = response.data.projects ? response.data.projects : [];
        this.data.archived_projects = response.data.archived_projects ? response.data.archived_projects : [];
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

    destroy(uuid) {
      this.itemToDelete = uuid;
      this.$refs.confirmDestroy.show();
    },

    restore(uuid) {
      NProgress.start();
      this.axios.get(`${this.routes.restore}/${uuid}`).then(response => {
        this.fetch();
        NProgress.done();
      });
    },

    softDelete() {
      NProgress.start();
      this.axios.delete(`${this.routes.destroy.soft}/${this.itemToDelete}`).then(response => {
        this.fetch();
        NProgress.done();
      });
    },

    forceDelete() {
      NProgress.start();
      this.axios.delete(`${this.routes.destroy.force}/${this.itemToDelete}`).then(response => {
        this.fetch();
        NProgress.done();
      });
    },

    selectType() {
      this.$router.push({ name: 'projects', params: { type: this.type } });
      this.fetch();
    },

    setType() {
      if (!this.$route.params.type) {
        this.type = 'active';
      }
      else {
        this.type = this.$route.params.type;
      }
      this.fetch();
    },

    toggleIntermediates() {
      this.showIntermediates = this.showIntermediates ? false : true;
    },
  },
}
</script>