<template>
  <div class="text-base text-sm break-words feed-item__body">
    <slot />
  </div>
</template>
<style>
.feed-item__body p {
  @apply !mt-0 !mb-2;
}
.feed-item__body p:last-of-type {
  @apply !mb-1;
}

.feed-item__body a {
  @apply text-ellipsis
}

.feed-item__body.is-truncated > div {
  display: none;
}

.feed-item__body.is-truncated > a {
  display: flex;
}

</style>