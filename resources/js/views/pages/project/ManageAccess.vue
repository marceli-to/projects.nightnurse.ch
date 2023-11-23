<template>
<div>
  <form @submit.prevent="update()" v-if="isFetched">
    <content-header>
      <template #title>
        {{ translate('Projekt-Zugriffe verwalten') }}
      </template>
    </content-header>

    <h4>{{ translate('Benutzer') }}</h4>
    <div class="mb-2 lg:mb-4">
      <div v-for="user in users" :key="user.uuid" class="form-check mb-2">
        <input 
          type="checkbox" 
          class="checkbox" 
          :value="user.uuid" 
          :id="user.uuid"
          :disabled="$store.state.user.uuid === user.uuid"
          :checked="user.associated ? true : false"
          @change="sync(user.uuid)">
        <label class="inline-block text-gray-800" :for="user.uuid" v-if="user.register_complete">
          {{ user.firstname }} {{ user.name }} ({{ user.email }})
        </label>
        <label class="inline-block text-gray-800" :for="user.uuid" v-else>
          {{ user.email }}
        </label>
      </div>
    </div>
    <button-widget :label="translate('Benutzer erstellen')" @click="$refs.addUser.show()" />
    <add-user ref="addUser" @stored="storedUser($event)" />

    <content-footer>
      <button type="submit" class="btn-primary">{{ translate('Speichern') }}</button>
      <router-link :to="{ name: 'projects' }" class="form-helper form-helper-footer">
        <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
        <span>{{ translate('Zurück') }}</span>
      </router-link>
    </content-footer>
  </form>

</div>
</template>
<script>
import { TheMask } from "vue-the-mask";
import { XIcon, ArrowLeftIcon, PlusSmIcon, PencilAltIcon, PencilIcon, TrashIcon, ExternalLinkIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";
import NProgress from 'nprogress';
import ErrorHandling from "@/mixins/ErrorHandling";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import ButtonWidget from "@/components/ui/ButtonWidget.vue";
import AddUser from "@/views/pages/project/components/AddUser.vue";

export default {
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    TheMask,
    Asterisk,
    XIcon,
    ArrowLeftIcon,
    PencilAltIcon,
    PlusSmIcon,
    ExternalLinkIcon,
    PencilIcon,
    TrashIcon,
    NProgress,
    ButtonWidget,
    AddUser
  },

  mixins: [ErrorHandling, i18n],

  props: {
    type: String
  },

  data() {
    return {
      
      // Users
      users: [],

      // Validation
      errors: {
      },

      // Routes
      routes: {
        fetch: '/api/project/access',
        put: '/api/project/access',
      },

      // States
      isFetched: true,
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      NProgress.start();
      this.isFetched = false;
      this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`).then(response => {
        this.users = response.data;
        this.isFetched = true;
        NProgress.done();
      });
    },

    update() {
      NProgress.start();
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, {users: this.users}).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
    },

    sync(uuid) {
      let user = this.users.find(user => user.uuid === uuid);
      user.associated = !user.associated;
    },

    storedUser(event) {
      const user = event.user;
      user.associated = true;
      this.users.push(user);
    },

  },

};
</script>
