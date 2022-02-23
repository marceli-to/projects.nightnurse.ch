<template>
<div v-if="isFetched">
  <content-header :title="`Willkommen <span class='text-highlight'>${user.firstname} ${user.name}</span>`"></content-header>
</div>
</template>
<script>

// Mixins
import Helpers from "@/mixins/Helpers";
import ContentHeader from "@/components/ui/layout/Header.vue";

export default {

  components: {
    ContentHeader,
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
      this.axios.get(`/api/user/authenticated`).then(response => {
        this.user = response.data;
        this.isFetched = true;
      });
    }
  }
}
</script>