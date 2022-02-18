<template>
  <div class="text-base text-sm feed-item__body">
    <slot />
  </div>
</template>
<style>
.feed-item__body p {
  @apply !mt-0;
}
.feed-item__body p:last-of-type {
  @apply !mb-1;
}
</style>