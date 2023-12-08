<template>
<div :class="[this.$route.name == 'markup' ? '' : 'max-w-5xl', 'bg-white w-full px-4 lg:px-6 pb-20 sm:pb-20 lg:pb-24 relative mx-auto']">
  <slot />
</div>
</template>