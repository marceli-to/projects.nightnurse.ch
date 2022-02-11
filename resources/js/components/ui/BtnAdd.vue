<template>
  <router-link :to="{ name: route, params: routeParams }" class="feather-icon feather-icon--prepend">
    <plus-icon size="16"></plus-icon>
    <span>Hinzufügen</span>
  </router-link>
</template>

<script>
import { PlusIcon } from 'vue-feather-icons';

export default {
  
  components: {
    PlusIcon,
  },

  props: {
    route: String,

    routeParams: {
      type: Object,
      default: {}
    }
  },
}
</script>

<style>

</style>