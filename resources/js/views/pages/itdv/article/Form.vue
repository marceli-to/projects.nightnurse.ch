<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <form @submit.prevent="submit" v-if="isFetched">

    <header class="content-header">
      <h1>{{title}}</h1>
    </header>

    <tabs :tabs="tabs" :errors="errors"></tabs>
    
    <div v-show="tabs.data.active">
      <div>
        <div :class="[this.errors.title ? 'has-error' : '', 'form-row']">
          <label>Titel *</label>
          <input type="text" v-model="article.title">
          <label-required />
        </div>

        <div class="form-row">
          <label>Text</label>
          <tinymce-editor
            :api-key="tinyApiKey"
            :init="tinyConfig"
            v-model="article.text"
          ></tinymce-editor>
        </div>
        
      </div>
    </div>
   
    <div v-show="tabs.settings.active">
      <div>
        <div class="form-row">
          <radio-button 
            :label="'Publizieren?'"
            v-bind:publish.sync="article.publish"
            :model="article.publish"
            :name="'publish'">
          </radio-button>
        </div>
      </div>
    </div>
    
    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'articles' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer>

  </form>
</div>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import RadioButton from "@/components/ui/RadioButton.vue";
import LabelRequired from "@/components/ui/LabelRequired.vue";
import Tabs from "@/components/ui/Tabs.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";
import tabsConfig from "@/views/pages/itdv/article/config/tabs.js";
import tinyConfig from "@/config/tiny.js";
import TinymceEditor from "@tinymce/tinymce-vue";

export default {
  components: {
    RadioButton,
    LabelRequired,
    Tabs,
    PageFooter,
    PageHeader,
    TinymceEditor
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      article: {
        title: null,
        text: null,
        publish: 1,
      },

      // Validation
      errors: {
        title: false,
      },

      // Loading states
      isFetched: true,
      isLoading: false,

      // Tabs config
      tabs: tabsConfig,

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',
    };
  },

  created() {
    if (this.$props.type == "edit") {
      this.isFetched = false;
      let uri = `/api/article/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.article = response.data;
        this.isFetched = true;
      });
    }
    // Get files for tinymce
    let _this = this;
    this.tinyConfig.link_list = function(success) {
      _this.axios.get(`/api/files/list`).then(response => {
        success(response.data);
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
      this.axios.post('/api/article', this.article).then(response => {
        this.$router.push({ name: "articles" });
        this.$notify({ type: "success", text: "Text erfasst!" });
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/article/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.article).then(response => {
        this.$router.push({ name: "articles" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

  },

  computed: {
    title() {
      return this.$props.type == "edit" 
        ? "Text bearbeiten" 
        : "Text hinzufügen";
    }
  }
};
</script>
