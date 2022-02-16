<template>
  <div :class="`max-w-4xl sm:grid ${$props.cols} ${$props.gap}`">
    <slot />
  </div>
</template>
<script>
export default {
  props: {
    cols: {
      type: String,
      default: 'sm:grid-cols-2'
    },
    gap: {
      type: String,
      default: 'sm:gap-x-8 sm:gap-y-6'
    }
  }
}
</script>