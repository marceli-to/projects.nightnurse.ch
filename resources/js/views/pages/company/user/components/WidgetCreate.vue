<template>
<div>
  <form @submit.prevent="submit">
    <content-header class="border-none !mb-2 !sm:mb-4 !pt-0">
      <template #title>
        {{ translate('Benutzer hinzufügen') }}
      </template>
    </content-header>
    <div v-if="hasErrors" class="text-sm font-mono mb-2 text-red-500">
      {{errors.message}}
    </div>
    <template v-if="errors.validEmailDomain == false">
      <div class="flex items-start py-2 px-3 mr-1 mb-4 rounded-sm bg-yellow-500 text-white text-xs font-mono">
        <exclamation-icon class="shrink-0 block w-6 h-auto mr-2 mt-0.5" />
        <div class="hyphens-auto leading-5">{{ translate('Die E-Mail-Adresse stimmt nicht mit der Kundendomain überein. Soll diese trotzdem gespeichert werden? Falls die Person zu einer anderen Firma gehört, bitte bei dieser Firma hinzufügen.') }}</div>
      </div>
    </template>
    <div :class="[errors.email ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('E-Mail') }} <asterisk /></label>
      <input type="email" v-model="data.email" @blur="validateEmailDomain()">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <footer>
      <div class="mt-4 lg:mt-6">
        <button :disabled="isPending ? true : false" type="submit" class="btn-primary" @click.prevent="store()">
          {{ translate('Speichern') }}
        </button>
      </div>
    </footer>
  </form>
</div>
</template>
<script>
import { ArrowLeftIcon, ExclamationIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {
  
  components: {
    ArrowLeftIcon,
    ExclamationIcon,
    ContentHeader,
    ContentFooter,
    FormRadio,
    Required,
    Asterisk,
    NProgress,
  },

  mixins: [i18n],

  props: {
    companyUuid: String
  },

  data() {
    return {
      
      // Model
      data: {
        email: null,
        company_uuid: this.$props.companyUuid
      },

      // Validation
      errors: {
        email: false,
        company_uuid: false,
        validEmailDomain: true,
      },

      // Routes
      routes: {
        post: '/api/user/register',
        validate: '/api/user/validate/email-domain',
      },

      // State
      hasErrors: false,
      isPending: false,
    };
  },

  methods: {

    store() {

      if (this.data.email == null) {
        this.errors.email = true;
        this.$notify({ type: "danger", text: `Bitte alle mit * markierten Felder prüfen!`});
        return;
      }
      NProgress.start();
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$notify({ type: "success", text: this.translate('Benutzer erfasst') });
        this.$emit('createdUser', response.data.user);
        NProgress.done();
      }).catch(error => {
        this.hasErrors = true;
        this.errors.message = error.response.data.errors.email[0];
        NProgress.done();
      });
    },

    validateEmailDomain() {
      const data = {
        email: this.data.email,
        company_uuid: this.data.company_uuid,
      };
      this.isPending = true;
      this.hasErrors = false;
      NProgress.start();
      this.axios.post(this.routes.validate, data).then(response => {
        NProgress.done();
        this.isPending = false;
        this.errors.validEmailDomain = response.data.valid;
      });
    },

  },
};
</script>
  