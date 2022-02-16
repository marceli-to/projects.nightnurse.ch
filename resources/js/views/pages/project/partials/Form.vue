<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header :title="title"></content-header>
    
    <div class="form-group">
      <label>Nummer *</label>
      <input type="text" v-model="data.number">
      <required />
    </div>

    <div class="form-group">
      <label>Name *</label>
      <input type="text" v-model="data.name">
      <required />
    </div>
    
    <div class="form-group">
      <label>Projektleiter</label>
      <select v-model="data.user_id">
        <option :value="s.id" v-for="s in settings.staff" :key="s.id">{{s.firstname}} {{s.name}}</option>
      </select>
    </div>

    <div class="form-group">
      <label>Hauptkunde</label>
      <select v-model="data.company_id">
        <option :value="c.id" v-for="c in settings.companies" :key="c.id">{{c.name}}, {{c.city}}</option>
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

    <div class="form-group">
      <label>Status</label>
      <select v-model="data.project_state_id">
        <option :value="s.id" v-for="s in settings.states" :key="s.id">{{s.description}}</option>
      </select>
    </div>

    <template v-if="showSelector">
      <company-selector
        v-bind:companies.sync="data.companies"
        :projectId="data.id"
        :label="'Weitere Kunden hinufügen'"
        :labelSelected="'Kunden'"
        :data="data.companies"
      ></company-selector>
    </template>

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
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import CompanySelector from '@/views/pages/company/components/Selector.vue';

import { TheMask } from "vue-the-mask";

export default {
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    TheMask,
    CompanySelector
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        number: null,
        name: null,
        date_start: null,
        date_end: null,
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
      showSelector: false,

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
        this.showSelector = true;
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

    addCompany(company) {
      this.data.companies.push(company);
    },

    removeCompany(id) {
      const idx = this.data.companies.findIndex(x => x.id === id);
      console.log(idx);
      this.data.companies.splice(idx, 1);
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

  },

  computed: {
    title() {
      return this.$props.type == "update" ? "Projekt bearbeiten"  : "Projekt hinzufügen";
    }
  }
};
</script>
