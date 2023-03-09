<template>
<div v-if="isFetched" class="max-w-5xl">

  <header class="mb-4 pt-2 sm:pt-3 pb-2 sticky top-0 bg-white z-40 border-bottom relative -ml-[1px] pl-[1px]">
    <div>
      <div class="text-xl lg:text-2xl font-bold mb-2 sm:mb-3 flex items-end sm:max-w-4xl leading-snug sm:leading-normal">
        <div class="text-dark" v-if="project.company">
          <div class="font-normal text-sm">
            {{project.company.name}}
          </div>
          <span :style="`color: ${project.color}`">{{project.number}} – {{project.name}}</span>
        </div>
        <div class="text-dark" v-else>
          {{project.number}} {{project.name}}
        </div>
        <router-link :to="{name: 'project-update', params: { uuid: project.uuid, redirect: 'messages' }}" v-if="$store.state.user.admin">
          <pencil-alt-icon class="icon-list mb-1 ml-1 sm:ml-2" aria-hidden="true" />
        </router-link>
      </div>
      <div class="grid grid-cols-12 sm:gap-2 max-w-sm md:max-w-lg">
        <div class="col-span-4 text-gray-400">
          <div class="text-xs font-mono pb-0.5">{{ translate('Projektleiter') }}</div>
          <div class="text-sm text-dark">
            {{project.manager.full_name}}
            <a :href="`tel:${project.manager.phone}`" class="flex items-center text-dark hover:text-highlight no-underline" v-if="project.manager.phone">
              <phone-icon class="w-4 h-4 mr-1" /> {{project.manager.phone}}
            </a>
          </div>
        </div>
        <div class="col-span-4 text-gray-400">
          <div class="text-xs font-mono pb-0.5">{{ translate('Projektstart') }}</div>
          <div class="text-sm text-dark">{{project.date_start}}</div>
        </div>
        <div class="col-span-4 text-gray-400">
          <div class="text-xs font-mono pb-0.5">{{ translate('Abgabetermin') }}</div>
          <div class="text-sm text-dark">{{project.date_end}}</div>
        </div>
      </div>
      <template v-if="!showInfo">
        <div class="mt-2">
          <a href="" @click.prevent="toggleInfo()" class="py-1 font-mono text-gray-400 no-underline text-xs flex items-center w-auto">
            {{ translate('Mehr anzeigen') }}
            <chevron-down-icon class="w-4 h-4 ml-1" />
          </a>
        </div>
      </template>
      <template v-if="showInfo">
        <div class="grid grid-cols-12 sm:gap-2 max-w-sm md:max-w-lg">
          <div class="col-span-4 text-gray-400 mt-4" v-if="project.workflow">
            <div class="text-xs font-mono pb-0.5">{{ translate('Workflow') }}</div>
            <div class="text-sm text-dark">
              <a :href="project.workflow" target="_blank" class="flex items-center no-underline hover:text-highlight group">
                {{ translate('Anzeigen') }}
                <external-link-icon class="w-4 h-4 ml-1 text-gray-400 group-hover:text-highlight" />
              </a>
            </div>
          </div>
          <div class="col-span-4 text-gray-400 mt-4" v-if="project.quotes.length">
            <div class="text-xs font-mono pb-0.5">{{ translate('Offerten') }}</div>
            <div v-for="(quote, index) in project.quotes" :key="index" class="text-sm text-dark">
              <a :href="quote.uri" target="_blank" class="flex items-center no-underline hover:text-highlight group">
                {{ quote.description }}
                <external-link-icon class="w-4 h-4 ml-1 text-gray-400 group-hover:text-highlight" />
              </a>
            </div>
          </div>
        </div>
        <div class="text-gray-400 mt-4">
          <div class="text-xs font-mono pb-0.5">{{ translate('Projektbeteiligte') }}</div>
          <div 
            v-for="(associate, index) in projectAssociates"
            :key="index"
            class="text-sm text-dark py-1 sm:py-0.5">
            {{ associate.full_name }}<template v-if="associate.phone">, <a :href="`tel:${associate.phone}`" class="text-dark hover:text-highlight no-underline">{{associate.phone}}</a></template>
          </div>
        </div>

        <div class="flex justify-start mt-2">
          <a href="" @click.prevent="toggleInfo()" class="py-1 font-mono text-gray-400 no-underline text-xs flex items-center">
            {{ translate('Weniger anzeigen') }}
            <chevron-up-icon class="w-4 h-4 ml-1" />
          </a>
        </div>
      </template>
    </div>
  </header>

  <message-form 
    :message="message"
    @cancelMessage="hideForm()" v-if="hasForm">
  </message-form>
  
  <div class="hidden sm:flex justify-between">
    <template v-if="$store.state.user.admin">
      <template v-if="filter == 'private'">
        <a href="javascript:;" 
          class="text-xs font-mono text-gray-400 flex justify-start items-center no-underline hover:text-highlight"
          @click="resetFilter()">
          <shield-check-icon class="h-4 w-4" aria-hidden="true" />
          <span class="block ml-2">{{ translate('Alle Nachrichten') }}</span>
        </a>
      </template>
      <template v-else>
        <a href="javascript:;" 
          class="text-xs font-mono text-gray-400 flex justify-start items-center no-underline hover:text-highlight"
          @click="setFilter('private')">
          <shield-check-icon class="h-4 w-4" aria-hidden="true" />
          <span class="block ml-2">{{ translate('Private Nachrichten') }}</span>
        </a>
      </template>
    </template>
    <template>
      <a href="javascript:;" 
        class="text-xs font-mono text-gray-400 flex justify-center items-center no-underline hover:text-highlight"
        @click="toggleTimeline()">
        <menu-icon class="h-4 w-4" aria-hidden="true" />
        <span class="block ml-2">{{ translate('Index') }}</span>
      </a>
    </template>
    <template v-if="filter == 'files'">
      <a href="javascript:;" 
        class="text-xs font-mono text-gray-400 flex justify-end items-center no-underline hover:text-highlight"
        @click="resetFilter()">
        <switch-horizontal-icon class="h-4 w-4" aria-hidden="true" />
        <span class="block ml-2">{{ translate('Alle Nachrichten') }}</span>
      </a>
    </template>
    <template v-else>
      <a href="javascript:;" 
        class="text-xs font-mono text-gray-400 flex justify-end items-center no-underline hover:text-highlight"
        @click="setFilter('files')">
        <switch-horizontal-icon class="h-4 w-4" aria-hidden="true" />
        <span class="block ml-2">{{ translate('Anhänge Filtern') }}</span>
      </a>
    </template>
  </div>

  <template v-if="hasTimeline">
    <feed-index :feedItems="feedItems" @scrollTo="scrollTo"></feed-index>
  </template>

  <template>
    <feed>
      <div v-for="(items, day) in feedItems" :key="day" class="relative">
        <feed-item-timestamp>{{ day }}</feed-item-timestamp>
        <feed-item 
          v-for="item in filteredItems(items)" 
          :key="item.uuid" 
          :item="item" 
          :filesOnly="filter == 'files' ? true : false"
          @itemDeleted="fetch()"
          @scrollTo="scrollTo"
          @reply="reply"
          :ref="`message-${item.uuid}`">
        </feed-item>
      </div>
    </feed>
  </template>

  <content-footer v-if="!hasForm">
    <router-link :to="{ name: 'projects' }" class="form-helper form-helper-footer">
      <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
      <span>{{ translate('Zurück') }}</span>
    </router-link>
    <a href="javascript:;" @click="toggleForm()" class="btn-create">
      <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
      <span class="block ml-2">{{ translate('Neue Nachricht') }}</span>
    </a>
  </content-footer>
