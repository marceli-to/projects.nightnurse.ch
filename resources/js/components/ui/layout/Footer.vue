<template>
  <footer class="bg-light fixed bottom-0 left-0 w-full z-[200]">
    <div :class="[this.$route.name == 'markup' ? '' : 'max-w-5xl', 'bg-white mx-auto w-full flex items-center justify-between p-4 lg:py-5 border-t-2 border-light']">
      <slot />
    </div>
  </footer>
</template>