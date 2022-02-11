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
        <input type="text" v-model="participant.firstname">
        <label-required />
      </div>
      <div :class="[this.errors.name ? 'has-error' : '', 'form-row']">
        <label>Name *</label>
        <input type="text" v-model="participant.name">
        <label-required />
      </div>
      <div :class="[this.errors.email ? 'has-error' : '', 'form-row']">
        <label>Email *</label>
        <input type="text" v-model="participant.email">
        <label-required />
      </div>        
    </div>
    <template v-if="$props.type == 'edit'">
      <header class="content-header sb-lg">
        <h1>Mitgliederbeiträge</h1>
        <a href="" @click.prevent="showForm()" class="feather-icon feather-icon--prepend">
          <plus-icon size="16"></plus-icon>
          <span>Hinzufügen</span>
        </a>
      </header>

      <template v-if="hasForm">
        <div>
          <div class="form-row">
            <input type="text" maxlength="4" v-model="year" class="is-small" placeholder="Jahr *" @blur="storeSubscription()">
          </div>
        </div>
      </template>

      <div class="listing" v-if="participant.subscriptions.length">
        <div
          class="listing__item"
          v-for="s in participant.subscriptions"
          :key="s.id"
        >
          <div class="listing__item-body">
            {{ s.year }}
          </div>
          <div class="listing__item-action">
            <div>
              <a
                href="javascript:;"
                class="feather-icon"
                @click.prevent="destroySubscription(s.id)"
              >
                <trash2-icon size="18"></trash2-icon>
              </a>
            </div>
          </div>
        </div>
      </div>
    </template>

    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'participants' }" class="btn-secondary">
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
      participant: {
        firstname: null,
        name: null,
        email: null,
      },

      // Validation
      errors: {
        firstname: false,
        name: false,
        email: false,
      },

      year: null,

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
      let uri = `/api/participant/${this.$route.params.id}`;
      this.axios.get(uri).then(response => {
        this.participant = response.data;
        this.isFetched = true;
      });
    },

    store() {
      this.isLoading = true;
      this.axios.post('/api/participant', this.participant).then(response => {
        this.$router.push({ name: "participants" });
        this.$notify({ type: "success", text: "Mitglied erfasst!" });
        this.isLoading = false;
      });
    },

    update() {
      let uri = `/api/participant/${this.$route.params.id}`;
      this.isLoading = true;
      this.axios.put(uri, this.participant).then(response => {
        this.$router.push({ name: "participants" });
        this.$notify({ type: "success", text: "Änderungen gespeichert!" });
        this.isLoading = false;
      });
    },

    storeSubscription() {
      this.isLoading = true;
      this.axios.post('/api/participant/subscription', {year: this.year, participant_id: this.participant.id}).then(response => {
        this.isLoading = false;
        this.fetch();
        this.hideForm();
      });
    },

    destroySubscription(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        this.isLoading = true;
        this.axios.delete(`/api/participant/subscription/${id}`).then(response => {
          this.isLoading = false;
          this.fetch();
        });
      }
    },

    showForm() {
      this.hasForm = true;
    },

    hideForm() {
      this.hasForm = false;
    },
  },

  computed: {
    title() {
      return this.$props.type == "edit" 
        ? "Mitglied bearbeiten" 
        : "Mitglied hinzufügen";
    }
  }
};
</script>
