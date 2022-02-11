<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <form @submit.prevent="submit" v-if="isFetched" class="is-loaded">

    <header class="content-header">
      <h1>{{title}}</h1>
    </header>

    <div :class="[this.errors.text_intro ? 'has-error' : '', 'form-row']">
      <textarea class="is-inline-edit" v-model="newsletter.text_intro" placeholder="Intro"></textarea>
      <label-required />
    </div>

    <hr class="sb-lg" />

    <div class="form-row">
      <input type="text" class="is-inline-edit fs-md" v-model="newsletter.title_articles" placeholder="Rückblick">
    </div>

    <article-selector 
      :newsletterId="newsletter.id" 
      v-if="newsletter.id">
    </article-selector>

    <div class="form-row flex justify-start sb-md">
      <router-link 
        :to="{ name: 'newsletter-article-create', params: { newsletterId: newsletter.id }}" 
        class="feather-icon feather-icon--prepend" 
        v-if="newsletter.id">
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </router-link>
      <a 
        href="" 
        @click.prevent="storeAndRedirect()" 
        class="feather-icon feather-icon--prepend"
        v-else>
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </a>
    </div>

    <hr />

    <div class="form-row">
      <input type="text" class="is-inline-edit fs-md" v-model="newsletter.title_events" placeholder="Ausblick">
    </div>

    <event-selector
      v-bind:events.sync="newsletter.events"
      :label="'Vorschau hinzufügen'"
      :labelSelected="'Vorschau'"
      :data="newsletter.events"
    ></event-selector>

    <div class="form-row">
      <tinymce-editor
        :api-key="tinyApiKey"
        :init="tinyConfig"
        :class="'is-inline-edit'"
        v-model="newsletter.text_event"
      ></tinymce-editor>
    </div>

    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'newsletter-index' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer>

  </form>
</div>
</template>
<script>
import { PlusIcon } from 'vue-feather-icons';
import ErrorHandling from "@/mixins/ErrorHandling";
import RadioButton from "@/components/ui/RadioButton.vue";
import LabelRequired from "@/components/ui/LabelRequired.vue";
import Tabs from "@/components/ui/Tabs.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";
import tabsConfig from "@/views/pages/newsletter/newsletter/config/tabs.js";
import ArticleSelector from '@/views/pages/newsletter/newsletter/article/Index.vue';
import EventSelector from "@/views/pages/newsletter/newsletter/EventSelector.vue";
import tinyConfig from "@/config/tiny.js";
import TinymceEditor from "@tinymce/tinymce-vue";

export default {
  components: {
    PlusIcon,
    RadioButton,
    LabelRequired,
    Tabs,
    PageFooter,
    PageHeader,
    TinymceEditor,
    ArticleSelector,
    EventSelector
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      newsletter: {
        title_articles: 'Rückblick',
        title_events: 'Ausblick',
        text_intro: 'Intro',
        text_event: null,
        events: [],
      },

      // Validation
      errors: {
        text_intro: false,
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
      this.isLoading = true;
      let uri = `/api/newsletter/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.newsletter = response.data;
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
      this.axios.post('/api/newsletter', this.newsletter).then(response => {
        this.$router.push({ name: "newsletter-index" });
        this.$notify({ type: "success", text: "Newsletter erfasst!" });
        this.isLoading = false;
      });
    },

    storeAndRedirect() {
      this.isLoading = true;
      this.axios.post('/api/newsletter', this.newsletter).then(response => {
        this.$router.push({ name: "newsletter-article-create", params: { newsletterId: response.data.newsletterId }});
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/newsletter/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.newsletter).then(response => {
        this.$router.push({ name: "newsletter-index" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

    addEvent(e) {
      this.newsletter.events.push(e);
    },

    removeEvent(id) {
      const idx = this.newsletter.events.findIndex(x => x.id === id);
      this.newsletter.events.splice(idx, 1);
    }
  },

  computed: {
    title() {
      return this.$props.type == "edit" 
        ? "Newsletter bearbeiten" 
        : "Newsletter hinzufügen";
    }
  }
};
</script>
