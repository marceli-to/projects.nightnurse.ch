<template>
  <div v-if="isFetched" class="is-loaded">
    <page-header>
      <h1>Edizioni &amp; Mitglieder</h1>
    </page-header>
    <div class="content content--wide cards">
      <div class="card">
        <router-link :to="{name: 'edizioni'}">
          <h2>Edizioni</h2>
          <p>Verwaltung der Edizioni</p>
        </router-link>
      </div>
      <div class="card">
        <router-link :to="{name: 'participants'}">
          <h2>Mitglieder</h2>
          <p>Verwaltung der Mitglieder</p>
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