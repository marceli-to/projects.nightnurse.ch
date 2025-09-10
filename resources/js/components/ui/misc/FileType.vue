<template>
  <div :class="'inline-flex items-center rounded-sm p-1.5 text-white text-tiny font-mono leading-none bg-dark is-' + extension">{{extension}}</div>
</template>

<script>
export default {
  props: {
    extension: {
      type: String,
    }
  }
}
</script>
<style>
.is-jpg,
.is-jpeg,
.is-webp,
.is-gif,
.is-avif,
.is-png {
  @apply bg-gray-300;
}
.is-zip {
  @apply bg-yellow-400;
}
</style>