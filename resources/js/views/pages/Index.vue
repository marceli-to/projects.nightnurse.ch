<template>
<div>
  <header>
    <h3 class="!mt-0 border-b-2 border-light pb-2">Clients</h3>
  </header>

  <div v-if="isFetched" class="is-loaded">
    <page-header>
      <h1>Willkommen <strong>{{user.firstname}} {{user.name}}</strong></h1>
    </page-header>
    <div class="content content--wide cards">
      <!-- <div class="card">
        <router-link :to="{name: 'events'}">
          <h2>Edizioni</h2>
          <p>Verwaltung der Edizioni &amp; Mitglieder</p>
        </router-link>
      </div> -->
    </div>
  </div>
</div>
</template>
<script>

// Mixins
import Helpers from "@/mixins/Helpers";
import PageHeader from "@/components/ui/layout/Header.vue";

export default {

  components: {
    PageHeader,
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