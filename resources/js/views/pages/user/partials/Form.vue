<template>
<div>
  <content-header :title="title"></content-header>
  <form @submit.prevent="submit" v-if="isFetched && isFetchedSettings" class="max-w-4xl">
    <div class="form-group">
      <label>Vorname *</label>
      <input type="text" v-model="data.firstname">
      <required />
    </div>
    <div class="form-group">
      <label>Name *</label>
      <input type="text" v-model="data.name">
      <required />
    </div>
    <div class="form-group">
      <label>Telefon</label>
      <input type="text" v-model="data.phone">
    </div>

    <content-grid class="mt-6 lg:mt-8">
      <div class="form-group">
        <label>Geschlecht</label>
        <select v-model="data.gender_id">
          <option :value="g.id" v-for="g in settings.genders" :key="g.id">{{g.description}}</option>
        </select>
      </div>
      <div class="form-group">
        <label>Sprache</label>
        <select v-model="data.language_id">
          <option :value="l.id" v-for="l in settings.languages" :key="l.id">{{l.description}}</option>
        </select>
      </div>
    </content-grid>

    <div class="form-group">
      <label>Kunde</label>
      <select v-model="data.company_id">
        <option :value="c.id" v-for="c in settings.companies" :key="c.id">{{c.name}}, {{c.city}}</option>
      </select>
    </div>
    <template v-if="$props.type == 'create'">
      <h3 class="mb-6 lg:mb-8">Zugangsdaten</h3>
      <div class="form-group">
        <label>E-Mail *</label>
        <input type="email" v-model="data.email">
        <required />
      </div>
      <div class="form-group">
        <label>Passwort *</label>
        <input type="password" v-model="data.password">
        <required />
      </div>

      <div class="form-group">
        <label>Passwort wiederholen *</label>
        <input type="password" v-model="data.password_confirmation">
        <required />
      </div>

      <div class="form-group">
        <label>Rolle</label>
        <select v-model="data.role_id">
          <option :value="r.id" v-for="r in settings.roles" :key="r.id">{{r.description}}</option>
        </select>
      </div>
    </template>

    <content-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'users' }" class="form-helper form-helper-footer">
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

export default {
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required
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
        company_id: null
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

      // Messages
      messages: {
        created: 'Benutzer erfasst!',
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
        this.$router.push({ name: "users" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      });
    },

    update() {
      this.isLoading = true;
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {
        this.$router.push({ name: "users" });
        this.$notify({ type: "success", text: this.messages.updated });
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
        this.isLoading = false;
      }));
    },
  },

  computed: {
    title() {
      return this.$props.type == "update" ? "Benutzer bearbeiten"  : "Benutzer hinzufügen";
    }
  }
};
</script>
