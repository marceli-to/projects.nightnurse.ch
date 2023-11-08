<template>
  <div class="flex justify-start mt-2">
    <a href="javascript:;" 
      class="text-xs font-mono text-dark flex items-center no-underline hover:text-highlight"
      @click="$emit('click')">
      <plus-sm-icon class="h-4 w-4" aria-hidden="true" />
      <span class="ml-0.5 mt-0.5">{{ label }}</span>
    </a>
  </div>
</template>
<script>
import { PlusSmIcon } from "@vue-hero-icons/outline";

export default {

  components: {
    PlusSmIcon
  },

  props: {
    label: {
      type: String,
      default: null
    }
  }

}
</script>