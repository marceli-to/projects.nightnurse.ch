<template>
<div>
  <div v-if="isVisible">
    <form @submit.prevent="submit" class="pb-4 sm:pb-6 lg:pb-8 border-b-2 mb-4 border-bottom" v-if="isFetched">
      <div class="sm:grid sm:grid-cols-12 gap-4 lg:gap-8">
        <div class="sm:col-span-7 lg:col-span-8">
          <div class="form-group">
            <label>{{ translate('Betreff') }}</label>
            <input type="text" v-model="data.subject">
          </div>
          <div class="form-group mt-6 lg:mt-8">
            <label class="mb-2 lg:mb-3 !flex justify-between w-full">
              <div>{{ translate('Mitteilung') }}</div>
              <div :class="[data.body.length > 10 ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none', 'flex items-center']">
                <div class="text-dark text-xs mr-2">{{ translate('Mitteilung übersetzen') }}</div>
                <a :href="`https://translate.google.com/?sl=auto&tl=en&text=${translation}&op=translate`" target="_blank" class="block mr-2">
                  <flag-en-icon />
                </a>
                <a :href="`https://translate.google.com/?sl=auto&tl=de&text=${translation}&op=translate`" target="_blank" class="block">
                  <flag-de-icon />
                </a>
              </div>
            </label>
            <tinymce-editor
              :api-key="tinyApiKey"
              :init="tinyConfig"
              v-model="data.body"
            ></tinymce-editor>
          </div>
          <div class="form-group">
            <label class="mb-2 lg:mb-3">{{ translate('Dateien') }}</label>
            <vue-dropzone
              ref="dropzone"
              id="dropzone"
              :options="dropzoneConfig"
              @vdropzone-success="uploadSuccess"
              @vdropzone-complete="uploadComplete"
              @vdropzone-max-files-exceeded="uploadMaxFilesExceeded"
              :useCustomSlot=true>
              <div class="text-dark text-sm">
                <div class="font-bold">{{ translate('Datei auswählen oder hierhin ziehen') }}</div>
                <div>{{ translate('max. Grösse 250 MB') }}</div>
              </div>
            </vue-dropzone>
            <list v-if="hasUpload && data.files.length" class="my-4 sm:my-6 lg:my-8">
              <list-item v-for="(d, i) in data.files" :key="i" class="!p-0 border-gray-100 border-b">
                <div class="flex items-center no-underline hover:text-highlight py-2">
                  <img 
                    :src="`/img/thumbnail/${d.name}`" 
                    height="100" 
                    width="100"
                    class="!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto max-w-[40px] bg-light rounded-sm"
                    v-if="d.preview" />
                  <div class="mr-2 lg:mr-3 py-1" v-else>
                    <file-type :extension="d.extension" />
                  </div>
                  <div class="font-mono text-xs">
                    {{ d.original_name | truncate(25, '...') }} – {{ d.size | filesize(d.size) }}
                  </div>
                </div>
                <list-action>
                  <a :href="`/img/original/${d.name}`" target="_blank" class="mr-2" v-if="d.preview">
                    <cloud-download-icon class="icon-list" aria-hidden="true" />
                  </a>
                  <a :href="`/storage/uploads/temp/${d.name}`" target="_blank" class="mr-2" v-else>
                    <cloud-download-icon class="icon-list" aria-hidden="true" />
                  </a>
                  <a href="javascript:;" @click.prevent="uploadDelete(d.name)">
                    <trash-icon class="icon-list" aria-hidden="true" />
                  </a>
                </list-action>
              </list-item>
            </list>
          </div>
        </div>
        <div class="sm:col-span-5 lg:col-span-4">
          <div class="bg-light p-2 px-4 pb-1">
            <div class="form-group" v-if="$store.state.user.admin">
              <form-radio 
                :label="translate('Private Nachricht?')"
                v-bind:private.sync="data.private"
                :model="data.private"
                :labelTrue="translate('Ja')"
                :labelFalse="translate('Nein')"
                :name="'private'">
              </form-radio>
            </div>

            <div :class="[errors.users ? 'is-invalid' : '', 'form-group']">
              <label class="mb-2">{{ translate('Empfänger') }} *</label>
              <div v-if="project.users.owner.teams" class="mb-4 lg:mb-8">
                <div v-for="team in project.users.owner.teams" :key="team.uuid" class="mb-4">

                  <template v-if="$store.state.user.admin">
                    <div class="form-check mb-2">
                      <input 
                        type="checkbox" 
                        class="checkbox" 
                        :id="team.uuid" 
                        @change="toggleAll($event, team.uuid)">
                      <label class="inline-block text-gray-800 font-bold" :for="team.uuid">
                        NNI {{ team.description }}
                      </label>
                    </div>                  
                  </template>
                  <template v-else>
                    <div class="inline-block text-sm text-dark font-sans font-bold">
                      NNI {{ team.description }}
                    </div>
                  </template>
                  {{ team.users }}
                  <!--
                  <div class="mb-1" v-if="team.users.length > 0">
                    <div v-for="(user, index) in team.users" :key="index" class="mb-2">
                      <div :class="[index < 6 ? 'flex' : 'hidden', 'form-check']" :data-truncatable="team.uuid" :data-truncatable-index="index">
                        <input 
                          type="checkbox" 
                          class="checkbox" 
                          :value="user.uuid" 
                          :id="user.uuid" 
                          :checked="addProjectLead(user.uuid)"
                          :data-team-uuid="team.uuid"
                          @change="toggleOne($event, user.uuid)">
                        <label class="inline-block text-gray-800" :for="user.uuid" v-if="user.register_complete">
                          {{ user.firstname }} {{ user.name }}
                        </label>
                        <label class="inline-block text-gray-800" :for="user.uuid" v-else>
                          {{ user.email }}
                        </label>
                      </div>
                    </div>
                  </div>
                  -->
                  <a 
                    href="javascript:;" 
                    @click="showOverflow(team.uuid)"
                    :data-truncatable-more="team.uuid"
                    class="text-gray-400 flex items-center no-underline hover:underline mt-3 sm:mt-0"
                    v-if="team.users.length > 10">
                    <chevron-down-icon class="h-5 w-5" aria-hidden="true" />
                    <span class="inline-block ml-2 text-xs font-mono">{{ translate('Mehr anzeigen') }}</span>
                  </a>
                  <a
                    href="javascript:;" 
                    @click="hideOverflow(team.uuid)"
                    :data-truncatable-less="team.uuid"
                    class="text-gray-400 hidden items-center no-underline hover:underline mt-3 sm:mt-0">
                    <chevron-up-icon class="h-5 w-5" aria-hidden="true" />
                    <span class="inline-block ml-2 text-xs font-mono">{{ translate('Weniger anzeigen') }}</span>
                  </a>
                </div>
              </div>
            </div>
            <div v-if="!data.private">
              <div v-for="company in project.users.clients" :key="company.uuid">
                <div v-if="company.users.length > 0" class="mb-4 lg:mb-8">
                  <div class="form-check mb-2">
                    <input 
                      type="checkbox" 
                      class="checkbox" 
                      :id="company.data.uuid" 
                      @change="toggleAll($event, company.data.uuid)">
                    <label class="inline-block text-gray-800 font-bold" :for="company.data.uuid">
                      {{ company.data.name }}
                    </label>
                  </div>
                  <div class="mb-1">
                    <div v-for="(user, index) in company.users" :key="user.uuid" class="mb-2">
                      <div :class="[index < 6 ? 'flex' : 'hidden', 'form-check']" :data-truncatable="company.data.uuid" :data-truncatable-index="index">
                        <input 
                          type="checkbox" 
                          class="checkbox" 
                          :value="user.uuid" 
                          :id="user.uuid" 
                          :data-team-uuid="company.data.uuid"
                          @change="toggleOne($event, user.uuid)">
                        <label class="inline-block text-gray-800" :for="user.uuid" v-if="user.register_complete">
                          {{ user.firstname }} {{ user.name }}
                        </label>
                        <label class="inline-block text-gray-800" :for="user.uuid" v-else>
                          {{ user.email }}
                        </label>
                      </div>
                    </div>
                  </div>
                  <a 
                    href="javascript:;" 
                    @click="showOverflow(company.data.uuid)"
                    :data-truncatable-more="company.data.uuid"
                    class="text-gray-400 flex items-center no-underline hover:underline mt-3 sm:mt-0"
                    v-if="company.users.length > 10">
                    <chevron-down-icon class="h-5 w-5" aria-hidden="true" />
                    <span class="inline-block ml-2 text-xs font-mono">{{ translate('Mehr anzeigen') }}</span>
                  </a>
                  <a
                    href="javascript:;" 
                    @click="hideOverflow(company.data.uuid)"
                    :data-truncatable-less="company.data.uuid"
                    class="text-gray-400 hidden items-center no-underline hover:underline mt-3 sm:mt-0">
                    <chevron-up-icon class="h-5 w-5" aria-hidden="true" />
                    <span class="inline-block ml-2 text-xs font-mono">{{ translate('Weniger anzeigen') }}</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="sm:grid sm:grid-cols-12 gap-4 lg:gap-8">
        <div class="sm:col-span-7 lg:col-span-8 flex items-center justify-between mt-4">
          <button type="submit" class="btn-send">
            <mail-icon class="h-5 w-5" aria-hidden="true" />
            <span class="block ml-2">{{ translate('Senden') }}</span>
          </button>
          <a href="javascript:;" class="form-helper form-helper-footer l-4 lg:ml-8" @click="hide()">
            <span>{{ translate('Abbrechen') }}</span>
          </a>
        </div>
        <div class="sm:col-span-5 lg:col-span-4"></div>
      </div>
    </form>
  </div>
  <!-- 
  <div v-else>
    <div class="flex justify-center">
      <a href="javascript:;" @click="show()" class="btn-create">
        <plus-circle-icon class="h-5 w-5" aria-hidden="true" />
        <span class="block ml-2">{{ translate('Neue Nachricht') }}</span>
      </a>
    </div>
  </div>
  -->
