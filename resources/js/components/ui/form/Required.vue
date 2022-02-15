<template>
  <div class="font-mono text-xs text-gray-400 text-right w-full pt-2">{{text}}</div>
</template>
<script>
export default {
  props: {
    text: {
      type: String,
      default: 'Pflichtfeld'
    }
  }
}
</script>
