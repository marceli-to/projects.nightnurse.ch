<template>
  <header class="mb-4 sm:mb-8 lg:mb-9 flex items-start sm:items-center">
    <slot />
    <h3 class="!mt-0 !mb-0" v-html="title"></h3>
  </header>
</template>
<script>
export default {
  props: {
    title: {
      type: String,
      default: ''
    }
  }
}
</script>
