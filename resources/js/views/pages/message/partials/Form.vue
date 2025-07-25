<template>
<div v-if="isFetched">
  <form @submit.prevent="submit" class="p-2 sm:p-4 mb-4 lg:mb-8 bg-light rounded-md">
    <div class="sm:grid sm:grid-cols-12 lg:gap-8">
      <div class="sm:col-span-12">
        <!-- is it a reply? -->
        <template v-if="$props.message">
          <feed-item-reply :originalMessage="message" class="md:max-w-[60%] !cursor-default"></feed-item-reply>
        </template>
        
        <!-- Recipients -->
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
            :init="tinyConfig"
            v-model="data.body"
          ></tinymce-editor>
        </div>

        <!-- Files -->
        <div class="form-group" v-if="allowUploads">
          <label class="mb-2 lg:mb-3">{{ translate('Dateien') }}</label>
          <vue-dropzone
            ref="dropzone"
            id="dropzone"
            :options="dropzoneConfig"
            @vdropzone-file-added="uploadFileAdded"
            @vdropzone-files-added="uploadFilesAdded"
            @vdropzone-success="uploadSuccess"
            @vdropzone-complete="uploadComplete"
            @vdropzone-complete-multiple="uploadCompleteMultiple"
            @vdropzone-queue-complete="uploadQueueComplete"
            @vdropzone-max-files-exceeded="uploadMaxFilesExceeded"
            :useCustomSlot=true>
            <div class="text-dark text-sm">
              <div class="font-bold">{{ translate('Datei auswählen oder hierhin ziehen') }}</div>
              <div>{{ translate('max. Grösse 250 MB') }}</div>
            </div>
          </vue-dropzone>
          <list v-if="hasUploads" class="my-4 sm:my-6 lg:my-8">
            <list-item v-for="(d, i) in data.files" :key="i" class="!p-0 border-gray-100 border-b">
              <div class="flex items-center no-underline hover:text-highlight py-2">
                <img 
                  :src="`/img/thumbnail/${d.name}?v=${Date.now()}`" 
                  height="100" 
                  width="100"
                  class="!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm"
                  v-if="d.instant_previewable" />
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
            <div class="form-group my-4 sm:my-6 lg:my-8" v-if="$store.state.user.admin && $store.state.feedType != 'private'">
              <label class="mb-2 lg:mb-3">{{ translate('Zwischenstand?') }}</label>
              <label class="relative !flex items-center cursor-pointer">
                <input type="checkbox" value="1" v-model="data.intermediate" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-none peer-focus:ring-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all  peer-checked:bg-highlight"></div>
              </label>
            </div>
          </list>
        </div>
      </div>

      <content-footer>
        <a href="javascript:;" class="form-helper form-helper-footer" @click="hide()">
          <span>{{ translate('Abbrechen') }}</span>
        </a>
        <button type="submit" :class="[!allowSubmit ? 'pointer-events-none opacity-40' : '', 'btn-send']">
          <template v-if="!hasValidUpload">
            <clock-icon class="h-5 w-5" aria-hidden="true" />
            <span class="block ml-2">{{ translate('Warten auf Upload') }}</span>
          </template>
          <template v-else>
            <mail-icon class="h-5 w-5" aria-hidden="true" />
            <span class="block ml-2">{{ $store.state.feedType == 'private' ? translate('Private Nachricht senden') : translate('Nachricht senden') }}</span>
          </template>
        </button>
      </content-footer>
      <confirm-recipients ref="confirmRecipients" />
      <confirm-intermediate ref="confirmIntermediate" />
      <confirm-non-matching-files ref="confirmNonMatchingFiles" />
    </div>
  </form>
</div>
</template>

