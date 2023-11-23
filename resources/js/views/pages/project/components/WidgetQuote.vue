<template>
<div>
  <form @submit.prevent="submit">
    <content-header class="border-none !mb-2 !sm:mb-4 !pt-0">
      <template #title>
        {{ translate('Offerte hinzufügen') }}
      </template>
    </content-header>
    <div :class="[errors.description ? 'is-invalid' : '', 'form-group mb-3']">
      <label>{{ translate('Beschreibung') }} <asterisk /></label>
      <input type="text" v-model="data.description">
    </div>
    <div :class="[errors.uri ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('URL') }} <asterisk /></label>
      <input type="text" v-model="data.uri">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <footer>
      <div class="flex justify-between mt-8 lg:mt-12">
        <button type="submit" class="btn-primary" @click.prevent="store()">
          {{ translate('Speichern') }}
        </button>
      </div>
    </footer>
  </form>
</div>
</template>
<script>
import { ArrowLeftIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {
  
  components: {
    ArrowLeftIcon,
    ContentHeader,
    ContentFooter,
    Required,
    Asterisk,
    NProgress,
  },

  mixins: [i18n],

  props: {
    id: [Number, String]
  },

  data() {
    return {
      
      // Model
      data: {
        project_id: null,
        description: null,
        uri: null,
      },

      // Validation
      errors: {
        description: false,
        uri: false,
      },

      // Routes
      routes: {
        fetch: '/api/project/quote',
        post: '/api/project/quote',
        put: '/api/project/quote'
      },

      // States
      hasErrors: false,
    };
  },

  mounted() {
    this.data.project_id = this.$props.id;
  },
  
  methods: {

    store() {

      if (this.data.uri == null) {
        this.errors.uri = true;
        this.hasErrors = true;
      }

      if (this.data.description == null) {
        this.errors.description = true;
        this.hasErrors = true;
      }

      if (this.hasErrors) {
        this.$notify({ type: "danger", text: this.translate(`Bitte alle mit * markierten Felder prüfen!`)});
        return;
      }
      
      NProgress.start();
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$notify({ type: "success", text: this.translate('Offerte erfasst') });
        this.data.uuid = response.data.projectQuoteUuid;
        this.$emit('createdQuote', this.data);
        NProgress.done();
      }).catch(error => {
        // this.hasErrors = true;
        // this.errors.message = error.response.data.errors.email[0];
        NProgress.done();
      });
    },
  },
};
</script>
  