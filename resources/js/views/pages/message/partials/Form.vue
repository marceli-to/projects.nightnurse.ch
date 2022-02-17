<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header :title="title"></content-header>
    
    <div class="form-group">
      <label>Betreff</label>
      <input type="text" v-model="data.subject">
    </div>

    <div class="form-group mt-6 lg:mt-8">
      <label class="mb-3 lg:mb-3">Nachricht</label>
      <tinymce-editor
        :api-key="tinyApiKey"
        :init="tinyConfig"
        v-model="data.body"
      ></tinymce-editor>
    </div>

    <div class="form-group">
      <label>Dateien</label>
      <vue-dropzone
        ref="dropzone"
        id="dropzone"
        :options="dropzoneConfig"
        @vdropzone-success="uploadSuccess"
        @vdropzone-complete="uploadComplete"
        @vdropzone-max-files-exceeded="uploadMaxFilesExceeded"
        :useCustomSlot=true>
        <div>
          <strong>Datei auswählen oder hierhin ziehen</strong><br>
          <small>JPG, PNG, TIFF | max. Grösse 100 MB</small>
        </div>
      </vue-dropzone>

      <div v-if="hasUpload" class="my-4 sm:my-6 lg:my-8 border-top font-mono text-dark text-sm">
        <div 
          class="border-bottom py-2 flex justify-between items-center"
          v-for="(d, i) in data.files" :key="i">
          <div class="flex items-center">
            <file-type :extension="d.extension" />
            <separator />
            <div>{{ d.name | truncate(50, '...')}}</div>
            <separator />
            <div>{{d.size | filesize(d.size)}}</div>
          </div>
          <div>
            <a href="javascript:;" @click.prevent="uploadDelete(d.file)">
              <trash-icon class="h-5 w-5 icon-list" />
            </a>
          </div>
        </div>
      </div>

    </div>

    <div class="form-group">
      <form-radio 
        :label="'Private Nachricht?'"
        v-bind:private.sync="data.private"
        :model="data.private"
        :name="'private'">
      </form-radio>
    </div>


    <div class="form-group">
      <label class="mb-2">Empfänger</label>

      <div v-for="company in project.companies" :key="company.uuid">
        <div v-if="company.users.length > 0">
          <div class="form-check mb-2">
            <input 
              type="checkbox" 
              class="checkbox" 
              :id="company.uuid" 
              @change="toggleAll($event, company.uuid)">
            <label class="inline-block text-gray-800 font-bold" :for="company.uuid">
              {{ company.name }} (Alle)
            </label>
          </div>
          <div class="grid lg:grid-cols-4 mb-6">
            <div v-for="user in company.users" :key="user.uuid">
              <div class="form-check">
                <input 
                  type="checkbox" 
                  class="checkbox" 
                  :value="user.uuid" 
                  :id="user.uuid" 
                  :data-company-uuid="company.uuid"
                  @change="toggleOne($event, user.uuid)">
                <label class="inline-block text-gray-800" :for="user.uuid">
                  {{ user.firstname }} {{ user.name }}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <content-footer>
      <button type="submit" class="btn-primary">Senden</button>
      <router-link :to="{ name: 'messages', params: { uuid: this.$route.params.uuid }}" class="form-helper form-helper-footer">
        <span>Zurück</span>
      </router-link>
    </content-footer>

  </form>
</div>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import { TrashIcon } from "@vue-hero-icons/outline";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import Divider from "@/components/ui/misc/Divider.vue";
import Separator from "@/components/ui/misc/Separator.vue";
import FileType from "@/components/ui/misc/FileType.vue";
import { TheMask } from "vue-the-mask";
import tinyConfig from "@/config/tiny.js";
import TinymceEditor from "@tinymce/tinymce-vue";
import vue2Dropzone from "vue2-dropzone";

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
    TinymceEditor,
    vueDropzone: vue2Dropzone
  },

  
  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        subject: null,
        body: null,
        private: 0,
        users: [],
        files: [],
      },

      project: {
        number: null,
        name: null
      },

      // Settings
      settings: {
        staff: [],
        states: [],
        companies: [],
      },

      // Validation
      errors: {
        name: false,
      },

      // Routes
      routes: {
        fetch: '/api/project',
        post: '/api/message',
        destroy: '/api/upload'
      },

      // States
      isFetched: true,
      isLoading: false,
      hasUpload: false,

      // Messages
      messages: {
        created: 'Nachricht erfasst!',
        confirm: 'Bitte löschen bestätigen',
        deleted: 'Datei gelöscht'
      },

      // Dropzone config
      dropzoneConfig: {
        url: "/api/upload",
        method: 'post',
        maxFilesize: 100,
        maxFiles: 99,
        createImageThumbnails: false,
        autoProcessQueue: true,
        acceptedFiles: '.png, .jpg, .jpeg, .tiff',
        previewTemplate: this.uploadTemplate(),
        headers: {
          'x-csrf-token': document.head.querySelector('meta[name="csrf-token"]').content
        }
      },

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',
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
      this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`).then(response => {
        this.project = response.data;
        this.isFetched = true;
      });
    },

    submit() {
      this.store();
    },

    store() {
      this.isLoading = true;
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      });
    },

    /**
     * Recipients
     */

    toggleAll(event, uuid) {
      const _this = this;
      const state = event.target.checked ? true : false;
      const boxes = document.querySelectorAll('[data-company-uuid="'+uuid+'"]');
      boxes.forEach(function(box){
        box.checked = state;
        _this.addOrRemove(state, box.value);
      });
    },

    toggleOne(event, uuid) {
      const state = event.target.checked ? true : false;
      this.addOrRemove(state, uuid);
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
              this.$notify({ type: "danger", text: 'image_exceeds_max_size', duration: 2000 });
            }
            else {
              this.$notify({ type: "danger", text: 'image_type_not_allowed', duration: 2000 });
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
        this.isLoading = true;
        this.axios.delete(`${this.routes.destroy}/${file}`).then(response => {
          const idx = this.data.files.findIndex(x => x.file == file);
          if (idx > -1) {
            this.data.files.splice(idx, 1);
          }
          this.isLoading = false;
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

  },

  computed: {
    title() {
      return `Neue Nachricht erstellen<br><span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    }
  }
};
</script>
