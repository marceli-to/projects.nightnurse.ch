<template>
  <div class="fixed w-screen h-screen inset-0 bg-black bg-opacity-60 z-[20001] pb-32" v-if="isOpen">
    <div :class="`fixed z-50 left-1/2 -translate-x-1/2 w-full bg-white rounded-md p-4 ${$props.maxWidth} ${$props.styles}`">
      <div class="max-h-[80vh] overflow-y-auto">
      <div class="relative">
        <a href="javascript:;" @click="hide()" class="text-gray-400 hover:text-highlight block absolute z-50 top-0 right-0">
          <x-icon class="h-6 w-6" aria-hidden="true" />
        </a>
        <slot />
      </div>
      </div>
    </div>
  </div>
</template>
<script>
import { XIcon } from "@vue-hero-icons/outline";

export default {

  components: {
    XIcon
  },

  data() {
    return {
      isOpen: false,
    }
  },

  props: {
    maxWidth: {
      type: String,
      default: 'max-w-[300px] sm:max-w-lg'
    },
    styles: {
      type: String,
      default: 'top-1/2 -translate-y-1/2'
    }
  },

  created() {
    const onEscape = (e) => {
      if (this.isOpen && e.keyCode === 27) {
        this.hide();
      }
    }
    document.addEventListener('keydown', onEscape);
  },

  methods: {

    show() {
      this.isOpen = true;
    },

    hide() {
      this.isOpen = false;
    }
  }
}
</script>