<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">


    
    <page-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'companies' }" class="btn-secondary">
        <span>Zurück</span>
      </router-link>
    </page-footer>

  </form>
</div>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";

export default {
  components: {
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
      data: {
        title: null,
        text: null,
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
    if (this.$props.type == "edit") {
      this.fetch();
    }
  },

  methods: {

    fetch() {
      this.isFetched = false;
      this.axios.get(`${this.routes.fetch}/${this.$route.params.id}`).then(response => {
        this.data = response.data;
        this.isFetched = true;
      });
    },

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
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "companies" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      });
    },

    update() {
      this.isLoading = true;
      this.axios.put(`${this.routes.put}/${this.$route.params.id}`, this.data).then(response => {
        this.$router.push({ name: "companies" });
        this.$notify({ type: "success", text: this.messages.updated });
        this.isLoading = false;
      });
    },

  },

  computed: {
    title() {
      return this.$props.type == "edit" ? "Firma bearbeiten"  : "Firma hinzufügen";
    }
  }
};
</script>
