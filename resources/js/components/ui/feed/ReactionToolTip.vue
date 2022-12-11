<template>
<span class="has-reaction-tooltip block relative w-full">
  <slot />
  <div class="reaction-tooltip">{{ $props.users.map(x => x.full_name).join(", ") }}</div>
</span>
</template>
<script>
export default {

  props: {
    users: {},
  },
}
</script>
<style scoped>
.reaction-tooltip {
  @apply cursor-pointer text-white !font-mono text-tiny break-keep text-center leading-relaxed p-1.5 bg-dark rounded-sm invisible absolute left-[50%] -top-1 translate-y-[-100%] translate-x-[-50%] block w-auto min-w-[80px] max-w-md;
}

.has-reaction-tooltip:hover .reaction-tooltip {
  @apply visible z-50 cursor-pointer;
}
</style>