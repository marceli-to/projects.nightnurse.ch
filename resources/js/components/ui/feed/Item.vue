<template>
  <div :class="`flex text-dark relative feed-item ${position}`">
    <div class="sm:max-w-[60%] w-full p-3 lg:py-2 lg:pb-3 bg-white border-2 border-zinc-100 text-sm sm:text-base text-dark rounded">
      <slot />
    </div>
  </div>
</template>
<script>
export default {
  props: {
    item: {
      type: Object,
      default: null
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
.feed-item ul {
  @apply mb-4
}

.feed-item li {
  @apply !mt-1 !mb-1
}

.feed-item.is-private > div {
  @apply bg-slate-400 border-slate-400 !text-slate-100
}

.feed-item.is-private a {
  @apply text-slate-100
}

.feed-item.is-private .feed-item__body > div {
  @apply !text-slate-100
}

.feed-item--left {
  @apply justify-start
}

.feed-item--right {
  @apply justify-end
}

.feed-item--left > div {
  @apply bg-zinc-50
}

.feed-item-delete {
  @apply absolute -bottom-[25px] text-xs !text-slate-300 no-underline pl-0
}

.feed-item-delete:hover {
  @apply !text-highlight
}

.feed-item--right .feed-item-delete {
  @apply right-0
}

.feed-item--left .feed-item-delete {
  @apply left-0
}

.feed-item {
  @apply mb-10 sm:mb-8
}
</style>