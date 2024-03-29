<template>
<div>
  <content-header>
    <template #title>
      {{ title }}
    </template>
  </content-header>
  <form @submit.prevent="submit" v-if="isFetched" class="max-w-5xl">
    <div v-if="hasErrors" class="text-sm font-mono mb-2 text-red-500">
      {{errors.message}}
    </div>
    <div :class="[errors.email ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('E-Mail') }} <asterisk /></label>
      <input type="email" v-model="data.email">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <div class="form-group">
      <label>{{ translate('Sprache') }}</label>
      <select v-model="data.language_id">
        <option v-for="language in languages" :key="language.id" :value="language.id">
          {{ language.description }}
        </option>
      </select>
    </div>
    <content-footer>
      <button type="submit" class="btn-primary">{{ translate('Speichern') }}</button>
      <router-link :to="{ name: 'users', params: { companyUuid: $route.params.companyUuid}}" class="form-helper form-helper-footer">
        <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
        <span>{{ translate('Zurück') }}</span>
      </router-link>
    </content-footer>
  </form>
</div>
</template>
<script>
import { EyeIcon, ArrowLeftIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {
  components: {
    EyeIcon,
    ArrowLeftIcon,
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    Asterisk,
    NProgress
  },

  mixins: [ErrorHandling, i18n],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        email: null,
        company_uuid: this.$route.params.companyUuid,
        has_domain_confirmation: true,
        language_id: 1,
      },

      languages: [],

      // Validation
      errors: {
        email: false,
        message: null,
      },

      // Routes
      routes: {
        post: '/api/user/register',
        languages: '/api/languages',
      },

      // States
      isFetched: true,
      isLoading: false,
      hasErrors: false,
    };
  },

  created() {
    this.getLanguages();
  },

  methods: {

    submit() {
      NProgress.start();
      this.hasErrors = false;
      this.errors.message = null;
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "users" });
        this.$notify({ type: "success", text: this.translate('Benutzer erfasst') });
        NProgress.done();
      })
      .catch(error => {
        this.hasErrors = true;
        NProgress.done();
        this.errors.message = error.response.data.errors.email[0];
      });
    },

    getLanguages() {
      NProgress.start();
      this.isFetched = false;
      this.axios.get(this.routes.languages).then(response => {
        this.languages = response.data.data;
        this.isFetched = true;
        NProgress.done();
      });
    },
  },

  computed: {
    title() {
      return "Benutzer einladen";
    }
  }
};
</script>
