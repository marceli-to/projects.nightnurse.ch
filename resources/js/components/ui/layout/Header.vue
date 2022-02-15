<template>
  <header class="mb-4 sm:mb-8 flex items-center">
    <slot />
    <h3 class="!mt-0 !mb-0">
      {{title}}
    </h3>
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
