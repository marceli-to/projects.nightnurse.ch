<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 flex items-center justify-center z-[21000]" @click.self="close">
    <div class="bg-white rounded-lg p-4 pb-6 max-w-4xl max-h-[80vh] w-full mx-4 overflow-hidden">
      <div class="flex justify-between items-center mb-4">
        <h3 class="!mt-0 !mb-0 !text-lg w-full">{{ translate('Bild auswählen') }}</h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600">
          <x-icon class="h-6 w-6" />
        </button>
      </div>
      
      <div class="grid grid-cols-12 gap-2 overflow-y-auto">
        <a 
          href="javascript:;"
          v-for="image in images" 
          :key="image.uuid"
          @click="selectImage(image)"
          class="col-span-3 group relative">
          <img 
            :src="thumbnailImageUri(image)" 
            :alt="`Image ${image.uuid}`"
            :class="[
              currentImage?.uuid === image.uuid ? '!opacity-100' : '',
              'block w-full h-auto !my-0 relative opacity-60 group-hover:opacity-100 transition-all'
            ]"
            loading="lazy" />

            <template v-if="image.markup_count > 0">
              <span class="absolute top-2 right-2 z-5 bg-blue-700 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">
                {{ image.markup_count }}
              </span>
            </template>
          
          <!-- Comment/Markup count badge -->
          <div 
            v-if="getImageMarkupCount(image.uuid) > 0"
            class="absolute top-2 right-2 bg-blue-700 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">
            {{ getImageMarkupCount(image.uuid) }}
          </div>

        </a>
      </div>
    </div>
  </div>
</template>

<script>
import i18n from "@/i18n";
import Helpers from "@/mixins/Helpers";
import { XIcon, CheckIcon } from "@vue-hero-icons/outline";

export default {
  components: {
    XIcon,
    CheckIcon,
  },

  mixins: [i18n, Helpers],

  props: {
    show: {
      type: Boolean,
      default: false,
    },
    images: {
      type: Array,
      required: true,
    },
    currentImage: {
      type: Object,
      default: null,
    },
  },

  methods: {
    close() {
      this.$emit('close');
    },
    
    selectImage(image) {
      this.$emit('selectImage', image.uuid);
      this.close();
    },
    
    getImageMarkupCount(imageUuid) {
      // return this.imageMarkupCounts[imageUuid] || 0;
    },
  },
};
</script>