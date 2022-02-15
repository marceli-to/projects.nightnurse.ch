<template>
<div v-if="isFetched">

  <div v-if="data.length">
    <div v-for="d in data" :key="d.uuid">
      {{d.name}} {{ d.uuid }}
      <router-link :to="{name: 'company-update', params: { uuid: d.uuid }}">
        edit
      </router-link>
      delete
    </div>
  </div>
  <div v-else>
    <p class="no-records">
      {{messages.emptyData}}
    </p>
  </div>
  
  <!-- <page-footer>
    <router-link :to="{ name: 'dashboard' }" class="btn-primary">
      <span>Zurück</span>
    </router-link>
  </page-footer> -->
    
</div>
</template>
<script>

import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {

      // Data
      data: [],

      // Routes
      routes: {
        list: '/api/companies',
        toggle: '/api/company/state/',
        destroy: '/api/company/'
      },

      // States
      isLoading: false,
      isFetched: false,

      // Messages
      messages: {
        emptyData: 'Es sind noch keine Texte vorhanden...',
        confirmDestroy: 'Bitte löschen bestätigen!',
        updated: 'Status geändert',
      }
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(this.routes.list).then(response => {
        this.data = response.data.data;
        this.isFetched = true;
      });
    },

    toggle(uuid,event) {
      this.isLoading = true;
      this.axios.get(`${this.routes.toggle}/${uuid}`).then(response => {
        const index = this.data.findIndex(x => x.uuid === uuid);
        this.data[index].publish = response.data;
        this.$notify({ type: "success", text: this.messages.updated });
        this.isLoading = false;
      });
    },

    destroy(uuid, event) {
      if (confirm(this.messages.confirmDestroy)) {
        this.isLoading = true;
        this.axios.delete(`${this.routes.destroy}/${uuid}`).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },

  }
}
</script>