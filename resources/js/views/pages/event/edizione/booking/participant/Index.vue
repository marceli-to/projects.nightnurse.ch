<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    
    <page-header>
      <h1>Buchung N° {{ booking.id }}</h1>
    </page-header>

    <div class="listing" v-if="booking.participants">
      <div
        :class="[p.publish == 0 ? 'is-disabled' : '', 'listing__item']"
        v-for="p in booking.participants"
        :key="p.id"
      >
        <div class="listing__item-body">
          {{ p.firstname }} {{ p.name }} <separator /> {{ p.email }}
        </div>
        <list-actions 
          :id="p.pivot.id" 
          :record="p"
          :hasEdit="false"
          :hasToggle="false">
        </list-actions>
      </div>
    </div>
    <div v-else>
      <p class="no-records">Es hat sich noch niemand angemeldet...</p>
    </div>
    <page-footer>
      <router-link :to="{ name: 'edizione-bookings', params: {id: booking.event.id } }" class="btn-primary">
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
      booking: null,
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/booking/participants/${this.$route.params.id}`).then(response => {
        this.booking = response.data;
        this.isFetched = true;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        this.isLoading = true;
        this.axios.delete(`/api/booking/participant/${id}`).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },
  }
}
</script>