<template>
<header class="mb-4 sm:mb-6 pt-3 sm:pt-4 pb-2 sm:pb-4 sticky top-0 bg-white z-40 flex items-center border-bottom relative -ml-[1px] pl-[1px]">
  <template v-if="$slots.icon">
    <slot name="icon" />
  </template>
  <template v-if="$slots.title">
    <h3 class="!mt-0 !mb-0 !text-lg w-full">
      <slot name="title" />
    </h3>
  </template>
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
