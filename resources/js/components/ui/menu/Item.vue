<template>
  <router-link :to="{name: $props.route}" :active-class="'text-magenta'" class="text-gray-400 hover:text-magenta text-base font-normal no-underline flex items-center p-3 sm:p-5">
    <slot />
  </router-link>
</template>
<script>
export default {
  props: {
    route: {
      type: String,
    }
  }
}
</script>

 