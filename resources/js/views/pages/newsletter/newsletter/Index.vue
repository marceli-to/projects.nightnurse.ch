<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    
    <div class="widget is-overlay" v-if="hasDialog">
      <div>
        <a href="" class="feather-icon btn-close" @click.prevent="hideDialog()">
          <x-icon size="24"></x-icon>
        </a>
        <form class="sb-sm">
          <div>
            <label>Newsletter als Test an:</label>
            <input type="email" class="sa-sm" v-model="mailRecipient">
            <button type="submit" class="btn-secondary" @click.prevent="sendTestmail()">Senden</button>
          </div>
          <hr>
          <div>
            <label class="sa-sm">Newsletter an alle <span class="fs-lg text-danger">{{subscribers.length}}</span> Subscriber(s):</label>
            <button type="submit" class="btn-primary" v-if="!isConfirmed" @click.prevent="isConfirmed = true">Senden</button>
            <button type="submit" class="btn-danger" v-if="isConfirmed" @click.prevent="queueSubscribers()">Sicher?</button>
          </div>
        </form>
      </div>
    </div>

    <page-header>
      <h1>Newsletter</h1>
      <router-link :to="{ name: 'newsletter-create' }" class="feather-icon feather-icon--prepend">
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </router-link>
    </page-header>

    <div class="listing" v-if="newsletters.length">
      <div
        :class="[n.publish == 0 ? 'is-disabled' : '', 'listing__item']"
        v-for="n in newsletters"
        :key="n.id"
      >
        <div class="listing__item-body">
          {{n.id}} <separator /> {{ n.text_intro | truncate(50, '...') }}
          <pill class="is-queued" v-if="n.state == 'queued'">{{n.state}}</pill>
          <pill class="is-processed" v-if="n.state == 'processed'">{{n.state}}</pill>
        </div>
        <list-actions 
          :id="n.id" 
          :record="n"
          :hasDraggable="false"
          :hasToggle="false"
          :hasMail="true"
          :hasClearQueue="n.state == 'queued' ? true : false"
          :routes="{edit: 'newsletter-edit'}">
        </list-actions>
      </div>
    </div>
    <div v-else>
      <p class="no-records">Es sind noch keine Newsletter vorhanden...</p>
    </div>
    
    <page-footer>
      <router-link :to="{ name: 'newsletters' }" class="btn-primary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
    
  </div>
</div>
</template>
<script>

// Icons
import { PlusIcon, XIcon } from 'vue-feather-icons';

// Components
import ListActions from "@/components/ui/ListActions.vue";
import Separator from "@/components/ui/Separator.vue";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";
import Pill from "@/components/ui/Pill.vue";

// Mixins
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    ListActions,
    Separator,
    PlusIcon,
    XIcon,
    PageFooter,
    PageHeader,
    Pill
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {
      mailType: false,
      mailRecipient: null,
      subscribers: [],

      hasDialog: false,
      isConfirmed: false,
      isLoading: false,
      isFetched: false,

      newsletters: [],
      newsletterId: null,
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/newsletters`).then(response => {
        this.newsletters = response.data.data;
        this.isFetched = true;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/newsletter/${id}`;
        this.isLoading = true;
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },

    maildialog(id) {
      this.isLoading = true;
      this.newsletterId = id;
      this.axios.get(`/api/subscribers`).then(response => {
        this.subscribers = response.data.data;
        this.isLoading = false;
        this.showDialog();
      });
    },

    showDialog() {
      this.hasDialog = true;
    },

    hideDialog() {
      this.newsletterId = null;
      this.mailRecipient = null;
      this.isConfirmed = false;
      this.hasDialog = false;
    },

    sendTestmail() {
      this.isLoading = true;
      this.axios.get(`/api/newsletter/testmail/${this.newsletterId}/${this.mailRecipient}`).then(response => {
        this.$notify({ type: "success", text: "Testnewsletter versendet!" });
        this.hideDialog();
        this.isLoading = false;
      });
    },

    queueSubscribers() {
      this.isLoading = true;
      this.axios.get(`/api/newsletter/queue/${this.newsletterId}`).then(response => {
        this.$notify({ type: "success", text: "Newsletterversand gestartet!" });
        this.hideDialog();
        this.fetch();
        this.isLoading = false;
      });
    },

    clearQueue(id) {
      this.isLoading = true;
      this.axios.delete(`/api/newsletter/queue/clear/${id}`).then(response => {
        this.$notify({ type: "success", text: "Newsletterqueue geleert!" });
        this.fetch();
        this.isLoading = false;
      });
    }
  }
}
</script>
<style>
hr {
  margin-top: 35px !important;
  margin-bottom: 20px !important;
}
</style>