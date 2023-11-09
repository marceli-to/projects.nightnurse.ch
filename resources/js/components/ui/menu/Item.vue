<template>
  <router-link :to="{name: $props.route, params: $props.params}" :active-class="'text-highlight'" :class="classes" v-if="$props.params">
    <slot />
  </router-link>
  <router-link :to="{name: $props.route}" :active-class="'text-highlight'" :class="classes" v-else>
    <slot />
  </router-link>
</template>
<script>
export default {
  data() {
    return {
      classes: 'hover:text-highlight text-base font-normal no-underline flex items-center p-6 lg:p-8 px-0 lg:px-0 border-b',
    }
  },
  props: {
    route: {
      type: String,
    },
    params: {
      type: Object,
      default: () => {},
    },
  },

}
</script>

 