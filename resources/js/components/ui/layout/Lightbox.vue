<template>
  <div class="fixed z-40 w-screen h-screen inset-0 bg-black bg-opacity-60 z-1001" v-if="isOpen">
    <div class="fixed z-50 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[300px] sm:max-w-lg bg-white rounded-md p-4 space-y-5">
      <a href="javascript:;" @click="hide()" class="text-gray-400 hover:text-highlight block absolute top-3 right-3">
          <x-icon class="h-6 w-6" aria-hidden="true" />
        </a>
      <slot />
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