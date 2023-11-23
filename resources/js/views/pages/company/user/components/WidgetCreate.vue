<template>
<div>
  <form @submit.prevent="submit">
    <content-header class="border-none !mb-2 !sm:mb-4 !pt-0">
      <template #title>
        {{ translate('Benutzer hinzufügen') }}
      </template>
    </content-header>
    <template v-if="hasErrors && errors.email">
      <div class="flex items-center py-2 px-3 mr-1 mb-4 rounded-sm bg-red-500 text-white text-xs font-mono">
        <exclamation-icon class="shrink-0 block w-6 h-auto mr-2 mt-0.5" />
        <div class="hyphens-auto leading-5">{{ translate(errors.message) }}</div>
      </div>
    </template>
    <template v-if="hasErrors && errors.domain">
      <div class="flex items-start py-2 px-3 mr-1 mb-4 rounded-sm bg-yellow-500 text-white text-xs font-mono">
        <exclamation-icon class="shrink-0 block w-6 h-auto mr-2 mt-0.5" />
        <div class="hyphens-auto leading-5">{{ translate('Die E-Mail-Adresse stimmt nicht mit der Kundendomain überein. Soll diese trotzdem gespeichert werden? Falls die Person zu einer anderen Firma gehört, bitte bei dieser Firma hinzufügen.') }}</div>
      </div>
    </template>
    <div class="form-group">
      <label>{{ translate('E-Mail') }} <asterisk /></label>
      <input type="email" v-model="data.email" @focus="clearErrors()">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <footer>
      <div class="mt-4 lg:mt-6">
        <button :disabled="isPending ? true : false" type="submit" class="btn-primary p-2 md:!py-3 md:!px-3" @click.prevent="store()">
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
import Lightbox from "@/components/ui/layout/Lightbox.vue";

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
    Lightbox
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
        company_uuid: this.$props.companyUuid,
        has_domain_confirmation: false,
      },

      // Validation
      errors: {
        email: false,
        domain: false,
        company_uuid: false,
      },

      // Routes
      routes: {
        store: '/api/user/register',
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
        this.$notify({ type: "danger", text: this.translate(`Bitte alle mit * markierten Felder prüfen!`)});
        return;
      }

      NProgress.start();
      this.isPending = true;
      this.axios.post(this.routes.store, this.data).then(response => {
        this.isPending = false;
        NProgress.done();

        if (response.data.error == 'invalid_domain') {
          this.errors.domain = true;
          this.data.has_domain_confirmation = true;
          this.hasErrors = true;
          return false;
        }
        else {
          this.$notify({ type: "success", text: this.translate('Benutzer erfasst') });
          this.$emit('createdUser', response.data.user);
        }

      }).catch(error => {
        this.errors.email = true;
        this.errors.message = error.response.data.errors.email[0];
        this.hasErrors = true;
        this.isPending = false;
        NProgress.done();
      });
    },

    clearErrors() {
      this.errors.email = false;
      this.errors.domain = false;
      this.hasErrors = false;
    }
  },
};
</script>
  