<script>
import { TrashIcon, CloudDownloadIcon, MailIcon, PlusCircleIcon, ChevronDownIcon, ChevronUpIcon, ClockIcon } from "@vue-hero-icons/outline";
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
import Lightbox from "@/components/ui/layout/Lightbox.vue";
import ConfirmIntermediate from '@/views/pages/message/components/ConfirmIntermediate.vue';
import ConfirmRecipients from '@/views/pages/message/components/ConfirmRecipients.vue';
import ConfirmNonMatchingFiles from '@/views/pages/message/components/ConfirmNonMatchingFiles.vue';

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
    ClockIcon,
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
    FeedItemReply,
    Lightbox,
    ConfirmIntermediate,
    ConfirmRecipients,
    ConfirmNonMatchingFiles
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
        intermediate: 0,
        message_uuid: null,
        users: [],
        files: [],
      },

      hasDraft: false,
      draftName: null,

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
        fetchProject: '/api/project',
        fetchUsers: '/api/project/users',
        fetchMessageUsers: '/api/message/users',
        post: '/api/message/queue',
        destroy: '/api/upload'
      },

      // States
      isFetched: true,
      isLoading: false,
      isVisible: false,
      isReply: false,
      isSending: false,
      isProjectManager: false,
      hasUploads: false,
      hasValidUpload: true,
      allowUploads: true,

      // Messages
      messages: {
        created: 'Nachricht erfasst',
        confirm: 'Bitte löschen bestätigen',
        deleted: 'Datei gelöscht',
        image_exceeds_max_size: 'max. Dateigrösse überschritten',
        image_type_not_allowed: 'Dateityp nicht erlaubt',
        image_max_files_exceeded: 'max. Anzahl Dateien überschritten',
        confirm_recipients: null,
      },

      // Dropzone config
      dropzoneConfig: {
        url: "/api/upload",
        method: 'post',
        maxFilesize: 256,
        maxFiles: 99,
        createImageThumbnails: false,
        autoProcessQueue: true,
        uploadMultiple: false,
        //acceptedFiles: '',
        //previewTemplate: this.uploadTemplate(),
        headers: {
          'x-csrf-token': document.head.querySelector('meta[name="csrf-token"]').content
        }
      },

      uploadProgress: null,

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'no-api-key',

      // Deepl
      deeplApiKey: '86b707ef-e41d-2645-71b1-b0e62e6126ef:fx',
      deeplApi: 'https://api-free.deepl.com/v2/translate',
    };
  },

  created() {
    this.fetch();
    this.data.private = this.$store.state.feedType === 'private' ? 1 : 0;

    // Handle drafts
    this.handleDrafts();
  },

  methods: {

    /**
     * Message
     */

    fetch() {
      this.isFetched = false;
      this.axios.all([
        this.axios.get(`${this.routes.fetchProject}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.fetchUsers}/${this.$route.params.uuid}`),
      ]).then(axios.spread((...responses) => {
        this.project = responses[0].data;
        this.project.clients = responses[1].data.clients;
        this.project.owner = responses[1].data.owner;
        this.project.associates = responses[1].data.associates;
        this.isProjectManager = responses[1].data.isProjectManager;
                
        if (this.$props.message) {
          // handle recipients in case of a reply
          this.isReply = true;

          // add 'Re: ' to this.data.subject if it's not already in this.$props.message.subject
          if (this.$props.message.subject && !this.$props.message.subject.startsWith('Re: ')) {
            this.data.subject = `Re: ${this.$props.message.subject}`;
          }
          else {
            this.data.subject = this.$props.message.subject;
          }
          this.data.message_uuid = this.$props.message.uuid;
          this.handleReplyRecipients();

        }
        else {
          // handle recipients in case of a new message
          this.handleRecipients();
        }

        if (this.$store.state.markupMessage) {
          this.data.markupMessage = this.$store.state.markupMessage;
          this.data.subject = `${this.translate('Neue Markierungen/Kommentare')}`;
          this.allowUploads = false;
        }

        this.isFetched = true;
      }));
    },

    submit() {

      this.handleIntermediates().then(result => {
        if (result === false) return;
        this.validateRecipients().then(result => {
          if (result === false) return;
          this.handleUploads().then(result => {
          if (result === false) return;
            this.store();
          })
        })
      });
    },

    store() {
      this.isSending = true;
      this.data.intermediate = this.data.intermediate ? 1 : 0;
      this.axios.post(`${this.routes.post}/${this.$route.params.uuid}`, this.data).then(response => {
        this.removeDraft();
        this.reset();
        window.scrollTo(0, 0);

        // if there are faulty files, notify the user
        if (response.data.faultyFiles.length > 0) {
          // for each faulty file, notify the user
          response.data.faultyFiles.forEach(file => {
            this.$notify({ type: "danger", text: 'Die Datei ' + file + ' konnte nicht hochgeladen werden. Bitte versuche es erneut!', duration: -1 });
          })
        }
        this.$notify({ type: "success", text: this.translate(this.messages.created) });
      });
    },

    reset() {
      this.hide();
      this.isSending = false;
      this.data = {
        subject: null,
        body: null,
        private: this.$store.state.feedType === 'private' ? 1 : 0,
        users: [],
        files: [],
      };
      this.$store.commit('markupMessage', null);
      // this.$store.commit('markupFiles', []);
      this.$parent.fetch();
    },

    async validateRecipients() {
      try {
        const external_users = this.data.users.filter((user) => user.company_id != 1);
        const internal_users = this.data.users.filter((user) => user.team_id == 1);
        let message;

        if (!this.$store.state.user.admin && internal_users.length == 0) {
          message = this.translate('Es wurde kein Nightnurse-Empfänger ausgewählt. Trotzdem fortfahren?');
        }
        else if (this.$store.state.user.admin && external_users.length == 0 && this.data.private == 0) {
          message = this.translate('Es wurde kein kundenseitiger Empfänger ausgewählt. Trotzdem fortfahren?');
        }
        else {
          return true;
        }
        const result = await this.$refs.confirmRecipients.show({
          message: message
        });
        return result;
      }
      catch (error) {
        throw error;
      }
    },

    // Add/remove recipients
    addOrRemoveRecipient(state, user) {
      const idx = this.data.users.findIndex(x => x.uuid == user.uuid);
      if (state == true) {
        if (idx == -1) {
          this.errors.users = false;
          this.data.users.push(user);
        }
      }
      else {
        if (idx > -1) {
          this.data.users.splice(idx, 1);
        }
      }
    },

    handleReplyRecipients() {
      this.data.users = [];
      this.axios.get(`${this.routes.fetchMessageUsers}/${this.$props.message.uuid}`).then(response => {
        response.data.forEach(user => {
          this.addOrRemoveRecipient(true, user);
        });
      });

      // Add the sender of the message to the recipients
      this.addOrRemoveRecipient(true, this.$props.message.sender);

      this.removePreSelectedUser(this.$store.state.user);
    },


    // Add preselected recipients
    handleRecipients() {

      // Abort if we load a draft
      if (this.hasDraft) {
        return;
      }


      // Remove project manager from the associates to prevent double entries
      this.project.associates = this.project.associates.filter(x => x.id !== this.project.manager.id);
      
      // Add project manager to the recipients if the current user is not the project manager
      if (this.isProjectManager === false) {
        this.project.associates.unshift(this.project.manager);
      }
      
      // Preselect project associates
      this.project.associates.forEach(user => {
        this.addOrRemoveRecipient(true, user);
        this.removePreSelectedUser(user);
      });

      // For messages from admins, preselect all client users for public messages or internal users for private messages
      if (this.$store.state.user.admin && !this.isReply) {

        // Preselect all client users for external messages
        if (this.data.private == 0) {
          Object.entries(this.project.clients).forEach(client => {
            const users = client[1].users && client[1].users.length ? client[1].users : null;
            if (users) {
              users.forEach(user => {
                this.addOrRemoveRecipient(true, user);
              });
            }
          });
        }
        
        // Preselect all users of team Buenos Aires for internal messages
        if (this.data.private == 1) {
          this.project.internal_users.forEach(user => {
            this.addOrRemoveRecipient(true, user);
          });
        }
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

    // Handle intermediates
    async handleIntermediates() {

      try {
        // if one of the files starts with the project.number, ask the user if 
        // he wants to send the message as an intermediate marked message
        const possibleIntermediate = this.data.files.some(file => file.name.startsWith(this.project.number));
        if (possibleIntermediate && !this.data.intermediate && this.$store.state.user.admin && this.$store.state.feedType != 'private') {
          const result = await this.$refs.confirmIntermediate.show();
          return result;
        }
        else {
          return true;
        }
      }
      catch (error) {
        throw error;
      }
    },

    async handleUploads() {

      // return true if no files or user is not admin
      if (this.data.files.length == 0 || !this.$store.state.user.admin) {
        return true;
      }

      try {
        // if one of the files does not start with the project.number, 
        // require the user to confirm the upload
        const possibleNonMatchingFiles = this.data.files.some(file => !file.name.startsWith(this.project.number));
        if (possibleNonMatchingFiles && this.$store.state.user.admin && this.$store.state.feedType != 'private') {
          const result = await this.$refs.confirmNonMatchingFiles.show();
          return result;
        }
        else {
          return true;
        }
      }
      catch (error) {
        throw error;
      }
    },

    /** 
     * Image/File Upload 
     */

    uploadFileAdded(file) {
      this.hasValidUpload = false;
    },

    uploadFilesAdded(file) {
      this.hasValidUpload = false;
    },

    uploadSuccess(file, response) {
      let res = JSON.parse(file.xhr.response);
      this.data.files.push(res);
      this.hasUploads = true;
    },

    uploadQueueComplete() {
      this.hasValidUpload = true;
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
            // if (file.size > 9000000) {
            //   this.$notify({ type: "danger", text: this.messages.image_exceeds_max_size, duration: 2000 });
            // }
            // else {
            //   this.$notify({ type: "danger", text: this.messages.image_type_not_allowed, duration: 2000 });
            // }
            this.$notify({ type: "danger", text: this.messages.image_type_not_allowed, duration: 2000 });
            this.$refs.dropzone.removeFile(file);
          }
        }
      }
      else {
        this.$refs.dropzone.removeFile(file);
      }
    },

    uploadCompleteMultiple(files) {
      files.forEach(file => {
        this.uploadComplete(file);
      });
    },

    uploadMaxFilesExceeded(file) {
      this.$refs.dropzone.removeAllFiles(true);
      this.$notify({ type: "danger", text: this.messages.image_max_files_exceeded, duration: 2000 });
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
              <div class="dz-success-mark"></div>
              <div class="dz-error-mark"></div>
          </div>`;
    },

    show() {
      this.isVisible = true;
    },

    hide() {
      this.isVisible = false;
      this.isReply = false;
      this.allowUploads = true;
      this.$store.commit('markupMessage', null);
      //this.$store.commit('markupFiles');
      this.removeDraft();
      this.$emit('cancelMessage');
    },

    toggle() {
      this.isVisible = this.isVisible ? false : true;
    },

    handleDrafts() {
      this.draftName = `draft-${this.$route.params.uuid}`;
      if (localStorage.getItem(this.draftName)) {
        this.getDraft();
      }
    },

    getDraft() {
      this.hasDraft = true;
      this.data = JSON.parse(localStorage.getItem(this.draftName)).data;
      if (this.data.files.length > 0) {
        this.hasUploads = true;
      }
    },

    removeDraft() {
      localStorage.removeItem(this.draftName);
      this.hasDraft = false;
    },

  },

  computed: {
    title() {
      return `${this.translate('Neue Nachricht')} <span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    },

    allowSubmit() {
      // Form submission is allowed if:
      // 1. Not currently sending
      // 2. Has valid upload state
      // 3. Has recipients
      // 4. Has content (subject, body, or files)
      if (this.isSending || !this.hasValidUpload || this.data.users.length === 0) {
        return false;
      }

      // Must have either text content (subject or body) or files
      const hasTextContent = (this.data.subject && this.data.subject.length > 0) || (this.data.body && this.data.body.length > 0);
      const hasFiles = this.data.files.length > 0;

      return hasTextContent || hasFiles;
    },
  },

  watch: {
    data: {
      handler() {
        localStorage.setItem(this.draftName, JSON.stringify( { data: this.data } ));
      },
      deep: true,
    },
  },
};
</script>