</div>
</template>

<script>
import { TrashIcon, CloudDownloadIcon, MailIcon, PlusCircleIcon, ChevronDownIcon, ChevronUpIcon } from "@vue-hero-icons/outline";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import Divider from "@/components/ui/misc/Divider.vue";
import Separator from "@/components/ui/misc/Separator.vue";
import List from "@/components/ui/layout/List.vue";
import ListItem from "@/components/ui/layout/ListItem.vue";
import ListAction from "@/components/ui/layout/ListAction.vue";
import FileType from "@/components/ui/misc/FileType.vue";
import { TheMask } from "vue-the-mask";
import tinyConfig from "@/config/tiny.js";
import TinymceEditor from "@tinymce/tinymce-vue";
import vue2Dropzone from "vue2-dropzone";
import FlagDeIcon from '@/components/ui/icons/flag-de';
import FlagEnIcon from '@/components/ui/icons/flag-en';
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {
  
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    TheMask,
    Asterisk,
    Divider,
    Separator,
    FileType,
    TrashIcon,
    CloudDownloadIcon,
    PlusCircleIcon,
    MailIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    List,
    ListItem,
    ListAction,
    TinymceEditor,
    FlagDeIcon,
    FlagEnIcon,
    vueDropzone: vue2Dropzone,
    NProgress
  },

  mixins: [i18n],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        subject: null,
        body: '',
        private: 0,
        users: [],
        files: [],
      },

      project: {
        number: null,
        name: null,
        user: null,
        users: [],
      },

      user: {},

      // Validation
      errors: {
        name: false,
      },

      // Routes
      routes: {
        fetch: '/api/project',
        post: '/api/message/queue',
        destroy: '/api/upload'
      },

      // States
      isFetched: true,
      isLoading: false,
      isVisible: false,
      hasUpload: false,

      // Messages
      messages: {
        created: 'Nachricht erfasst!',
        confirm: 'Bitte löschen bestätigen',
        deleted: 'Datei gelöscht',
        image_exceeds_max_size: 'max. Dateigrösse überschritten',
        image_type_not_allowed: 'Dateityp nicht erlaubt'
      },

      // Dropzone config
      dropzoneConfig: {
        url: "/api/upload",
        method: 'post',
        maxFilesize: 256,
        maxFiles: 99,
        createImageThumbnails: false,
        autoProcessQueue: true,
        //acceptedFiles: '',
        previewTemplate: this.uploadTemplate(),
        headers: {
          'x-csrf-token': document.head.querySelector('meta[name="csrf-token"]').content
        }
      },

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',

      // Deepl
      deeplApiKey: '86b707ef-e41d-2645-71b1-b0e62e6126ef:fx',
      deeplApi: 'https://api-free.deepl.com/v2/translate',
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    /**
     * Messsage
     */
    fetch() {
      this.isFetched = false;
      this.axios.all([
        this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`),
        this.axios.get(`/api/project/users/${this.$route.params.uuid}`),
      ]).then(axios.spread((...responses) => {
        this.project = responses[0].data;
        this.project.users = responses[1].data;
        console.log(this.project.users.owner);
        this.project.users.owner.teams = this.sortByProjectLead(this.project.users.owner);
        this.isFetched = true;
      }));

    },

    submit() {
      this.store();
    },

    store() {
      this.axios.post(`${this.routes.post}/${this.$route.params.uuid}`, this.data).then(response => {
        this.$notify({ type: "success", text: this.messages.created });
        this.reset();
      });
    },

    reset() {
      this.hide();
      this.data = {
        subject: null,
        body: null,
        private: 0,
        users: [],
        files: [],
      };
      this.$parent.fetch();
    },

    /**
     * Recipients
     */

    toggleAll(event, uuid) {
      const _this = this;
      const state = event.target.checked ? true : false;
      const boxes = document.querySelectorAll('[data-team-uuid="'+uuid+'"]');
      boxes.forEach(function(box){
        box.checked = state;
        _this.addOrRemove(state, box.value);
      });
    },

    toggleOne(event, uuid) {
      const state = event.target.checked ? true : false;
      this.addOrRemove(state, uuid);
    },

    showOverflow(uuid) {
      const recipients = document.querySelectorAll('[data-truncatable="'+uuid+'"]');
      recipients.forEach(function(recipient){
        recipient.classList.remove('hidden');
        recipient.classList.add('flex');
      });

      const btnMore = document.querySelector('[data-truncatable-more="'+uuid+'"]');
      btnMore.classList.remove('flex');
      btnMore.classList.add('hidden');

      const btnLess = document.querySelector('[data-truncatable-less="'+uuid+'"]');
      btnLess.classList.remove('hidden');
      btnLess.classList.add('flex');
    },

    hideOverflow(uuid) {
      const recipients = document.querySelectorAll('[data-truncatable="'+uuid+'"]');
      recipients.forEach(function(recipient){
        if (recipient.dataset.truncatableIndex >= 6) {
          recipient.classList.remove('flex');
          recipient.classList.add('hidden');
        }
      });

      const btnLess = document.querySelector('[data-truncatable-less="'+uuid+'"]');
      btnLess.classList.remove('flex');
      btnLess.classList.add('hidden');

      const btnMore = document.querySelector('[data-truncatable-more="'+uuid+'"]');
      btnMore.classList.remove('hidden');
      btnMore.classList.add('flex');

    },

    addOrRemove(state, value) {
      const idx = this.data.users.findIndex(x => x == value);
      if (state == true) {
        if (idx == -1) {
          this.data.users.push(value);
        }
      }
      else {
        if (idx > -1) {
          this.data.users.splice(idx, 1);
        }
      }

    },

    addProjectLead(uuid) {
      if (this.project.manager.uuid == uuid)
      {
        this.addOrRemove(true, uuid);
        return true;
      }
      return false;
    },

    sortByProjectLead(project) {
      let teams = [];
      project.teams.forEach(team => {
        const user = team.users.findIndex((u) => u.uuid === this.project.manager.uuid);
        if (user) {
          team.users.unshift(team.users.splice(user, 1)[0]);
        }
        teams.push(team);
      });
      return teams;
    },

    /** 
     * Image/File Upload 
     */

    uploadSuccess(file, response) {
      let res = JSON.parse(file.xhr.response);
      this.hasUpload = true;
      this.data.files.push(res);
    },

    uploadComplete(file) {
      if (file.status == "error") {

        if (file.xhr !== undefined) {
          let res = JSON.parse(file.xhr.response);
          if (res.errors.file) {
            res.errors.file.forEach(error => this.$notify({ type: "danger", text: error, duration: 2000 }));
            this.$refs.dropzone.removeFile(file);
          }
        }
        else {
          if (file.accepted == false) {
            if (file.size > 9000000) {
              this.$notify({ type: "danger", text: this.messages.image_exceeds_max_size, duration: 2000 });
            }
            else {
              this.$notify({ type: "danger", text: this.messages.image_type_not_allowed, duration: 2000 });
            }
            this.$refs.dropzone.removeFile(file);
          }
        }
      }
      else {
        this.$refs.dropzone.removeFile(file);
      }
    },

    uploadMaxFilesExceeded(file) {
      this.$refs.dropzone.removeAllFiles(true);
      this.$notify({ type: "danger", text: "image_max_files_exceeded", duration: 2000 });
    },

    uploadDelete(file) {
      if (confirm(this.messages.confirm)) {
        NProgress.start();
        this.axios.delete(`${this.routes.destroy}/${file}`).then(response => {
          const idx = this.data.files.findIndex(x => x.name == file);
          if (idx > -1) {
            this.data.files.splice(idx, 1);
          }
          NProgress.done();
          this.$notify({ type: "success", text: this.messages.deleted, duration: 2000 });
        });
      }
    },

    uploadTemplate: function () {
      return `<div class="dz-preview dz-file-preview">
              <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
              <div class="dz-error-message"><span data-dz-errormessage></span></div>
              <div class="dz-success-mark"><i class="fa fa-check"></i></div>
              <div class="dz-error-mark"><i class="fa fa-close"></i></div>
          </div>`;
    },

    show() {
      this.isVisible = true;
    },

    hide() {
      this.isVisible = false;
    },

    toggle() {
      this.isVisible = this.isVisible ? false : true;
    },

    decodeHTMLEntities(text) {
      let textArea = document.createElement('textarea');
      textArea.innerHTML = text;
      return textArea.value;
    }

  },

  computed: {
    title() {
      return `${this.translate('Neue Nachricht')} <span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    },

    translation() {
      const str = this.decodeHTMLEntities(this.data.body);
      if (str && str.length > 0) {
        return str.toString().replace( /(<([^>]+)>)/ig, ''); 
      }
    }
  }
};
</script>
