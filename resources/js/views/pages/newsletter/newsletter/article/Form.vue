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
        <div :class="[this.errors.text ? 'has-error' : '', 'form-row']">
          <label>Text</label>
          <textarea v-model="article.text"></textarea>
          <label-required />
        </div>
      </div>
    </div>
   
    <div v-show="tabs.image.active">
      <div>
        <div class="form-row" v-if="article.images.length == 0">
          <image-upload
            :restrictions="'jpg, png | max. 8 MB'"
            :maxFiles="99"
            :maxFilesize="8"
            :acceptedFiles="'.png,.jpg'"
          ></image-upload>
        </div>
        <div class="form-row">
          <image-edit 
            :images="article.images"
            :imagePreviewRoute="'cache'"
            :aspectRatioW="4"
            :aspectRatioH="3"
          ></image-edit>
        </div>
      </div>
    </div>
    
    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'newsletter-edit', params: {id: $route.params.newsletterId } }" class="btn-secondary">
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
import ImageUpload from "@/components/images/Upload.vue";
import ImageEdit from "@/views/pages/newsletter/newsletter/article/images/Edit.vue";
import tabsConfig from "@/views/pages/newsletter/newsletter/article/config/tabs.js";

export default {
  components: {
    RadioButton,
    LabelRequired,
    Tabs,
    PageFooter,
    PageHeader,
    ImageUpload,
    ImageEdit
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
        images: [],
        newsletter_id: this.$route.params.newsletterId,
      },

      // Validation
      errors: {
        title: false,
        text: false,
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
      let uri = `/api/newsletter/article/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.article = response.data;
        this.isFetched = true;
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
      this.axios.post('/api/newsletter/article', this.article).then(response => {
        this.$router.push({ name: "newsletter-edit", params: {id: this.article.newsletter_id } });
        this.$notify({ type: "success", text: "Artikel erfasst!" });
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/newsletter/article/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.article).then(response => {
        this.$router.push({ name: "newsletter-edit", params: {id: this.article.newsletter_id } });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

    // Store uploaded image
    storeImage(upload) {
      let image = {
        id: null,
        name: upload.name,
        orientation: upload.orientation,
        coords_w: 0,
        coords_h: 0,
        coords_x: 0,
        coords_y: 0,
        publish: 1,
        newsletter_article_id: null
      }

      if (this.$props.type == "edit") {
        image.newsletter_article_id = this.$route.params.id;
        this.axios.post('/api/newsletter/article/image', image).then(response => {
          this.$notify({ type: "success", text: "Bild gespeichert!" });
          image.id = response.data.newsletterArticleImageId;
          this.article.images.push(image);
        });
      }
      else {
        this.article.images.push(image);
      }
    },

    destroyImage(image, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/newsletter/article/image/${image}`;
        this.isLoading = true;
        this.axios.delete(uri).then(response => {
          const index = this.article.images.findIndex(x => x.name === image);
          this.article.images.splice(index, 1);
          this.isLoading = false;
        });
      }
    },

    toggleImage(image, event) {
      let uri = `/api/newsletter/article/image/state/${image.id}`;
      this.isLoading = true;
      this.axios.get(uri).then(response => {
        const index = this.article.images.findIndex(x => x.id === image.id);
        this.article.images[index].publish = response.data;
        this.isLoading = false;
      });
    },

    saveImageCoords(image) {
      let uri = `/api/newsletter/article/image/coords/${image.id}`;
      this.isLoading = true;
      this.axios.put(uri, image).then(response => {
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

  },

  computed: {
    title() {
      return this.$props.type == "edit" 
        ? "Artikel bearbeiten" 
        : "Artikel hinzufügen";
    }
  }
};
</script>
