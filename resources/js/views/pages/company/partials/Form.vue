<template>
<div>
  <content-header>
    <h3 class="!mt-0">{{title}}</h3>
  </content-header>
  <form @submit.prevent="submit" v-if="isFetched">

    <div class="form-group">
      <label>Name</label>
      <input type="text" v-model="data.name">
    </div>

    <div class="form-group">
      <label>City</label>
      <input type="text" v-model="data.city">
    </div>
    
    <!-- <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'companies' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer> -->

  </form>
</div>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import ContentHeader from "@/components/ui/layout/Footer.vue";
import ContentFooter from "@/components/ui/layout/Header.vue";

export default {
  components: {
    ContentHeader,
    ContentFooter,
  },

  mixins: [ErrorHandling],

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

      // Messages
      messages: {
        created: 'Firma erfasst!',
        updated: 'Änderungen gespeichert!',
      }
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
      this.isLoading = true;
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "companies" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      });
    },

    update() {
      this.isLoading = true;
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {
        this.$router.push({ name: "companies" });
        this.$notify({ type: "success", text: this.messages.updated });
        this.isLoading = false;
      });
    },

  },

  computed: {
    title() {
      return this.$props.type == "update" ? "Firma bearbeiten"  : "Firma hinzufügen";
    }
  }
};
</script>
