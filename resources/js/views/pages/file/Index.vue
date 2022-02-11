<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    <header class="content-header">
      <h1>Dateien</h1>
      <router-link :to="{ name: 'file-create' }" class="feather-icon feather-icon--prepend">
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </router-link>
    </header>
    <div class="listing" v-if="files.length">
      <div
        :class="[f.publish == 0 ? 'is-disabled' : '', 'listing__item']"
        v-for="f in files"
        :key="f.id"
      >
        <div class="listing__item-body">
          {{ f.name }}
          <separator />
          {{ f.size }}
        </div>
        <list-actions 
          :id="f.id" 
          :record="f"
          :hasDownload="true"
          :routes="{edit: 'file-edit'}">
        </list-actions>
      </div>
    </div>
    <div v-else>
      <p class="no-records">Es sind noch keine Dateien vorhanden...</p>
    </div>
    <page-footer>
      <router-link :to="{ name: 'dashboard' }" class="btn-primary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
  </div>
</div>
</template>
<script>
import { PlusIcon } from 'vue-feather-icons';
import ListActions from "@/components/ui/ListActions.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    ListActions,
    PlusIcon,
    PageFooter
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {
      isLoading: false,
      isFetched: false,
      files: []
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.isLoading = true;
      this.axios.get(`/api/files`).then(response => {
        this.files = response.data.data;
        this.isFetched = true;
        this.isLoading = false;
      });
    },

    toggle(id,event) {
      let uri = `/api/file/state/${id}`;
      this.isLoading = true;
      this.axios.get(uri).then(response => {
        const index = this.files.findIndex(x => x.id === id);
        this.files[index].publish = response.data;
        this.$notify({ type: "success", text: "Status geändert" });
        this.isLoading = false;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/file/${id}`;
        this.isLoading = true;
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },
  }
}
</script>