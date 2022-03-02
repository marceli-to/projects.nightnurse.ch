<template>
<div v-if="isFetched">
  <content-header :title="`Willkommen <span class='text-highlight'>${$store.state.user.firstname} ${$store.state.user.name}</span>`"></content-header>
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

}
</script>