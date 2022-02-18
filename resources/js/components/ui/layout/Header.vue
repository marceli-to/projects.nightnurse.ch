<template>
  <header class="mb-4 sm:mb-8 lg:mb-9 pt-4 sm:pt-5 lg:pt-7 pb-2 flex items-start sm:items-center sticky top-0 bg-white z-50">
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
