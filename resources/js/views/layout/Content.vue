<template>
<div :class="[this.$route.name == 'markup' ? '' : 'max-w-5xl px-4 lg:px-6', 'bg-white w-full pb-20 lg:pb-24 relative mx-auto']">
  <slot />
</div>
</template>