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
    <div :class="[errors.email ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('E-Mail') }} <asterisk /></label>
      <input type="email" v-model="data.email">
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
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {
  
  components: {
    ArrowLeftIcon,
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
      },

      // Routes
      routes: {
        post: '/api/user/register',
      },

      // State
      hasErrors: false,
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

  },
};
</script>
  