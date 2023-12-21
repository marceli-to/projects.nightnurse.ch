<template>
  <lightbox ref="lightbox" :maxWidth="'max-w-[280px] md:max-w-[480px]'">
    <div>
      <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">
        {{ translate('Benutzer erstellen') }}
      </h2>

      <template v-if="hasErrors && errors.email">
        <div class="flex items-center py-2 px-3 mr-1 mb-4 rounded-sm bg-red-500 text-white text-xs font-mono">
          <exclamation-icon class="shrink-0 block w-6 h-auto mr-2 mt-0.5" />
          <div class="hyphens-auto leading-5">{{ translate(errors.message) }}</div>
        </div>
      </template>

      <div class="form-group">
        <label>{{ translate('E-Mail') }} <asterisk /></label>
        <input type="email" v-model="email" @focus="clearErrors()">
        <required :text="translate('Pflichtfeld')" />
      </div>

      <footer>
        <div class="flex justify-between items-center mt-4 lg:mt-6">
          <button :disabled="isPending ? true : false" type="submit" class="btn-primary p-2 md:!py-3 md:!px-3" @click.prevent="store()">
            {{ translate('Speichern') }}
          </button>
        </div>
      </footer>
    </div>
  </lightbox>
</template>
<script>
import i18n from "@/i18n";
import ErrorHandling from "@/mixins/ErrorHandling";
import NProgress from 'nprogress';
import { ExclamationIcon } from "@vue-hero-icons/outline";
import Lightbox from "@/components/ui/layout/Lightbox.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import Required from "@/components/ui/form/Required.vue";

export default {

  components: {
    NProgress,
    Lightbox,
    Asterisk,
    Required,
    ExclamationIcon
  },

  mixins: [i18n],

  data() {
    return {
      
      email: null,
      hasErrors: false,

      errors: {
        email: false,
        message: null
      },

      isPending: false,

      // Routes
      routes: {
        store: '/api/employee/register',
      },
    }
  },

  methods: {

    show() {
      this.$refs.lightbox.show();
      this.reset();
    },

    store() {
      this.isPending = true;
      if (this.email == null) {
        this.isPending = false;
        this.errors.email = true;
        this.$notify({ type: "danger", text: this.translate(`Bitte alle mit * markierten Felder prüfen!`)});
        return;
      }

      NProgress.start();
      this.axios.post(this.routes.store, {email: this.email}).then(response => {
        NProgress.done();
        this.isPending = false;
        this.$emit('stored', response.data);
        this.$refs.lightbox.hide();
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
      this.hasErrors = false;
    },

    reset() {
      this.email = null;
      this.clearErrors();
    }
  }
}
</script>
w