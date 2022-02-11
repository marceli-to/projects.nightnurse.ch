<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    
    <page-header>
      <h1>Edizione</h1>
      <router-link :to="{ name: 'edizione-create' }" class="feather-icon feather-icon--prepend">
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </router-link>
    </page-header>

    <div class="listing" v-if="events.length">
      <div
        :class="[e.publish == 0 ? 'is-disabled' : '', 'listing__item']"
        v-for="e in events"
        :key="e.id"
      >
        <div class="listing__item-body">
          <pill v-if="e.special" class="is-special is-left">Special</pill>
          <em v-if="!e.special">{{ e.number }} <separator /></em> {{ e.date }} <separator v-if="e.type" /> <span v-if="e.type">{{ e.type }}</span>
          <pill v-if="e.fully_booked" class="is-danger">Ausgebucht</pill>
        </div>
        <list-actions 
          :id="e.id" 
          :record="e"
          :hasBookings="true"
          :routes="{edit: 'edizione-edit', bookings: 'edizione-bookings'}">
        </list-actions>
      </div>
    </div>
    <div v-else>
      <p class="no-records">Es sind noch keine Edizione vorhanden...</p>
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

// Icons
import { PlusIcon } from 'vue-feather-icons';

// Components
import ListActions from "@/components/ui/ListActions.vue";
import Separator from "@/components/ui/Separator.vue";
import Pill from "@/components/ui/Pill.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";

// Mixins
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
      events: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/events`).then(response => {
        this.events = response.data.data;
        this.isFetched = true;
      });
    },

    toggle(id,event) {
      let uri = `/api/event/state/${id}`;
      this.isLoading = true;
      this.axios.get(uri).then(response => {
        const index = this.events.findIndex(x => x.id === id);
        this.events[index].publish = response.data;
        this.$notify({ type: "success", text: "Status geändert" });
        this.isLoading = false;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/event/${id}`;
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