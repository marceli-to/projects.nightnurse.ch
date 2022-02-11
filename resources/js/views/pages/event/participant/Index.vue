<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    
    <page-header>
      <h1>Mitglieder</h1>
      <router-link :to="{ name: 'participant-create' }" class="feather-icon feather-icon--prepend">
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </router-link>
    </page-header>

    <div class="listing" v-if="participants.length">
      <div
        :class="[p.publish == 0 ? 'is-disabled' : '', 'listing__item']"
        v-for="p in participants"
        :key="p.id"
      >
        <div class="listing__item-body">
          {{ p.firstname }} {{ p.name }} <separator /> {{ p.email }}
        </div>
        <list-actions 
          :id="p.id" 
          :record="p"
          :hasEdit="true"
          :hasToggle="false"
          :routes="{edit: 'participant-edit'}">
        </list-actions>
      </div>
    </div>
    <div v-else>
      <p class="no-records">Es hat sich noch niemand angemeldet...</p>
    </div>
    <page-footer>
      <router-link :to="{ name: 'events' }" class="btn-primary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
    
  </div>
</div>
</template>
<script>
import { PlusIcon } from 'vue-feather-icons';
import ListActions from "@/components/ui/ListActions.vue";
import Separator from "@/components/ui/Separator.vue";
import Pill from "@/components/ui/Pill.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    ListActions,
    Separator,
    PlusIcon,
    PageFooter,
    PageHeader,
    Pill
  },

  mixins:
    Separator [ErrorHandling, Helpers],

  data() {
    return {
      isLoading: false,
      isFetched: false,
      participants: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/participants`).then(response => {
        this.participants = response.data.data;
        this.isFetched = true;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        this.isLoading = true;
        this.axios.delete(`/api/participant/${id}`).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },
  }
}
</script>