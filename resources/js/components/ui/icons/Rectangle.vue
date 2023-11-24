<template>
<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" :class="$props.cls">
  <rect x="1" y="1" width="18" height="18" stroke="currentColor" stroke-width="2"/>
</svg>
</template>
<script>
export default {
  props: {
    cls: {
      type: String,
      default: '',
    },
  },
};
</script>