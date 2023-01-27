<template>
<div v-if="isFetched">
  <form @submit.prevent="submit" class="pb-4 sm:pb-6 lg:pb-8 border-b-2 mb-4 border-bottom">
    <div class="sm:grid sm:grid-cols-12 lg:gap-8">

      <div class="sm:col-span-12">

        <!-- is it a reply? -->
        <template v-if="$props.message">
          <feed-item-reply :originalMessage="message" class="md:max-w-[60%] !cursor-default"></feed-item-reply>
        </template>

        <!-- Message state -->
        <template v-if="$store.state.user.admin">
          <div class="form-group border-bottom pb-4">
            <div class="relative group text-xl hover:cursor-pointer">
              <label for="isPrivate" class="mb-2 block hover:cursor-pointer relative z-20">{{ translate('Private Nachricht?') }}</label>
              <input v-model="data.private" type="checkbox" class="hover:cursor-pointer absolute left-1/2 -translate-x-1/2 w-full h-full peer appearance-none rounded-md z-10" id="isPrivate" />
              <span class="w-11 h-6 flex items-center flex-shrink-0 p-1 bg-gray-300 rounded-full duration-300 ease-in-out peer-checked:bg-highlight after:h-4 after:w-4 after:bg-white after:rounded-full after:shadow-md after:duration-200 peer-checked:after:translate-x-5"></span>
            </div>
          </div>
        </template>

        <!-- Recepients -->
        <user-selection
          :private="data.private"
          :recipients="data.users"
          :manager="project.manager"
          :owner="project.owner"
          :clients="project.clients"
          :associates="project.associates"
          :hasErrors="errors.users"
          @addOrRemoveRecipient="addOrRemoveRecipient">
        </user-selection>

        <!-- Message (subject, text) -->
        <div class="form-group">
          <label>{{ translate('Betreff') }}</label>
          <input type="text" v-model="data.subject">
        </div>
        <div class="form-group mt-6 lg:mt-8">
          <label class="mb-2 lg:mb-3">{{ translate('Mitteilung') }}</label>
          <tinymce-editor
            :api-key="tinyApiKey"
            :init="tinyConfig"
            v-model="data.body"
          ></tinymce-editor>
        </div>

        <!-- Files -->
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
                  class="!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm"
                  v-if="d.preview" />
                <div class="mr-2 lg:mr-3 py-1" v-else>
                  <file-type :extension="d.extension" />
                </div>
                <div class="font-mono text-xs lg:hidden">
                  {{ d.original_name | truncate(25, '...') }} – {{ d.size | filesize(d.size) }}
                </div>
                <div class="font-mono text-xs hidden lg:block">
                  {{ d.original_name }} – {{ d.size | filesize(d.size) }}
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

      <content-footer>
        <a href="javascript:;" class="form-helper form-helper-footer" @click="hide()">
          <span>{{ translate('Abbrechen') }}</span>
        </a>
        <button type="submit" class="btn-send">
          <mail-icon class="h-5 w-5" aria-hidden="true" />
          <span class="block ml-2">{{ translate('Senden') }}</span>
        </button>
      </content-footer>
      
    </div>
  </form>
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
import i18n from "@/i18n";
import NProgress from 'nprogress';
import UserSelector from "@/components/ui/form/UserSelector.vue";
import UserSelection from "@/components/ui/form/UserSelection.vue";
import FeedItemReply from "@/components/ui/feed/Reply.vue";

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
    vueDropzone: vue2Dropzone,
    NProgress,
    UserSelector,
    UserSelection,
    FeedItemReply
  },

  mixins: [i18n],

  props: {

    message: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      
      // Model
      data: {
        subject: null,
        body: '',
        private: 0,
        message_uuid: null,
        users: [],
        files: [],
      },

      project: {
        number: null,
        name: null,
        manager: null,
        owner: {},
        clients: [],
        associates: [],
      },

      //user: {},

      // Validation
      errors: {
        name: false,
        users: false,
      },

      // Routes
      routes: {
        fetch: '/api/project',
        fetchUsers: '/api/project/users',
        post: '/api/message/queue',
        destroy: '/api/upload'
      },

      // States
      isFetched: true,
      isLoading: false,
      isVisible: false,
      isReply: false,
      isManager: false,
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
    if (this.$props.message) {
      this.isReply = true;
      this.data.subject = `Re: ${this.$props.message.subject ? this.$props.message.subject : ''}`;
      this.data.message_uuid = this.$props.message.uuid;
    }
  },

  methods: {

    /**
     * Messsage
     */

    fetch() {
      this.isFetched = false;
      this.axios.all([
        this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.fetchUsers}/${this.$route.params.uuid}`),
      ]).then(axios.spread((...responses) => {
        this.project = responses[0].data;
        this.project.clients = responses[1].data.clients;
        this.project.owner = responses[1].data.owner;
        this.project.associates = responses[1].data.associates;
        this.isManager = responses[1].data.isManager;
        this.addPreSelected();
        this.isFetched = true;
      }));
    },

    submit() {
      this.store();
    },

    store() {
      if (this.validate()) {
        this.axios.post(`${this.routes.post}/${this.$route.params.uuid}`, this.data).then(response => {
          this.$notify({ type: "success", text: this.messages.created });
          this.reset();
        });
      }
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

    validate() {

      // If the user is an admin (i.e. belongs to the owner company),
      // check if at least one recipient is a non-owner-recipient
      const external = this.data.users.filter((user) => user.company_id != 1);
      if (this.$store.state.user.admin && external.length == 0) {
        if (!confirm(this.translate('Es wurde kein kundenseitiger Empfänger ausgewählt. Trotzdem fortfahren?'))) {
          return false;
        }
      }
      if (this.data.users.length == 0) {
        this.errors.users = true;
        this.$notify({ type: "danger", text: this.translate('Bitte Empfänger auswählen...') });
        return false;
      }
      return true;
    },

    // Add/remove recipients
    addOrRemoveRecipient(state, user) {
      const idx = this.data.users.findIndex(x => x.uuid == user.uuid);
      if (state == true) {
        if (idx == -1) {
          this.errors.users = false;
          this.data.users.push(user);
          this.$store.commit('recipients', this.data.users);
        }
      }
      else {
        if (idx > -1) {
          this.data.users.splice(idx, 1);
          this.$store.commit('recipients', this.data.users);
        }
      }
    },

    // Add preselected recipients
    addPreSelected() {

      // Filter out project manager from the associates to prevent double entries
      this.project.associates = this.project.associates.filter(x => x.id !== this.project.manager.id);
      
      // Preselect the project manager if its not the same user as the logged in user
      if (this.isManager === false) {
        this.project.associates.unshift(this.project.manager);
      }
      
      // Preselect the sender of the original message if its a reply
      if (this.isReply) {
        this.addOrRemoveRecipient(true, this.$props.message.sender);
      }

      // Preselect project associates
      this.project.associates.forEach(user => {
        this.addOrRemoveRecipient(true, user);
        this.removePreSelectedUser(user);
      });

      // Preselect all client users for the first message (only for admins)
      if (this.$store.state.user.admin && this.project.is_first_message) {
        Object.entries(this.project.clients).forEach(client => {
          const users = client[1].users && client[1].users.length ? client[1].users : null;
          if (users) {
            users.forEach(user => {
              this.addOrRemoveRecipient(true, user);
            });
          }
        });
      }

    },

    removePreSelectedUser(user) {
      this.project.owner.teams.forEach((team, idx) => {
        team.users.forEach(u => {
          const index = team.users.findIndex(x => x.uuid === user.uuid);
          if (index > -1) team.users.splice(index, 1);
        });
      })
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
      this.isReply = false;
      this.$emit('cancelMessage');
    },

    toggle() {
      this.isVisible = this.isVisible ? false : true;
    },
  },

  computed: {
    title() {
      return `${this.translate('Neue Nachricht')} <span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    },
  }
};
</script>
