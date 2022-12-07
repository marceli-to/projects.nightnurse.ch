<template>
<div>
  <form @submit.prevent="submit()" v-if="isFetched">
    <content-header>
      <template #title>
        {{ title }}
      </template>
    </content-header>
    
    <div :class="[errors.number ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('Nummer') }} <asterisk /></label>
      <input type="text" v-model="data.number">
      <required :text="translate('Pflichtfeld')" />
    </div>

    <div :class="[errors.name ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('Name') }} <asterisk /></label>
      <input type="text" v-model="data.name">
      <required :text="translate('Pflichtfeld')" />
    </div>
    
    <div :class="[errors.user_id ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('Projektleiter') }} <asterisk /></label>
      <select v-model="data.user_id">
        <option value="null">{{ translate('Bitte wählen...') }}</option>
        <option :value="s.id" v-for="s in settings.staff" :key="s.id">{{s.firstname}} {{s.name}}</option>
      </select>
    </div>

    <content-grid class="mt-6 sm:mt-8">
      <div class="form-group">
        <label>{{ translate('Projektstart') }}</label>
        <the-mask
          type="text"
          mask="##.##.####"
          :masked="true"
          name="date_start"
          placeholder="dd.mm.YYYY"
          v-model="data.date_start"
        ></the-mask>
      </div>
      <div class="form-group">
        <label>{{ translate('Abgabetermin') }}</label>
        <the-mask
          type="text"
          mask="##.##.####"
          :masked="true"
          name="date_end"
          placeholder="dd.mm.YYYY"
          v-model="data.date_end"
        ></the-mask>
      </div>
    </content-grid>

    <div :class="[errors.company_id ? 'is-invalid' : '', 'form-group !mb-0']">
      <label>{{ translate('Hauptkunde') }} <asterisk /></label>
      <select v-model="data.company_id" @change="updateMainCompany($event)">
        <option value="null">{{ translate('Bitte wählen...') }}</option>
        <option :value="c.id" v-for="c in settings.companies" :key="c.id">{{c.name}}, {{c.city}}</option>
      </select>
      <div class="flex justify-end">
        <button-widget :label="translate('Kunde hinzufügen')" @click="$refs.widgetCompanyCreate.show()" />
      </div>
    </div>

    <div class="form-group !mb-0">
      <label>{{ translate('Weitere Kunden') }}</label>
      <select name="companies" @change="addCompany($event)">
        <option value="null">{{ translate('Bitte wählen...') }}</option>
        <option v-for="c in settings.companies" :key="c.id" :value="c.id">{{ c.full_name }}</option>
      </select>
      <div class="flex justify-end">
        <button-widget :label="translate('Kunde hinzufügen')" @click="$refs.widgetCompanyCreate.show()" />
      </div>
    </div>

    <div v-if="data.companies" class="form-group">
      <div class="flex flex-wrap">
        <a 
          href="javascript:;"
          class="rounded-sm inline-flex w-auto items-center bg-dark hover:bg-highlight px-3 py-2 lg:px-4 lg:py-3 text-white font-mono mr-4 mb-4 text-xs sm:text-sm no-underline"
          v-for="d in data.companies" 
          :key="d.id"
          @click.prevent="removeCompany(d.id)">
          <span class="inline-block mr-3">{{ d.full_name }}</span>
          <x-icon class="h-5 w-5" aria-hidden="true"></x-icon>
        </a>
      </div>
    </div>

    <div :class="[errors.project_state_id ? 'is-invalid' : '', 'form-group']">
      <label>{{ translate('Status') }} <asterisk /></label>
      <select v-model="data.project_state_id">
        <option value="null">{{ translate('Bitte wählen...') }}</option>
        <option :value="s.id" v-for="s in settings.states" :key="s.id">{{s.description}}</option>
      </select>
    </div>

    <div class="form-group">
      <label>{{ translate('Farbe') }} <asterisk /></label>
      <input type="color" name="color" v-model="data.color">
    </div>

    <h4>{{ translate('Zugriffsrechte') }}</h4>

    <!-- main client -->
    <div class="form-group" v-if="data.company">
      <div class="form-check mb-2">
        <input 
          type="checkbox" 
          class="checkbox" 
          :id="data.company.uuid" 
          @change="toggleAll($event, data.company.uuid)">
        <label class="inline-block text-gray-800 font-bold" :for="data.company.uuid">
          {{ data.company.full_name }}
        </label>
      </div>
      <div>
        <div v-for="user in data.company.users" :key="user.uuid" class="mb-2">
          <div class="form-check mb-1">
            <input 
              type="checkbox" 
              class="checkbox" 
              :value="user.id" 
              :id="user.uuid" 
              :ref="data.company.uuid"
              :checked="checkUser(user.id)"
              @change="toggleOne($event, user.id)">
            <label class="inline-block text-gray-800" :for="user.uuid" v-if="user.register_complete">
              {{ user.firstname }} {{ user.name }}
            </label>
            <label class="inline-block text-gray-800" :for="user.uuid" v-else>
              {{ user.email }}
            </label>
          </div>
        </div>
      </div>
      <button-widget :label="translate('Benutzer hinzufügen')" @click="showAddUserForm(data.company.uuid)" />
    </div>

    <!-- other clients -->
    <div class="form-group" v-if="data.companies.length">
      <div class="sm:grid sm:grid-cols-2 md:grid-cols-3">
        <div v-for="company in data.companies" :key="company.uuid" class="mb-4 lg:mb-8">
          <div class="form-check mb-2">
            <input 
              type="checkbox" 
              class="checkbox" 
              :id="company.uuid" 
              @change="toggleAll($event, company.uuid)">
            <label class="inline-block text-gray-800 font-bold" :for="company.uuid">
              {{ company.full_name }}
            </label>
          </div>
          <div>
            <div v-for="user in company.users" :key="user.uuid" class="mb-2">
              <div class="form-check mb-1">
                <input 
                  type="checkbox" 
                  class="checkbox" 
                  :value="user.id" 
                  :id="user.uuid" 
                  :ref="company.uuid"
                  :checked="checkUser(user.id)"
                  @change="toggleOne($event, user.id)">
                <label class="inline-block text-gray-800" :for="user.uuid" v-if="user.register_complete">
                  {{ user.firstname }} {{ user.name }}
                </label>
                <label class="inline-block text-gray-800" :for="user.uuid" v-else>
                  {{ user.email }}
                </label>
              </div>
            </div>
          </div>
          <button-widget :label="translate('Benutzer hinzufügen')" @click="showAddUserForm(company.uuid)" />
        </div>
      </div>
    </div>

    <h4>{{ translate('Projektbeteiligte') }}</h4>
    <div class="sm:grid sm:grid-cols-2 md:grid-cols-3 pb-4 sm:pb-8">
      <div v-for="user in settings.staff" :key="user.id" class="mb-2">
        <div class="form-check mb-1">
          <input 
            type="checkbox" 
            class="checkbox" 
            :value="user.id" 
            :id="user.uuid" 
            :checked="checkUser(user.id)"
            @change="toggleOne($event, user.id)">
          <label class="inline-block text-gray-800" :for="user.uuid" v-if="user.register_complete">
            {{ user.firstname }} {{ user.name }}
          </label>
          <label class="inline-block text-gray-800" :for="user.uuid" v-else>
            {{ user.email }}
          </label>
        </div>
      </div>
    </div>

    <content-footer>
      <button type="submit" class="btn-primary">{{ translate('Speichern') }}</button>
      <template v-if="$route.params.redirect">
        <router-link :to="{ name: 'messages', params: { uuid: $route.params.uuid } }" class="form-helper form-helper-footer">
          <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
          <span>{{ translate('Zurück') }}</span>
        </router-link>
      </template>
      <template v-else>
        <router-link :to="{ name: 'projects' }" class="form-helper form-helper-footer">
          <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
          <span>{{ translate('Zurück') }}</span>
        </router-link>
      </template>
    </content-footer>
  </form>

  <lightbox ref="widgetCompanyCreate">
    <company-create-widget @createdCompany="createdCompany($event)" />
  </lightbox>

  <lightbox ref="widgetUserCreate">
    <user-create-widget :companyUuid="companyUuid" @createdUser="createdUser($event)" />
  </lightbox>

