<template>
  <div :class="'flex items-center p-2 text-white text-xs font-mono leading-none bg-dark is-' + extension">{{extension}}</div>
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
  @apply bg-yellow-400;
}
.is-png {
  @apply bg-green-400;
}
</style>