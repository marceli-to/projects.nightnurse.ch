<template>
  <nav class="bg-white w-full pb-12 pt-4 leading-none flex flex-col items-center justify-between">
    <div class="">
      <a href="/projects" class="btn-icon flex justify-center !w-full !mr-0 !mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
      </a>
      <a href="javascript:;" class="text-black flex items-center justify-center p-3" @click="$emit('addRectangle')">
        <rectangle-icon class="icon-list text-black w-4 h-4" aria-hidden="true" />
      </a>
      <!-- <menu-separator /> -->
      <a href="javascript:;" class="text-black flex items-center justify-center p-3" @click="$emit('addCircle')">
        <circle-icon class="icon-list text-black w-4 h-4" aria-hidden="true" />
      </a>
      <!-- <menu-separator /> -->
      <a href="javascript:;" class="text-black flex items-center justify-center p-3" @click="$emit('addComment')">
        <annotation-icon class="icon-list text-black" aria-hidden="true" />
      </a>
      <template v-if="canDelete">
        <!-- <menu-separator /> -->
        <a href="javascript:;" class="text-red-600 flex items-center justify-center p-3" @click="$emit('deleteElement')">
          <trash-icon class="icon-list text-red-600" aria-hidden="true" />
        </a>
      </template>
    </div>
  </nav>
</template>
<script>
import MenuSeparator from '@/components/ui/markup/MenuSeparator.vue';
import { TrashIcon, AnnotationIcon } from "@vue-hero-icons/outline";
import RectangleIcon from '@/components/ui/icons/Rectangle.vue';
import CircleIcon from '@/components/ui/icons/Circle.vue';
export default {

  components: {
    MenuSeparator,
    TrashIcon,
    RectangleIcon,
    CircleIcon,
    AnnotationIcon,
  },

  props: {
    canDelete: {
      type: Boolean,
      default: false,
    },
  },
};
</script>