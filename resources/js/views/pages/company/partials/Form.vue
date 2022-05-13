<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header :title="title"></content-header>

    <div :class="[errors.name ? 'is-invalid' : '', 'form-group']">
      <label>{{translate('Name')}} <asterisk /></label>
      <input type="text" v-model="data.name">
      <required :text="translate('Pflichtfeld')" />
    </div>

    <div class="form-group">
      <label>{{translate('Ort')}}</label>
      <input type="text" v-model="data.city">
    </div>
       
    <content-footer>
      <button type="submit" class="btn-primary">{{translate('Speichern')}}</button>
      <router-link :to="{ name: 'companies' }" class="form-helper form-helper-footer">
        <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
        <span>{{translate('Zurück')}}</span>
      </router-link>
    </content-footer>

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
        name: null,
        city: null,
        owner: 0,
        publish: 1,
      },

      // Validation
      errors: {
        title: false,
      },

      // Routes
      routes: {
        fetch: '/api/company',
        post: '/api/company',
        put: '/api/company'
      },

      // States
      isFetched: true,
      isLoading: false,
    };
  },

  created() {
    if (this.$props.type == "update") {
      this.fetch();
    }
  },

  methods: {

    fetch() {
      this.isFetched = false;
      this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`).then(response => {
        this.data = response.data;
        this.isFetched = true;
      });
    },

    submit() {
      if (this.$props.type == "update") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    store() {
      NProgress.start();
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "companies" });
        this.$notify({ type: "success", text: this.translate('Firma erfasst') });
        NProgress.done();
      });
    },

    update() {
      NProgress.start();
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {
        this.$router.push({ name: "companies" });
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
    },

  },

  computed: {
    title() {
      return this.$props.type == "update" ? this.translate('Kunde bearbeiten')  : this.translate('Kunde hinzufügen');
    }
  }
};
</script>
