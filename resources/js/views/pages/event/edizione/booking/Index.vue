<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    
    <page-header>
      <h1>Buchungen für Edizione N° {{ event.number }}</h1>
    </page-header>

    <div class="listing" v-if="event.bookings">
      <div
        class="listing__item"
        v-for="b in event.bookings"
        :key="b.id"
      >
        <div class="listing__item-body">
          <div 
            v-for="p in b.participants"
            :key="p.id"
          >
            <span v-if="p.pivot.is_main">
              {{ p.firstname }} {{ p.name }}, {{ p.email }} 
              <em v-if="b.participants.length > 1">
                (+{{b.participants.length - 1}})
              </em>
            </span>
          </div>
          <pill class="is-paid" v-if="b.is_paid == 1">bezahlt</pill>
          <pill class="is-open" v-else>offen</pill>
        </div>
        <list-actions 
          :id="b.id" 
          :record="b"
          :hasEdit="false"
          :hasToggle="false"
          :hasParticipants="true"
          :hasBilling="true"
          :routes="{participants: 'edizione-booking-participants'}">
        </list-actions>
      </div>
    </div>
    <div v-else>
      <p class="no-records">Es hat sich noch niemand angemeldet...</p>
    </div>
    <div v-if="totalParticipants">
      Total: {{ totalParticipants }} Teilnehmer
    </div>
    <page-footer>
      <router-link :to="{ name: 'edizioni' }" class="btn-primary">
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
      event: [],
      totalParticipants: 0
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/bookings/${this.$route.params.id}`).then(response => {
        this.event = response.data;
        this.setParticipantsCount();
        this.isFetched = true;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        this.isLoading = true;
        this.axios.delete(`/api/booking/${id}`).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },

    updateBilling(id) {
      if (confirm("Bitte Statusänderung bestätigen!")) {
        this.isLoading = true;
        this.axios.get(`/api/booking/update/billing/${id}`).then(response => {
          this.$notify({ type: "success", text: "Status geändert!" });
          this.fetch();
          this.isLoading = false;
        });
      }
    },

    setParticipantsCount() {
      const _this = this;
      this.event.bookings.forEach(function(booking){
        _this.totalParticipants = _this.totalParticipants + booking.participants.length;
      });
    }
  },

}
</script>