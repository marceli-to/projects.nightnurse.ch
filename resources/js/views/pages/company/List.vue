<template>
<div v-if="isFetched">

    <div v-if="data.length">

    </div>
    <div v-else>
      <p class="no-records">{{messages.emptyData}}</p>
    </div>
  
  <!-- <page-footer>
    <router-link :to="{ name: 'dashboard' }" class="btn-primary">
      <span>Zurück</span>
    </router-link>
  </page-footer> -->
    
</div>
</template>
<script>

// Components
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";

// Mixins
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    PageFooter,
    PageHeader,
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

    toggle(id,event) {
      this.isLoading = true;
      this.axios.get(`${this.routes.toggle}/${id}`).then(response => {
        const index = this.data.findIndex(x => x.id === id);
        this.data[index].publish = response.data;
        this.$notify({ type: "success", text: this.messages.updated });
        this.isLoading = false;
      });
    },

    destroy(id, event) {
      if (confirm(this.messages.confirmDestroy)) {
        this.isLoading = true;
        this.axios.delete(`${this.routes.destroy}/${id}`).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },

  }
}
</script>