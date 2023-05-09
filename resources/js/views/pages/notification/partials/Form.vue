<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header>
      <template #title>
        {{ title }}
      </template>
    </content-header>

    <div class="form-group">
      <label>{{ translate('Title') }}</label>
      <input type="text" v-model="data.title">
    </div>

    <div class="form-group">
      <label>{{ translate('Inhalt') }}</label>
      <textarea name="text" v-model="data.text"></textarea>
    </div>

    <div class="form-group">
      <label>{{ translate('Status') }}</label>
      <select v-model="data.publish">
        <option value="1">{{ translate('aktiv') }}</option>
        <option value="0">{{ translate('inaktiv') }}</option>
      </select>
    </div>
       
    <content-footer>
      <button type="submit" class="btn-primary">{{ translate('Speichern') }}</button>
      <router-link :to="{ name: 'notifications' }" class="form-helper form-helper-footer">
        <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
        <span>{{ translate('Zurück') }}</span>
      </router-link>
    </content-footer>

  </form>
</div>
</template>
<script>
import { ArrowLeftIcon } from "@vue-hero-icons/outline";
import ErrorHandling from "@/mixins/ErrorHandling";
import PageTitle from "@/mixins/PageTitle";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {
  
  components: {
    ArrowLeftIcon,
    ContentHeader,
    ContentFooter,
    NProgress
  },

  mixins: [ErrorHandling, i18n, PageTitle],

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
        fetch: '/api/notification',
        post: '/api/notification',
        put: '/api/notification'
      },

      // States
      isFetched: true,
      isLoading: false,
    };
  },

  created() {
    if (this.$props.type == "update") {
      this.fetch();
    }
    this.setPageTitle('Kunden');
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
      if (this.$props.type == "update") {
        this.update();
      }

      if (this.$props.type == "create") {
        this.store();
      }
    },

    store() {
      NProgress.start();
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "notifications" });
        this.$notify({ type: "success", text: this.translate('Notification erfasst') });
        NProgress.done();
      });
    },

    update() {
      NProgress.start();
      this.axios.put(`${this.routes.put}/${this.$route.params.id}`, this.data).then(response => {
        this.$router.push({ name: "notifications" });
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
    },
  },

  computed: {
    title() {
      return this.$props.type == "update" ? this.translate('Notification bearbeiten')  : this.translate('Notification hinzufügen');
    }
  }
};
</script>