</div>
</template>

<script>
import { 
  FilterIcon,
  PlusCircleIcon, 
  PencilAltIcon, 
  TrashIcon, 
  ShieldCheckIcon, 
  CloudDownloadIcon, 
  FolderDownloadIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  ChevronUpIcon,
  ArrowLeftIcon,
  PhoneIcon,
  SwitchHorizontalIcon,
  MenuIcon,
  ExternalLinkIcon
} 
from "@vue-hero-icons/outline";
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
import Feed from "@/components/ui/feed/Feed.vue";
import FeedIndex from "@/components/ui/feed/Index.vue";
import FeedItem from "@/components/ui/feed/Item.vue";
import FeedItemHeader from "@/components/ui/feed/Header.vue";
import FeedItemTimestamp from "@/components/ui/feed/TimeStamp.vue";
import FeedItemAttachement from "@/components/ui/feed/Attachement.vue";
import FeedItemBody from "@/components/ui/feed/Body.vue";
import MessageForm from "@/views/pages/message/partials/Form.vue";
import FileType from "@/components/ui/misc/FileType.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    PhoneIcon,
    PlusCircleIcon,
    TrashIcon,
    PencilAltIcon,
    ShieldCheckIcon,
    CloudDownloadIcon,
    FolderDownloadIcon,
    ChevronUpIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    SwitchHorizontalIcon,
    FilterIcon,
    MenuIcon,
    ArrowLeftIcon,
    ExternalLinkIcon,
    ContentHeader,
    ContentFooter,
    Separator,
    List,
    ListItem,
    ListAction,
    ListEmpty,
    Feed,
    FeedIndex,
    FeedItem,
    FeedItemHeader,
    FeedItemTimestamp,
    FeedItemAttachement,
    FeedItemBody,
    FileType,
    MessageForm,
    NProgress
  },

  mixins: [ErrorHandling, Helpers, PageTitle, i18n],

  data() {
    return {

      // Data
      feedItems: [],

      // Project data
      project: [],

      // Project associates
      projectAssociates: [],

      // Single message
      message: null,

      // Routes
      routes: {
        list: '/api/messages',
        fetch: '/api/message',
        destroy: '/api/message',
        project: '/api/project',
        reactionTypes: '/api/reaction-types',
      },

      // Reaction types
      reactionTypes: null,

      // States
      isLoading: false,
      isFetched: false,
      isReply: false,
      hasForm: false,
      hasTimeline: false,
      showInfo: false,
      filter: null,

      // Access
      canAccessPrivateMessages: false,
    };
  },

  mounted() {
    this.fetch();

    window.Echo.private(`timeline.${this.$route.params.uuid}`).listen('MessageSent', (e) => {
      if (e.message.private === 1 && !this.canAccessPrivateMessages) {
        return;
      }
      e.message.can_delete = false;
      this.feedItems['Today'].unshift(e.message);
      if (!('Notification' in window)) {
        return;
      }
      Notification.requestPermission(permission => {
        let notification = new Notification('Projekte Nightnurse', {
          body: 'Neue Nachricht...',
          icon: 'https://projects.nightnurse.ch/assets/img/logo-nightnurse.png'
        });
        notification.onclick = () => {
          window.open(e.message.uri);
        };
      });
    });
    
  },

  methods: {

    fetch() {
      this.isFetched = false;
      NProgress.start();
      this.axios.all([
        this.axios.get(`${this.routes.list}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.project}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.reactionTypes}`),
      ]).then(axios.spread((...responses) => {
        this.feedItems = responses[0].data.data ? responses[0].data.data : responses[0].data;
        this.project = responses[1].data;
        this.projectAssociates = this.getProjectAssociates();
        this.reactionTypes = responses[2].data;
        this.$store.commit('reactionTypes', this.reactionTypes);
        this.isFetched = true;
        this.message = null;
        this.canAccessPrivateMessages = this.$store.state.user.admin ? true : false;
        this.setPageTitle(this.project.title);
        NProgress.done();
      }));
    },

    reply(uuid) {
      this.axios.get(`${this.routes.fetch}/${uuid}`).then(response => {
        this.message = response.data;
        this.toggleForm();
      });
    },

    switchViewType() {
      this.viewType = this.viewType == 'standard' ? 'files-only' : 'standard';
    },

    toggleForm() {
      this.showForm();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    showForm() {
      this.hasForm = true;
    },

    hideForm() {
      this.hasForm = false;
      this.message = null;
    },

    toggleTimeline() {
      this.hasTimeline = this.hasTimeline ? false : true;
    },

    setFilter(filterAttribute) {
      this.filter = filterAttribute;
    },

    resetFilter() {
      this.filter = null;
    },

    filteredItems(items) {
      if (this.filter == 'private') {
        return items.filter(item => item.private === 1);
      }
      if (this.filter == 'files') {
        return items.filter(item => item.files.length > 0);
      }
      return items;
    },

    scrollTo(uuid) {
      this.hasTimeline = false;
      this.$nextTick(() => {
        const message = this.$refs[`message-${uuid}`][0].$el;
        const offset  = message.getBoundingClientRect().top - 140;
        window.scrollTo({top: offset, behavior: 'smooth'});
      });
    },

    toggleInfo() {
      this.showInfo = this.showInfo ? false : true;
    },

    getProjectAssociates() {
      return this.project.users.filter(user => user.company.owner !== 1);
    },

  },

  watch: {
    feedItems() { },
  }
}
</script>