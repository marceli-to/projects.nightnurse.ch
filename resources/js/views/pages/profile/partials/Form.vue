<template>
<div>
  <content-header :title="'Profil bearbeiten'"></content-header>
  <form @submit.prevent="submit" v-if="isFetched && isFetchedSettings" class="max-width-content">
    <div :class="[errors.firstname ? 'is-invalid' : '', 'form-group']">
      <label>Vorname <asterisk /></label>
      <input type="text" v-model="data.firstname">
      <required />
    </div>
    <div :class="[errors.name ? 'is-invalid' : '', 'form-group']">
      <label>Name <asterisk /></label>
      <input type="text" v-model="data.name">
      <required />
    </div>
    <div class="form-group">
      <label>Telefon</label>
      <input type="text" v-model="data.phone">
    </div>

    <content-grid class="mt-6 lg:mt-8">
      <div :class="[errors.gender_id ? 'is-invalid' : '', 'form-group']">
        <label>Geschlecht <asterisk /></label>
        <select v-model="data.gender_id">
          <option :value="g.id" v-for="g in settings.genders" :key="g.id">{{g.description}}</option>
        </select>
      </div>
      <div :class="[errors.language_id ? 'is-invalid' : '', 'form-group']">
        <label>Sprache <asterisk /></label>
        <select v-model="data.language_id">
          <option :value="l.id" v-for="l in settings.languages" :key="l.id">{{l.description}}</option>
        </select>
      </div>
    </content-grid>

    <h4 class="mb-3 lg:mb-4">Zugangsdaten</h4>
    <div :class="[errors.email ? 'is-invalid' : '', 'form-group']">
      <label>E-Mail <asterisk /></label>
      <input type="email" v-model="data.email">
      <required />
    </div>

    <div :class="[errors.password ? 'is-invalid' : '', 'form-group']">
      <label>Passwort (min. 8 Zeichen)</label>
      <input type="password" v-model="data.password" data-field-password>
      <a href="javascript:;" @click.prevent="togglePassword()" class="absolute right-0 bottom-8">
        <eye-icon class="w-5 h-5 icon-list" />
      </a>
      <a 
        href="" 
        @click.prevent="generatePassword()" 
        class="absolute left-0 font-mono text-xs underline pt-2 text-gray-400 hover:text-highlight hover:no-underline">
        Passwort vorschlagen
      </a>
    </div>
    <div :class="[errors.password ? 'is-invalid' : '', 'form-group mt-12 lg:mt-16']">
      <label>Passwort wiederholen</label>
      <input type="password" v-model="data.password_confirmation" data-field-password>
    </div>
    <content-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'projects' }" class="form-helper form-helper-footer">
        <span>Zurück</span>
      </router-link>
    </content-footer>
  </form>
</div>
</template>
<script>
import { EyeIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";

export default {
  components: {
    EyeIcon,
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    Asterisk
  },

  mixins: [ErrorHandling],

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
      },

      // Settings
      settings: {
        genders: [],
        languages: [],
      },

      // Validation
      errors: {
        firstname: false,
        name: false,
        email: false,
        gender_id: false,
        language_id: false,
        password: false,
      },

      // Routes
      routes: {
        fetch: '/api/profile',
        put: '/api/profile'
      },

      // States
      isFetched: true,
      isFetchedSettings: true,
      isLoading: false,

      // Messages
      messages: {
        created: 'Benutzer erfasst!',
        updated: 'Änderungen gespeichert!',
        password_match_error: 'Passwörter stimmen nicht überein!'
      }
    };
  },

  created() {
    this.fetch();
    this.getSettings();
  },

  methods: {

    fetch() {
      this.isFetched = false;
      this.axios.get(`${this.routes.fetch}`).then(response => {
        this.data = response.data;
        this.isFetched = true;
      });
    },

    submit() {

      if (this.data.password && this.data.password_confirmation && (this.data.password !== this.data.password_confirmation)) {
        this.$notify({ type: "danger", text: this.messages.password_match_error });
        return false;
      }

      this.update();
    },

    update() {
      this.isLoading = true;
      this.axios.put(`${this.routes.put}`, this.data).then(response => {
        this.$notify({ type: "success", text: this.messages.updated });
        this.resetPassword();
        this.isLoading = false;
      });
    },

    getSettings() {
      this.isFetched = false;
      this.isFetchedSettings = false;
      this.isLoading = true;
      this.axios.all([
        this.axios.get(`/api/genders`),
        this.axios.get(`/api/languages`),
      ]).then(axios.spread((...responses) => {
        this.settings = {
          genders: responses[0].data.data,
          languages: responses[1].data.data,
        };
        this.isFetched = true;
        this.isFetchedSettings = true;
        this.isLoading = false;
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
};
</script>