<template>
<div>
  <loader v-if="isLoading" />
  <content-header :title="title"></content-header>
  <form @submit.prevent="submit" v-if="isFetched" class="max-width-content">
    <div v-if="hasErrors" class="text-sm font-mono mb-2 text-red-500">
      {{errors.message}}
    </div>
    <div :class="[errors.email ? 'is-invalid' : '', 'form-group']">
      <label>E-Mail <asterisk /></label>
      <input type="email" v-model="data.email">
      <required />
    </div>
    <content-footer>
      <button type="submit" class="btn-primary">Speichern</button>
      <router-link :to="{ name: 'users', params: { companyUuid: $route.params.companyUuid}}" class="form-helper form-helper-footer">
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
import Loader from "@/components/ui/LoadingIndicator.vue";

export default {
  components: {
    EyeIcon,
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    Asterisk,
    Loader
  },

  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        email: null,
        company_uuid: this.$route.params.companyUuid
      },

      // Validation
      errors: {
        email: false,
        message: null,
      },

      // Routes
      routes: {
        post: '/api/user/register',
      },

      // States
      isFetched: true,
      isLoading: false,
      hasErrors: false,

      // Messages
      messages: {
        created: 'Benutzer erfasst!',
      }
    };
  },

  created() {
  },

  methods: {

    submit() {
      this.isLoading = true;
      this.hasErrors = false;
      this.errors.message = null;
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "users" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      })
      .catch(error => {
        this.hasErrors = true;
        this.isLoading = false;
        this.errors.message = error.response.data.errors.email[0];
      });


    },
  },

  computed: {
    title() {
      return "Benutzer erfassen";
    }
  }
};
</script>
