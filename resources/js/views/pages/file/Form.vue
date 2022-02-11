<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <form @submit.prevent="submit" class="half-width" v-if="isFetched">
    <page-header>
      <h1>{{title}}</h1>
    </page-header>
    <div>
      <div>
        <div :class="[this.errors.name ? 'has-error' : '', 'form-row']">
          <label>Name*</label>
          <input type="text" v-model="file.name">
          <label-required />
        </div>
        <div :class="[this.errors.file ? 'has-error' : '', 'form-row']">
          <label>Datei</label>
          <div v-if="!file.file">
            <file-upload
              :restrictions="'pdf, word, zip, excel, text | max. 8 MB'"
              :maxFiles="99"
              :maxFilesize="8"
              :acceptedFiles="'.pdf, .doc, .docx, .zip, .xls, .xlsx, .txt'"
            ></file-upload>
          </div>
          <div class="listing is-form" v-if="file.file">
            <div class="listing__item">
              <div class="listing__item-body">{{ file.file }}</div>
              <div class="listing__item-action">
                <div>
                  <a :href="'/storage/uploads/' + file.file" target="_blank" class="feather-icon">
                    <download-cloud-icon size="18"></download-cloud-icon>
                  </a>
                </div>
                <div>
                  <a
                    href="javascript:;"
                    class="feather-icon"
                    @click.prevent="removeFile(file.file)"
                  >
                    <trash2-icon size="18"></trash2-icon>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="form-row is-last">
          <radio-button 
            :label="'Publizieren?'"
            v-bind:publish.sync="file.publish"
            :model="file.publish"
            :name="'publish'">
          </radio-button>
        </div>
      </div>
    </div>
    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'files' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
  </form>
</div>
</template>
<script>

// Icons
import { ArrowLeftIcon, DownloadCloudIcon, Trash2Icon } from 'vue-feather-icons';

// Mixins
import ErrorHandling from "@/mixins/ErrorHandling";

// Components
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";
import RadioButton from "@/components/ui/RadioButton.vue";
import LabelRequired from "@/components/ui/LabelRequired.vue";
import Tabs from "@/components/ui/Tabs.vue";
import FileUpload from "@/components/files/Upload.vue";

// Tabs config
import tabsConfig from "@/views/pages/file/config/tabs.js";

export default {
  components: {
    PageFooter,
    PageHeader,
    ArrowLeftIcon,
    Trash2Icon,
    DownloadCloudIcon,
    RadioButton,
    LabelRequired,
    FileUpload,
    Tabs
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      file: {
        name: null,
        file: null,
        size: null,
        types: [],
        publish: 1,
        images: [],
      },

      // Validation
      errors: {
        name: false,
        file: false,
      },

      // Loading states
      isFetched: true,
      isLoading: false,

      // Tabs config
      tabs: tabsConfig,
    };
  },

  created() {
    if (this.$props.type == "edit") {
      this.isFetched = false;
      this.isLoading = true;
      this.axios.get(`/api/file/${this.$route.params.id}`).then(response => {
        this.file = response.data;
        this.isFetched = true;
        this.isLoading = false;
      });
    }
  },

  methods: {

    // Submit form
    submit() {
      if (this.$props.type == "edit") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    store() {
      this.isLoading = true;
      this.axios.post('/api/file', this.file).then(response => {
        this.$router.push({ name: "files" });
        this.$notify({ type: "success", text: "Datei erfasst!" });
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/file/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.file).then(response => {
        this.$router.push({ name: "files" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

    // Store uploaded image
    storeFile(upload) {
      this.file.file = upload.name;
      this.file.size = upload.size;
      this.file.type = upload.type;
    },

    removeFile(file) {
      if (confirm("Bitte löschen bestätigen!")) {
        this.isLoading = true;
        this.axios.delete(`/api/file/byName/${file}`).then(response => {
          this.file.file = null;
          this.file.size = null;
          this.file.type = null;
          this.isLoading = false;
        });
      }
    },
  },

  computed: {
    title: function() {
      return this.$props.type == "edit" 
        ? "Datei bearbeiten" 
        : "Datei hinzufügen";
    }
  }
};
</script>
