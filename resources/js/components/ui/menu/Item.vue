<template>
  <router-link :to="{name: $props.route}" :active-class="'text-highlight'" class="hover:text-highlight text-base font-normal no-underline flex items-center p-6 lg:p-8 px-0 lg:px-0 border-b">
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

 