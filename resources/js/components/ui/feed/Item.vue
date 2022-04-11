<template>
  <div :class="`flex text-dark relative feed-item ${position}`">
    <div class="sm:max-w-[60%] w-full p-3 lg:py-2 lg:pb-3 bg-white border-2 border-gray-100 text-sm sm:text-base text-dark rounded">
      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    item: {
      type: Object,
      default: 0
    }
  },

  computed: {
    position() {
      return this.$props.item.internal ? 'feed-item--left' : 'feed-item--right';
    },
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
  @apply absolute -bottom-[56px] text-xs text-gray-300 no-underline pl-0
}

.feed-item-delete:hover {
  @apply text-highlight
}

.feed-item--right .feed-item-delete {
  @apply -right-[12px]
}

.feed-item--left .feed-item-delete {
  @apply -left-[12px]
}

.feed-item {
  @apply mt-16
}

.feed-item li {
  @apply !mt-1
}

.feed-item + .feed-item.has-timestamp {
  @apply mt-16
}

.feed-item.is-deleted + .feed-item {
  @apply mt-8
}

.feed-item.is-deleted + .feed-item.has-timestamp {
  @apply mt-16
}

.feed-item.is-private > div {
  @apply bg-slate-500 border-slate-500 !text-slate-100
}
</style>