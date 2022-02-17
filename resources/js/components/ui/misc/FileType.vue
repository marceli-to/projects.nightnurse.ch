<template>
  <div :class="'flex items-center rounded-sm p-1.5 text-white text-tiny font-mono leading-none bg-dark is-' + extension">{{extension}}</div>
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
.is-jpg {
  @apply bg-yellow-500;
}
.is-png {
  @apply bg-green-500;
}
</style>