<template>
  <nav class="bg-light pt-4 leading-none flex flex-col items-center justify-between">
    <div>
      <a href="/projects" class="btn-icon flex justify-center !w-full !mr-0 !mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
      </a>
      <a href="javascript:;" class="text-black flex items-center justify-center p-3" @click="$emit('addRectangle')">
        <rectangle-icon class="icon-list text-black w-4 h-4" aria-hidden="true" />
      </a>
      <a href="javascript:;" class="text-black flex items-center justify-center p-3" @click="$emit('addCircle')">
        <circle-icon class="icon-list text-black w-4 h-4" aria-hidden="true" />
      </a>
      <a href="javascript:;" class="text-black flex items-center justify-center p-3" @click="$emit('addComment')">
        <annotation-icon class="icon-list text-black" aria-hidden="true" />
      </a>
      <template v-if="$props.canDelete">
        <a href="javascript:;" class="text-red-600 flex items-center justify-center p-3" @click="$emit('deleteElement')">
          <trash-icon class="icon-list text-red-600" aria-hidden="true" />
        </a>
      </template>
      <template v-else>
        <div class="flex items-center justify-center p-3">
          <trash-icon class="icon-list text-gray-400" aria-hidden="true" />
        </div>
      </template>
    </div>

    <div class="w-auto gap-y-2 flex flex-col justify-center rounded-md">
      <a href="javascript:;" @click="$emit('selectImage', thumbnail.uuid)"
        v-for="thumbnail in images"
        :key="thumbnail.uuid"
        class="group">
        <img 
          :src="thumbnailImageUri(thumbnail)" 
          height="100" 
          width="100"
          loading="lazy"
          :class="[$props.image.uuid == thumbnail.uuid ? 'opacity-100' : 'border-transparent opacity-30', 'border block h-auto max-w-[48px] !m-0 group-hover:opacity-100 transition']" />
      </a>
    </div>

    <div class="mb-12">
      <router-link :to="{name: 'messages', params: { slug: $props.project.slug, uuid: $props.project.uuid }}" class="text-black flex items-center justify-center p-3">
        <arrow-left-icon class="icon-list text-black h-5 w-5" aria-hidden="true" />
      </router-link>
    </div>
  </nav>
</template>
<script>
import i18n from "@/i18n";
import Helpers from "@/mixins/Helpers";
import MenuSeparator from '@/components/ui/markup/MenuSeparator.vue';
import { TrashIcon, AnnotationIcon, ArrowLeftIcon, SaveIcon } from "@vue-hero-icons/outline";
import RectangleIcon from '@/components/ui/icons/Rectangle.vue';
import CircleIcon from '@/components/ui/icons/Circle.vue';
export default {

  components: {
    MenuSeparator,
    TrashIcon,
    RectangleIcon,
    ArrowLeftIcon,
    CircleIcon,
    SaveIcon,
    AnnotationIcon,
  },

  mixins: [i18n, Helpers],

  props: {

    canDelete: {
      type: Boolean,
      default: false,
    },

    project: {
      type: Object,
      required: true,
    },

    image: {
      type: Object,
      required: false,
    },

    images: {
      type: Array,
      default: () => [],
    },
  },

};
</script>