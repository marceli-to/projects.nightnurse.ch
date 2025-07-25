<template>
  <nav class="bg-light pt-4 leading-none flex flex-col items-center justify-between">
    <div>
      <a href="/projects" class="btn-icon flex justify-center !w-full !mr-0 !mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
      </a>
      <a href="javascript:;" class="flex items-center justify-center p-3" @click="$emit('selectShape', 'rectangle')">
        <rectangle-icon :class="[$props.selectedShape == 'rectangle' ? 'text-highlight' : 'text-black', 'icon-list w-4 h-4']" aria-hidden="true" />
      </a>
      <a href="javascript:;" class="flex items-center justify-center p-3" @click="$emit('selectShape', 'circle')">
        <circle-icon :class="[$props.selectedShape == 'circle' ? 'text-highlight' : 'text-black', 'icon-list w-4 h-4']" aria-hidden="true" />
      </a>
      <a href="javascript:;" class="flex items-center justify-center p-3" @click="$emit('selectShape', 'comment')">
        <annotation-icon :class="[$props.selectedShape == 'comment' ? 'text-highlight' : 'text-black', 'icon-list w-5 h-5']" aria-hidden="true" />
      </a>
      <template v-if="$props.canDelete">
        <a href="javascript:;" class="text-red-600 flex items-center justify-center p-3" @click="$emit('deleteElement')">
          <trash-icon class="icon-list text-red-600" aria-hidden="true" />
        </a>
      </template>
      <template v-else>
        <div class="flex items-center justify-center p-3">
          <trash-icon class="icon-list !text-gray-400" aria-hidden="true" />
        </div>
      </template>
    </div>

    <div class="w-auto gap-y-2 flex flex-col justify-center">
      <a href="javascript:;" @click="$emit('selectImage', thumbnail.uuid)"
        v-for="thumbnail in displayedImages"
        :key="thumbnail.uuid"
        class="group">
        <img 
          :src="thumbnailImageUri(thumbnail)" 
          height="100" 
          width="100"
          loading="lazy"
          :class="[$props.image.uuid == thumbnail.uuid ? 'opacity-100' : 'border-transparent opacity-30', 'border block h-auto max-w-[40px] !m-0 group-hover:opacity-100 transition']" />
      </a>
      
      <template v-if="images.length > 6">
        <a href="javascript:;" @click="showImageSelector = true" class="text-xs font-mono text-gray-600 hover:text-highlight flex justify-center !no-underline transition-colors">
          <plus-icon class="icon-list text-black h-7 w-7" aria-hidden="true" />
        </a>
      </template>
    </div>

    <div>
      <router-link :to="{name: 'messages', params: { slug: $props.project.slug, uuid: $props.project.uuid }}" class="text-black flex items-center justify-center p-3">
        <arrow-left-icon class="icon-list text-black h-5 w-5" aria-hidden="true" />
      </router-link>
    </div>
    
    <image-selector 
      :show="showImageSelector"
      :images="images"
      :current-image="image"
      @close="showImageSelector = false"
      @selectImage="handleImageSelect" />
  </nav>
</template>
<script>
import i18n from "@/i18n";
import Helpers from "@/mixins/Helpers";
import MenuSeparator from '@/components/ui/markup/MenuSeparator.vue';
import ImageSelector from '@/components/ui/markup/ImageSelector.vue';
import { TrashIcon, AnnotationIcon, ArrowLeftIcon, SaveIcon, PlusIcon } from "@vue-hero-icons/outline";
import RectangleIcon from '@/components/ui/icons/Rectangle.vue';
import CircleIcon from '@/components/ui/icons/Circle.vue';
export default {

  components: {
    MenuSeparator,
    ImageSelector,
    TrashIcon,
    RectangleIcon,
    ArrowLeftIcon,
    CircleIcon,
    SaveIcon,
    PlusIcon,
    AnnotationIcon,
  },

  mixins: [i18n, Helpers],

  props: {

    canDelete: {
      type: Boolean,
      default: false,
    },

    selectedShape: {
      type: String,
      default: '',
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

  data() {
    return {
      showImageSelector: false,
    };
  },

  computed: {
    displayedImages() {
      return this.images.slice(0, 6);
    },
  },

  methods: {
    handleImageSelect(imageUuid) {
      this.$emit('selectImage', imageUuid);
    },
  },

};
</script>