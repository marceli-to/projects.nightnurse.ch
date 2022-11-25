<template>
<div>
  <form @submit.prevent="submit">
    <content-header class="border-none !mb-2 !sm:mb-4 !pt-0">
      <template #title>
        {{ translate('Kunde hinzufügen') }}
      </template>
    </content-header>
    <div :class="[errors.name ? 'is-invalid' : '', 'form-group mb-1']">
      <label>{{ translate('Name') }} <asterisk /></label>
      <input type="text" v-model="data.name">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <div class="form-group">
      <label>{{ translate('Ort') }}</label>
      <input type="text" v-model="data.city">
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
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        name: null,
        city: null,
        owner: 0,
        publish: 1,
      },

      // Validation
      errors: {
        name: false,
      },

      // Routes
      routes: {
        fetch: '/api/company',
        post: '/api/company',
        put: '/api/company'
      },
    };
  },

  methods: {

    store() {

      if (this.data.name == null) {
        this.errors.name = true;
        this.$notify({ type: "danger", text: `Bitte alle mit * markierten Felder prüfen!`});
        return;
      }
      NProgress.start();
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$notify({ type: "success", text: this.translate('Firma erfasst') });
        this.$emit('createdCompany', response.data.company);
        NProgress.done();
      });
    },

  },
};
</script>
  