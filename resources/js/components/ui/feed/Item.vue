<template>
  <div :class="`flex last:!mb-0 text-dark relative mb-16 sm:mb-16 lg:mb-20 relative feed-item ${cls}`">
    <div class="sm:max-w-[60%] lg:max-w-[50%] w-full p-3 lg:py-2 lg:pb-3 bg-white border-2 border-gray-100 text-sm sm:text-base text-dark rounded">
      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    internal: {
      type: Number,
      default: 0
    }
  },

  computed: {
    cls() {
      return this.$props.internal ? 'feed-item--left' : 'feed-item--right';
    }
  },
}
</script>
<style>
.feed-item--left {
  @apply justify-start
}

.feed-item--right {
  @apply justify-end
}

.feed-item--left > div {
  @apply bg-zinc-50
}

.feed-item--left > div > div:first-of-type {
  @apply left-2/4 -translate-x-1/2
}

.feed-item--right > div > div:first-of-type {
  @apply right-2/4 translate-x-1/2
}

.feed-item-delete {
  @apply absolute -bottom-[24px] text-xs text-gray-300 no-underline
}

.feed-item-delete:hover {
  @apply text-highlight
}

.feed-item--right .feed-item-delete {
  @apply right-0 pr-1
}

.feed-item--left .feed-item-delete {
  @apply left-0 pl-1
}
</style>