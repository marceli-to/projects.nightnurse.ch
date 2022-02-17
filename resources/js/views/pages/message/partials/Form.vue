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
      <form-radio 
        :label="'Private Nachricht?'"
        v-bind:private.sync="data.private"
        :model="data.private"
        :name="'private'">
      </form-radio>
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
import { XIcon } from "@vue-hero-icons/outline";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import { TheMask } from "vue-the-mask";
import tinyConfig from "@/config/tiny.js";
import TinymceEditor from "@tinymce/tinymce-vue";

export default {
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    TheMask,
    Asterisk,
    XIcon,
    TinymceEditor
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
      },

      // States
      isFetched: true,
      isLoading: false,

      // Messages
      messages: {
        created: 'Nachricht erfasst!',
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

  },

  computed: {
    title() {
      return `Neue Nachricht erstellen<br><span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    }
  }
};
</script>