</div>
</template>
<script>
import { TheMask } from "vue-the-mask";
import i18n from "@/i18n";
import NProgress from 'nprogress';
import ErrorHandling from "@/mixins/ErrorHandling";
import { XIcon, ArrowLeftIcon, PlusSmIcon } from "@vue-hero-icons/outline";
import ContentHeader from "@/components/ui/layout/Header.vue";
import ContentFooter from "@/components/ui/layout/Footer.vue";
import ContentGrid from "@/components/ui/layout/Grid.vue";
import FormRadio from "@/components/ui/form/Radio.vue";
import Required from "@/components/ui/form/Required.vue";
import Asterisk from "@/components/ui/form/Asterisk.vue";
import Lightbox from "@/components/ui/layout/Lightbox.vue";
import ButtonWidget from "@/components/ui/ButtonWidget.vue";
import CompanyCreateWidget from '@/views/pages/company/components/WidgetCreate.vue';
import UserCreateWidget from '@/views/pages/company/user/components/WidgetCreate.vue';

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
    PlusSmIcon,
    NProgress,
    Lightbox,
    CompanyCreateWidget,
    UserCreateWidget,
    ButtonWidget
  },

  mixins: [ErrorHandling, i18n],

  props: {
    type: String
  },

  data() {
    return {
      
      // Model
      data: {
        id: null,
        number: null,
        name: null,
        color: '#ff008b',
        date_start: null,
        date_end: null,
        user_id: null,
        project_state_id: 1,
        company_id: null,
        company: null,
        manager: {},
        companies: [],
        users: [],
      },

      companyUuid: null,

      // Settings
      settings: {
        staff: [],
        states: [],
        companies: [],
        owner: {},
      },

      // Validation
      errors: {
        name: false,
      },

      // Routes
      routes: {
        fetch: '/api/project',
        fetchProjectCompanies: '/api/project/companies',
        fetchCompany: '/api/company',
        post: '/api/project',
        put: '/api/project',

      },

      // States
      isFetched: true,
    };
  },

  created() {
    if (this.isUpdate()) {
      this.fetch();
    }
    this.getSettings();
  },

  methods: {

    fetch() {
      NProgress.start();
      this.isFetched = false;
      this.axios.get(`${this.routes.fetch}/${this.$route.params.uuid}`).then(response => {
        this.data = response.data;
        this.isFetched = true;
        NProgress.done();
      });
    },

    submit() {
      if (this.isUpdate()) {
        this.update();
      }

      if (this.isCreate()) {
        this.store();
      }
    },

    store() {
      NProgress.start();
      this.axios.post(this.routes.post, this.data).then(response => {
        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.translate('Projekt erfasst') });
        NProgress.done();
      });
    },

    update() {
      NProgress.start();
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {

        // Redirect if necessary
        if (this.$route.params.redirect) {
          this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
          NProgress.done();
          this.$router.push({ name: "messages", params: { uuid: this.$route.params.uuid }});
          return;
        }

        this.$router.push({ name: "projects" });
        this.$notify({ type: "success", text: this.translate('Änderungen gespeichert') });
        NProgress.done();
      });
    },

    quickSave() {
      NProgress.start();
      this.axios.put(`${this.routes.put}/${this.$route.params.uuid}`, this.data).then(response => {
        NProgress.done();
      });
    },

    getSettings() {
      NProgress.start();
      this.axios.all([
        this.axios.get(`/api/companies/clients`),
        this.axios.get(`/api/users/staff`),
        this.axios.get(`/api/project-states`),
        this.axios.get(`/api/company/owner`),
      ]).then(axios.spread((...responses) => {
        this.settings = {
          companies: responses[0].data.data,
          staff: responses[1].data.data,
          states: responses[2].data.data,
          owner: responses[3].data,
        };
        NProgress.done();
      }));
    },

    addCompany(event) {
      const id = parseInt(event.target.value);
      const idx = this.data.companies.findIndex(x => x.id === id);
      if (idx == -1 && id != "null") {
        const index = this.settings.companies.findIndex(x => x.id === id);
        if (index > -1) {
          this.data.companies.push(this.settings.companies[index]);
          if (this.isUpdate()) {
            this.quickSave();
          }
        }
      }
    },

    removeCompany(id) {

      const idx = this.data.companies.findIndex(x => x.id === id);
      
      // remove all company associated users
      this.data.companies[idx].users.forEach(user => {
        const i = this.data.users.findIndex(x => x.id === user.id)
        if (i > -1) {
          this.data.users.splice(i, 1);
        }
      });

      // remove the company
      this.data.companies.splice(idx, 1);

      if (this.isUpdate()) {
        this.quickSave();
      }
    },

    updateMainCompany(event) {
      const id = parseInt(event.target.value);
      const idx = this.settings.companies.findIndex(x => x.id === id);
      if (idx > -1) {

        // remove all company associated users
        if (this.data.company) {
          this.data.company.users.forEach(user => {
            const i = this.data.users.findIndex(x => x.id === user.id)
            if (i > -1) {
              this.data.users.splice(i, 1);
            }
          });
        }

        this.data.company_id = this.settings.companies[idx].id;
        this.data.company = this.settings.companies[idx];
      }
      else {
        this.data.company_id = null;
        this.data.company = null;
      }

      if (this.isUpdate()) {
        this.quickSave();
      }
    },

    createdCompany(company) {
      this.settings.companies.unshift(company);
      this.$refs.widgetCompanyCreate.hide();
    },

    toggleAll(event, uuid) {
      const _this = this;
      const state = event.target.checked ? true : false;
      const boxes = this.$refs[uuid];
      boxes.forEach(function(box){
        if (!box.disabled) {
          box.checked = state;
          _this.addOrRemove(state, box.value);
        }
      });
    },

    toggleOne(event, id) {
      const state = event.target.checked ? true : false;
      this.addOrRemove(state, id);
    },

    addOrRemove(state, value) {
      const idx = this.data.users.findIndex(x => x.id === parseInt(value));
      if (state == true) {
        if (idx == -1) {
          let project_user = { 'id': value };
          this.data.users.push(project_user);
        }
      }
      else {
        if (idx > -1) {
          this.data.users.splice(idx, 1);
        }
      }
    },

    checkUser(id) {
      const idx = this.data.users.findIndex(x => x.id == id);
      return idx > -1 ? true : false;
    },

    showAddUserForm(companyUuid) {
      this.companyUuid = companyUuid;
      this.$refs.widgetUserCreate.show();
    },

    createdUser(user) {
      this.$refs.widgetUserCreate.hide();

      // Main company?
      if (this.data.company && this.data.company.uuid === this.companyUuid) {
        this.data.company.users.push(user)
      }

      // Other companies?
      const index = this.data.companies.findIndex(x => x.uuid === this.companyUuid);
      if (index > -1) {
        this.data.companies[index].users.unshift(user);
      }
      this.companyUuid = null;
    },

    isUpdate() {
      return this.$props.type == "update";
    },

    isCreate() {
      return this.$props.type == "create";
    }
  },

  computed: {
    title() {
      return this.isUpdate() ? this.translate('Projekt bearbeiten') : this.translate('Projekt erstellen');
    }
  }
};
</script>
