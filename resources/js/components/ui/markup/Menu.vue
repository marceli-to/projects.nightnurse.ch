<template>
  <nav class="bg-black leading-none flex justify-between">
    <div class="flex items-center">
      <a href="javascript:;" class="text-white flex items-center justify-center w-10 h-10" @click="$emit('addRectangle')">
        <rectangle-icon class="icon-list w-4 h-4" aria-hidden="true" />
      </a>
      <menu-separator />
      <a href="javascript:;" class="text-white flex items-center justify-center w-10 h-10" @click="$emit('addCircle')">
        <circle-icon class="icon-list w-4 h-4" aria-hidden="true" />
      </a>
      <menu-separator />
      <a href="javascript:;" class="text-white flex items-center justify-center w-10 h-10" @click="$emit('addComment')">
        <annotation-icon class="icon-list" aria-hidden="true" />
      </a>
      <menu-separator />
    </div>
    <div class="flex items-center">
      <menu-separator />
      <a href="javascript:;" class="text-white flex items-center justify-center w-10 h-10">
        <save-icon class="icon-list" aria-hidden="true" />
      </a>
      <template v-if="hasSelectedShape">
        <menu-separator />
        <a href="javascript:;" class="text-white flex items-center justify-center w-10 h-10" @click="$emit('deleteElement')">
          <trash-icon class="icon-list" aria-hidden="true" />
        </a>
      </template>
    </div>
  </nav>
</template>
<script>
import MenuSeparator from '@/components/ui/markup/MenuSeparator.vue';
import { TrashIcon, SaveIcon, AnnotationIcon } from "@vue-hero-icons/outline";
import RectangleIcon from '@/components/ui/icons/Rectangle.vue';
import CircleIcon from '@/components/ui/icons/Circle.vue';
export default {

  components: {
    MenuSeparator,
    TrashIcon,
    SaveIcon,
    RectangleIcon,
    CircleIcon,
    AnnotationIcon,
  },

  props: {
    hasSelectedShape: {
      type: Boolean,
      default: false,
    },
  },
};
</script>