<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <form @submit.prevent="submit" v-if="isFetched">
    <header class="content-header">
      <h1>{{title}}</h1>
    </header>
    <div>
      <div :class="[this.errors.firstname ? 'has-error' : '', 'form-row']">
        <label>Vorname *</label>
        <input type="text" v-model="subscriber.firstname">
        <label-required />
      </div>
      <div :class="[this.errors.name ? 'has-error' : '', 'form-row']">
        <label>Name *</label>
        <input type="text" v-model="subscriber.name">
        <label-required />
      </div>
      <div :class="[this.errors.email ? 'has-error' : '', 'form-row']">
        <label>Email *</label>
        <input type="text" v-model="subscriber.email">
        <label-required />
      </div> 
      <div class="form-row">
        <radio-button 
          :label="'Email bestätigt?'"
          v-bind:is_verified.sync="subscriber.is_verified"
          :model="subscriber.is_verified"
          :name="'is_verified'">
        </radio-button>
      </div>       
    </div>

    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'subscribers' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
  </form>
</div>
</template>
<script>
import { PlusIcon, Trash2Icon } from 'vue-feather-icons';
import ErrorHandling from "@/mixins/ErrorHandling";
import ListActions from "@/components/ui/ListActions.vue";
import RadioButton from "@/components/ui/RadioButton.vue";
import LabelRequired from "@/components/ui/LabelRequired.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";

export default {
  components: {
    PlusIcon,
    Trash2Icon,
    ListActions,
    RadioButton,
    LabelRequired,
    PageFooter,
    PageHeader,
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      subscriber: {
        firstname: null,
        name: null,
        email: null,
        is_verified: 0
      },

      // Validation
      errors: {
        firstname: false,
        name: false,
        email: false,
      },

      // States
      isFetched: true,
      isLoading: false,
      hasForm: false,
    };
  },

  created() {
    if (this.$props.type == "edit") {
      this.fetch();
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

    fetch() {
      this.isFetched = false;
      let uri = `/api/subscriber/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.subscriber = response.data;
        this.subscriber.is_verified = this.subscriber.verified_at ? 1 : 0;
        this.isFetched = true;
      });
    },

    store() {
      this.isLoading = true;
      this.axios.post('/api/subscriber', this.subscriber).then(response => {
        this.$router.push({ name: "subscribers" });
        this.$notify({ type: "success", text: "Subscriber erfasst!" });
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/subscriber/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.subscriber).then(response => {
        this.$router.push({ name: "subscribers" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },
  },

  computed: {
    title() {
      return this.$props.type == "edit" 
        ? "Subscriber bearbeiten" 
        : "Subscriber hinzufügen";
    }
  }
};
</script>
