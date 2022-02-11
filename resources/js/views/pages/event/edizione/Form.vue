<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <form @submit.prevent="submit" v-if="isFetched">

    <header class="content-header">
      <h1>{{title}}</h1>
    </header>

    <tabs :tabs="tabs" :errors="errors"></tabs>
    
    <div v-show="tabs.data.active">
      <div>
        <div :class="[this.errors.number ? 'has-error' : '', 'form-row']">
          <label>Nummer *</label>
          <input type="text" v-model="event.number">
          <label-required />
        </div>

        <div class="form-row">
          <label>Titel (Spezialevent)</label>
          <input type="text" v-model="event.title">
        </div>

        <div class="form-row">
          <label>Einleitung</label>
          <textarea v-model="event.description"></textarea>
        </div>
        
        <div class="form-row">
          <label>Datum *</label>
          <the-mask
            type="text"
            mask="##.##.####"
            :masked="true"
            class="is-datetime is-datepicker"
            name="date"
            v-model="event.date"
          ></the-mask>
          <label-required />
        </div>

        <div class="form-row">
          <label>Zeit</label>
          <the-mask
            type="text"
            mask="##.##"
            :masked="true"
            name="time"
            v-model="event.time"
          ></the-mask>
        </div>

        <div class="form-row">
          <label>Ort</label>
          <textarea v-model="event.location"></textarea>
        </div>

        <div class="form-row">
          <label>Google maps</label>
          <input type="text" v-model="event.maps">
        </div>

        <div class="form-row">
          <label>Was</label>
          <input type="text" v-model="event.type">
        </div>

        <div class="form-row">
          <label>Kosten</label>
          <input type="text" v-model="event.fees">
        </div>

      </div>
    </div>
   
    <div v-show="tabs.settings.active">
      <div>
        <div class="form-row">
          <radio-button 
            :label="'Spezialevent?'"
            v-bind:special.sync="event.special"
            :model="event.special"
            :name="'special'">
          </radio-button>
        </div>
        <div class="form-row">
          <radio-button 
            :label="'Publizieren?'"
            v-bind:publish.sync="event.publish"
            :model="event.publish"
            :name="'publish'">
          </radio-button>
        </div>
        <div class="form-row">
          <radio-button 
            :label="'Ausgebucht?'"
            v-bind:fully_booked.sync="event.fully_booked"
            :model="event.fully_booked"
            :name="'fully_booked'">
          </radio-button>
        </div>
        <div class="form-row">
          <radio-button 
            :label="'Mitgliedschaft erforderlich?'"
            v-bind:members_only.sync="event.members_only"
            :model="event.members_only"
            :name="'members_only'">
          </radio-button>
        </div>
        <div class="form-row" v-if="event.members_only == 1">
          <label>Mitgliedschaftsbedingungen</label>
          <textarea v-model="event.members_restrictions_text"></textarea>
        </div>
        <div class="form-row">
          <radio-button 
            :label="'Covid Auflagen?'"
            v-bind:covid_restrictions.sync="event.covid_restrictions"
            :model="event.covid_restrictions"
            :name="'covid_restrictions'">
          </radio-button>
        </div>
        <div class="form-row" v-if="event.covid_restrictions == 1">
          <label>Covid Auflagen</label>
          <textarea v-model="event.covid_restrictions_text"></textarea>
        </div>
      </div>
    </div>
    
    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'edizioni' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer>

  </form>
</div>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import RadioButton from "@/components/ui/RadioButton.vue";
import LabelRequired from "@/components/ui/LabelRequired.vue";
import Tabs from "@/components/ui/Tabs.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";
import tabsConfig from "@/views/pages/event/edizione/config/tabs.js";
import { TheMask } from "vue-the-mask";

export default {
  components: {
    RadioButton,
    LabelRequired,
    Tabs,
    PageFooter,
    PageHeader,
    TheMask
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      event: {
        number: null,
        title: null,
        description: null,
        date: null,
        time: null,
        location: null,
        maps: null,
        special: 0,
        publish: 1,
        fully_booked: 0,
        members_only: 0,
        members_restrictions_text: null,
        covid_restrictions: 0,
        covid_restrictions_text: null,
      },

      // Validation
      errors: {
        number: false,
        date: false,
      },

      // Loading states
      isFetched: true,
      isLoading: false,

      // Tabs config
      tabs: tabsConfig,

    };
  },

  created() {
    if (this.$props.type == "edit") {
      this.isFetched = false;
      let uri = `/api/event/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.event = response.data;
        this.isFetched = true;
      });
    }
  },

  methods: {

    // Submit form
    submit() {
      if (this.$props.type == "edit") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    store() {
      this.isLoading = true;
      this.axios.post('/api/event', this.event).then(response => {
        this.$router.push({ name: "edizioni" });
        this.$notify({ type: "success", text: "Edizione erfasst!" });
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/event/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.event).then(response => {
        this.$router.push({ name: "edizioni" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

  },

  computed: {
    title() {
      return this.$props.type == "edit" 
        ? "Edizione bearbeiten" 
        : "Edizione hinzufügen";
    }
  }
};
</script>
