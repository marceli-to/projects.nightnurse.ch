<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header :title="title"></content-header>
    
    <div :class="[errors.number ? 'is-invalid' : '', 'form-group']">
      <label>Nummer <asterisk /></label>
      <input type="text" v-model="data.number">
      <required />
    </div>

    <div :class="[errors.name ? 'is-invalid' : '', 'form-group']">
      <label>Name <asterisk /></label>
      <input type="text" v-model="data.name">
      <required />
    </div>
    
    <div :class="[errors.user_id ? 'is-invalid' : '', 'form-group']">
      <label>Projektleiter <asterisk /></label>
      <select v-model="data.user_id">
        <option value="null">Bitte wählen...</option>
        <option :value="s.id" v-for="s in settings.staff" :key="s.id">{{s.firstname}} {{s.name}}</option>
      </select>
    </div>

    <content-grid class="mt-6 sm:mt-8">
      <div class="form-group">
        <label>Projektstart</label>
        <the-mask
          type="text"
          mask="##.##.####"
          :masked="true"
          name="date_start"
          placeholder="dd.mm.YYYY"
          v-model="data.date_start"
        ></the-mask>
      </div>
      <div class="form-group">
        <label>Abgabetermin</label>
        <the-mask
          type="text"
          mask="##.##.####"
          :masked="true"
          name="date_end"
          placeholder="dd.mm.YYYY"
          v-model="data.date_end"
        ></the-mask>
      </div>
    </content-grid>

    <div :class="[errors.company_id ? 'is-invalid' : '', 'form-group']">
      <label>Hauptkunde <asterisk /></label>
      <select v-model="data.company_id">
        <option value="null">Bitte wählen...</option>
        <option :value="c.id" v-for="c in settings.companies" :key="c.id">{{c.name}}, {{c.city}}</option>
      </select>
    </div>

    <div class="form-group">
      <label>Weitere Kunden</label>
      <select name="companies" @change="addCompany($event)">
        <option value="null">Bitte wählen...</option>
        <option v-for="c in settings.companies" :key="c.id" :value="c.id">{{ c.full_name }}</option>
      </select>
    </div>

    <div v-if="data.companies" class="form-group">
      <div class="flex flex-wrap">
        <a 
          href="javascript:;"
          class="rounded-sm inline-flex w-auto items-center bg-dark hover:bg-highlight px-3 py-2 lg:px-4 lg:py-3 text-white font-mono mr-4 mb-4 text-xs sm:text-sm no-underline"
          v-for="d in data.companies" 
          :key="d.id"
          @click.prevent="removeCompany(d.id)">
          <span class="inline-block mr-3">{{ d.full_name }}</span>
          <x-icon class="h-5 w-5" aria-hidden="true"></x-icon>
        </a>
      </div>
    </div>

    <div :class="[errors.project_state_id ? 'is-invalid' : '', 'form-group']">
      <label>Status <asterisk /></label>
      <select v-model="data.project_state_id">
        <option value="null">Bitte wählen...</option>
        <option :value="s.id" v-for="s in settings.states" :key="s.id">{{s.description}}</option>
      </select>
    </div>

    <div class="form-group">
      <label>Farbe <asterisk /></label>
      <input type="color" name="color" v-model="data.color">
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
import ErrorHandling from "@/mixins/ErrorHandling";
import { XIcon } from "@vue-hero-icons/outline";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import { TheMask } from "vue-the-mask";
import i18n from "@/i18n";

export default {
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    TheMask,
    Asterisk,
    XIcon
  },

  mixins: [ErrorHandling, i18n],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        number: null,
        name: null,
        color: '#ff008b',
        date_start: null,
        date_end: null,
        user_id: null,
        project_state_id: 1,
        company_id: null,
        companies: [],
      },

      // Settings
      settings: {
        staff: [],
        states: [],
        companies: [],
      },

      // Validation
      errors: {
        name: false,
      },

      // Routes
      routes: {
        fetch: '/api/project',
        post: '/api/project',
        put: '/api/project'
      },

      // States
      isFetched: true,
      isLoading: false,

      // Messages
      messages: {
        created: 'Projekt erfasst!',
        updated: 'Änderungen gespeichert!',
      }
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
      if (this.$props.type == "update") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    store() {
      this.isLoading = true;
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      });
    },

    update() {
      this.isLoading = true;
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.messages.updated });
        this.isLoading = false;
      });
    },

    getSettings() {
      this.isFetched = false;
      this.isLoading = true;
      this.axios.all([
        this.axios.get(`/api/companies/clients`),
        this.axios.get(`/api/users/staff`),
        this.axios.get(`/api/project-states`),
      ]).then(axios.spread((...responses) => {
        this.settings = {
          companies: responses[0].data.data,
          staff: responses[1].data.data,
          states: responses[2].data.data,
        };
        this.isFetched = true;
        this.isLoading = true;
      }));
    },

    addCompany(event) {
      let target = event.target, id = target.value, name = target.options[target.selectedIndex].innerHTML;
      const idx = this.data.companies.findIndex(x => x.id === parseInt(id));
      if (idx == -1 && id != "null") {
        let d = { id: parseInt(id), full_name: name };
        this.data.companies.push(d);
      }
    },

    removeCompany(id) {
      const idx = this.data.companies.findIndex(x => x.id === id);
      this.data.companies.splice(idx, 1);
    },

  },

  computed: {
    title() {
      return this.$props.type == "update" ? this.translate('Projekt bearbeiten') : this.translate('Projekt erstellen');
    }
  }
};
</script>
