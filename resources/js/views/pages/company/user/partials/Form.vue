<template>
<div>
  <content-header :title="title"></content-header>
  <form @submit.prevent="submit" v-if="isFetched && isFetchedSettings" class="max-w-5xl">
    <div :class="[errors.firstname ? 'is-invalid' : '', 'form-group']">
      <label>{{translate('Vorname')}} <asterisk /></label>
      <input type="text" v-model="data.firstname">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <div :class="[errors.name ? 'is-invalid' : '', 'form-group']">
      <label>{{translate('Name')}} <asterisk /></label>
      <input type="text" v-model="data.name">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <div class="form-group">
      <label>{{translate('Telefon')}}</label>
      <input type="text" v-model="data.phone">
    </div>

    <content-grid class="mt-6 lg:mt-8">
      <div :class="[errors.gender_id ? 'is-invalid' : '', 'form-group']">
        <label>{{translate('Geschlecht')}} <asterisk /></label>
        <select v-model="data.gender_id">
          <option :value="g.id" v-for="g in settings.genders" :key="g.id">{{g.description}}</option>
        </select>
      </div>
      <div :class="[errors.language_id ? 'is-invalid' : '', 'form-group']">
        <label>{{translate('Sprache')}} <asterisk /></label>
        <select v-model="data.language_id">
          <option :value="l.id" v-for="l in settings.languages" :key="l.id">{{l.description}}</option>
        </select>
      </div>
    </content-grid>

    <div class="form-group">
      <label>{{translate('Kunde')}}</label>
      <select v-model="data.company_id">
        <option :value="c.id" v-for="c in settings.companies" :key="c.id">{{c.name}}, {{c.city}}</option>
      </select>
    </div>

    <h4 class="mb-3 lg:mb-4">{{translate('Zugangsdaten')}}</h4>
    <div :class="[errors.email ? 'is-invalid' : '', 'form-group']">
      <label>{{translate('E-Mail')}} <asterisk /></label>
      <input type="email" v-model="data.email">
      <required :text="translate('Pflichtfeld')" />
    </div>
    <div :class="[errors.password ? 'is-invalid' : '', 'form-group']">
      <label>{{translate('Passwort')}}</label>
      <input type="password" v-model="data.password" data-field-password autocomplete="off">
      <a href="javascript:;" @click.prevent="togglePassword()" class="absolute right-0 bottom-4">
        <eye-icon class="w-5 h-5 icon-list" />
      </a>
      <a 
        href="" 
        @click.prevent="generatePassword()" 
        class="absolute left-0 font-mono text-xs underline pt-2 text-gray-400 hover:text-highlight hover:no-underline">
        {{translate('Passwort vorschlagen')}}
      </a>
    </div>
    <div :class="[errors.password_confirmation ? 'is-invalid' : '', 'form-group mt-12 lg:mt-16']">
      <label>{{translate('Passwort wiederholen')}}</label>
      <input type="password" v-model="data.password_confirmation" data-field-password autocomplete="off">
    </div>
    <div :class="[errors.role_id ? 'is-invalid' : '', 'form-group']">
      <label>{{translate('Rolle')}}</label>
      <select v-model="data.role_id">
        <option :value="r.id" v-for="r in settings.roles" :key="r.id">{{r.description}}</option>
      </select>
    </div>

    <div class="form-group">
      <label>{{translate('Vertec Id')}} <asterisk /></label>
      <input type="text" v-model="data.vertec_id">
    </div>

    <content-footer>
      <button type="submit" class="btn-primary">{{translate('Speichern')}}</button>
      <router-link :to="{ name: 'users', params: { companyUuid: $route.params.companyUuid}}" class="form-helper form-helper-footer">
        <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
        <span>{{translate('Zurück')}}</span>
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
        firstname: null,
        name: null,
        phone: null,
        email: null,
        password: null,
        password_confirmation: null,
        gender_id: null,
        language_id: null,
        company_id: null,
        vertec_id: null,
      },

      // Settings
      settings: {
        genders: [],
        languages: [],
        companies: [],
        roles: [],
      },

      // Validation
      errors: {
        firstname: false,
        name: false,
        email: false,
        gender_id: false,
        language_id: false,
        company_id: false,
      },

      // Routes
      routes: {
        fetch: '/api/user',
        post: '/api/user',
        put: '/api/user'
      },

      // States
      isFetched: true,
      isFetchedSettings: true,
      isLoading: false,
    };
  },

  created() {
    if (this.$props.type == "update") {
      this.fetch();
    }
    this.getSettings();
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


      if (this.data.password && this.data.password_confirmation && (this.data.password !== this.data.password_confirmation)) {
        this.$notify({ type: "danger", text: this.translate('Passwörter stimmen nicht überein') });
        return false;
      }

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
        this.$router.push({ name: "users" });
        this.$notify({ type: "success", text: this.translate('Benutzer erfasst') });
        NProgress.done();
      });
    },

    update() {
      NProgress.start();
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {
        this.$router.push({ name: "users" });
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
    },

    getSettings() {
      this.isFetched = false;
      this.isFetchedSettings = false;
      NProgress.start();
      this.axios.all([
        this.axios.get(`/api/genders`),
        this.axios.get(`/api/languages`),
        this.axios.get(`/api/companies`),
        this.axios.get(`/api/roles`),
      ]).then(axios.spread((...responses) => {
        this.settings = {
          genders: responses[0].data.data,
          languages: responses[1].data.data,
          companies: responses[2].data.data,
          roles: responses[3].data.data,
        };

        this.isFetched = true;
        this.isFetchedSettings = true;
        NProgress.done();

        // set default company
        const index = this.settings.companies.findIndex(x => x.uuid === this.$route.params.companyUuid);
        this.data.company_id = this.settings.companies[index].id;
      }));
    },

    togglePassword() {
      const fields = document.querySelectorAll('input[data-field-password]');
      fields.forEach(function(field){
        field.type = field.type == 'password' ? 'text' : 'password';
      });
    },

    showPassword() {
      const fields = document.querySelectorAll('input[data-field-password]');
      fields.forEach(function(field){
        field.type = 'text';
      });
    },

    generatePassword() {
      const chars = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      const fields = document.querySelectorAll('input[data-field-password]');
      const length = 12;
      let pwd = "";
      for (let i = 0; i <= length; i++) {
        let randomNumber = Math.floor(Math.random() * chars.length);
        pwd += chars.substring(randomNumber, randomNumber + 1);
      }
     
      this.data.password = pwd;
      this.data.password_confirmation = pwd;

      fields.forEach(function(field){
        field.value = pwd;
      });
      this.showPassword();
    },

    resetPassword() {
      this.password = null;
      this.password_confirmation = null;
      const fields = document.querySelectorAll('input[data-field-password]');
      fields.forEach(function(field){
        field.value = '';
      });
    }
  },

  computed: {
    title() {
      return this.$props.type == "update" ? this.translate('Benutzer bearbeiten')  : this.translate('Benutzer hinzufügen');
    }
  }
};
</script>
