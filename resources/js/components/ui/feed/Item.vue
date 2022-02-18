<template>
  <div class="flex even:justify-end odd:justify-start last:!mb-0 text-dark relative mb-16 sm:mb-16 lg:mb-20 feed-item">
    <div class="max-w-[90%] sm:max-w-[60%] lg:max-w-[50%] w-full p-2 lg:p-2.5 bg-white border-2 border-gray-100 text-sm sm:text-base text-dark rounded">
      <slot />
    </div>
  </div>
</template>
<style>

.feed-item:nth-child(odd) > div {
  @apply bg-light
}

.feed-item:nth-child(even) > div > div:first-of-type {
  @apply left-2/4 -translate-x-1/2
}

.feed-item:nth-child(odd) > div > div:first-of-type {
  @apply right-2/4 translate-x-1/2
}
</style>