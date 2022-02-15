<template>
<div>
  <notifications />
  <page-main>
    <page-menu :user="$store.state.user"></page-menu>
    <page-content>
      <router-view></router-view>
    </page-content>
  </page-main>
</div>
</template>

<script>
import PageMenu from '@/views/layout/Menu.vue';
import PageMain from '@/views/layout/Main.vue';
import PageContent from '@/views/layout/Content.vue';

export default {

  components: {
    PageMenu,
    PageMain,
    PageContent,
  },

  mounted() {
    this.fetchUser();
  },

  methods: {
    fetchUser() {
      if (!this.$store.state.user) {
        this.axios.get(`/api/user`).then(response => {
          this.$store.commit('user', `${response.data.firstname} ${response.data.name}`);
        });
      }
    },
  }
}
</script>