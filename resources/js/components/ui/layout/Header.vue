<template>
  <header class="mb-4 sm:mb-8 lg:mb-9 pt-2 sm:pt-4 pb-2 sm:pb-4 flex items-center sticky top-0 bg-white z-40 border-bottom relative -ml-[1px] pl-[1px]">
    <slot />
    <h3 class="!mt-0 !mb-0 !text-lg" v-html="title"></h3>
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
