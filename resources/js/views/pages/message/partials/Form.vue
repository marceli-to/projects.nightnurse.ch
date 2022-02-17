<template>
<div>
  <form @submit.prevent="submit" v-if="isFetched">
    
    <content-header :title="title"></content-header>
    
    <div class="form-group">
      <label>Betreff</label>
      <input type="text" v-model="data.subject">
    </div>

    <div class="form-group mt-6 lg:mt-8">
      <label class="mb-3 lg:mb-3">Nachricht</label>
      <tinymce-editor
        :api-key="tinyApiKey"
        :init="tinyConfig"
        v-model="data.body"
      ></tinymce-editor>
    </div>

    <div class="form-group">
      <form-radio 
        :label="'Private Nachricht?'"
        v-bind:private.sync="data.private"
        :model="data.private"
        :name="'private'">
      </form-radio>
    </div>

    <div class="form-group">
      <label class="mb-2">Empfänger</label>

      <div v-for="company in project.companies" :key="company.uuid">
        <div v-if="company.users.length > 0">
          <div class="form-check mb-2">
            <input 
              type="checkbox" 
              class="checkbox" 
              :id="company.uuid" 
              @change="toggleAll($event, company.uuid)">
            <label class="inline-block text-gray-800 font-bold" :for="company.uuid">
              {{ company.name }} (Alle)
            </label>
          </div>
          <div class="grid lg:grid-cols-4 mb-6">
            <div v-for="user in company.users" :key="user.uuid">
              <div class="form-check">
                <input 
                  type="checkbox" 
                  class="checkbox" 
                  :value="user.uuid" 
                  :id="user.uuid" 
                  :data-company-uuid="company.uuid"
                  @change="toggleOne($event, user.uuid)">
                <label class="inline-block text-gray-800" :for="user.uuid">
                  {{ user.firstname }} {{ user.name }}
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <content-footer>
      <button type="submit" class="btn-primary">Senden</button>
      <router-link :to="{ name: 'messages', params: { uuid: this.$route.params.uuid }}" class="form-helper form-helper-footer">
        <span>Zurück</span>
      </router-link>
    </content-footer>

  </form>
</div>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import { XIcon } from "@vue-hero-icons/outline";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import Divider from "@/components/ui/misc/Divider.vue";
import { TheMask } from "vue-the-mask";
import tinyConfig from "@/config/tiny.js";
import TinymceEditor from "@tinymce/tinymce-vue";

export default {
  components: {
    ContentHeader,
    ContentFooter,
    ContentGrid,
    FormRadio,
    Required,
    TheMask,
    Asterisk,
    Divider,
    XIcon,
    TinymceEditor
  },

  
  mixins: [ErrorHandling],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        subject: null,
        body: null,
        private: 0,
        users: [],
      },

      project: {
        number: null,
        name: null
      },

      // Settings
      settings: {
        staff: [],
        states: [],
        companies: [],
      },

      // Validation
      errors: {
        name: false,
      },

      // Routes
      routes: {
        fetch: '/api/project',
        post: '/api/message',
      },

      // States
      isFetched: true,
      isLoading: false,

      // Messages
      messages: {
        created: 'Nachricht erfasst!',
      },

      // TinyMCE
      tinyConfig: tinyConfig,
      tinyApiKey: 'vuaywur9klvlt3excnrd9xki1a5lj25v18b2j0d0nu5tbwro',
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.isFetched = false;
      this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`).then(response => {
        this.project = response.data;
        this.isFetched = true;
      });
    },

    submit() {
      this.store();
    },

    store() {
      this.isLoading = true;
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.messages.created });
        this.isLoading = false;
      });
    },

    toggleAll(event, uuid) {
      const _this = this;
      const state = event.target.checked ? true : false;
      const boxes = document.querySelectorAll('[data-company-uuid="'+uuid+'"]');
      boxes.forEach(function(box){
        box.checked = state;
        _this.addOrRemove(state, box.value);
      });
    },

    toggleOne(event, uuid) {
      const state = event.target.checked ? true : false;
      this.addOrRemove(state, uuid);
    },

    addOrRemove(state, value) {
      const idx = this.data.users.findIndex(x => x == value);
      if (state == true) {
        if (idx == -1) {
          this.data.users.push(value);
        }
      }
      else {
        if (idx > -1) {
          this.data.users.splice(idx, 1);
        }
      }
    }

  },

  computed: {
    title() {
      return `Neue Nachricht erstellen<br><span class="text-highlight">${this.project.number} – ${this.project.name}</span>`;
    }
  }
};
</script>
