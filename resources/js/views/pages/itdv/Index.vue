<template>
  <div v-if="isFetched" class="is-loaded">
    <page-header>
      <h1>Inhalte «I Tavoli del Vagabondo»</h1>
    </page-header>
    <div class="content content--wide cards">
      <div class="card">
        <router-link :to="{name: 'articles'}">
          <h2>Texte</h2>
          <p>Verwaltung der Texte</p>
        </router-link>
      </div>
      <div class="card">
        <router-link :to="{name: 'images'}">
          <h2>Bilder</h2>
          <p>Verwaltung der Bilder</p>
        </router-link>
      </div>
      <div class="card">
        <router-link :to="{name: 'brands'}">
          <h2>Logo</h2>
          <p>Verwaltung der Logo-Varianten</p>
        </router-link>
      </div>
    </div>
    <page-footer>
      <router-link :to="{ name: 'dashboard' }" class="btn-primary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
  </div>
</template>
<script>

// Mixins
import Helpers from "@/mixins/Helpers";
import PageHeader from "@/components/ui/PageHeader.vue";
import PageFooter from "@/components/ui/PageFooter.vue";

export default {

  components: {
    PageHeader,
    PageFooter
  },


  mixins: [Helpers],

  data() {
    return {
      isFetched: false,
      user: {},
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      this.axios.get(`/api/user`).then(response => {
        this.user = response.data;
        this.isFetched = true;
      });
    }
  }
}
</script>