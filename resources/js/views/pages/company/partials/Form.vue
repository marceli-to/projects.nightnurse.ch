<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header :title="title"></content-header>

    <div :class="[errors.name ? 'is-invalid' : '', 'form-group']">
      <label>Name <asterisk /></label>
      <input type="text" v-model="data.name">
      <required />
    </div>

    <div class="form-group">
      <label>City</label>
      <input type="text" v-model="data.city">
    </div>
    
    <!-- <div class="form-group">
      <form-radio 
        :label="'Owner?'"
        v-bind:owner.sync="data.owner"
        :model="data.owner"
        :name="'owner'">
      </form-radio>
    </div> -->
       
    <content-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'companies' }" class="form-helper form-helper-footer">
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
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";

export default {
  components: {
    ContentHeader,
    ContentFooter,
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
      return this.$props.type == "update" ? "Kunde bearbeiten"  : "Kunde hinzufügen";
    }
  }
};
</script>
