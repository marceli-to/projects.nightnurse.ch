<template>
  <div :class="[$props.private ? 'border-slate-300' : 'border-gray-200', 'first:border-t border-b']" v-if="($props.truncate && $props.index < truncateLimit) || !$props.truncate">
    <template v-if="$props.intermediate">
      <a :href="fileUri(file)" 
        target="_blank" 
        :class="[file.preview ? 'flex-col mb-1 lg:mb-2' : 'items-center', 'flex no-underline hover:text-highlight py-2 lg:pt-3']">
        <img 
          :src="smallImageUri(file)" 
          height="100" 
          width="100"
          loading="lazy"
          :class="[file.image_orientation == 'portrait' ? 'w-1/2' : 'w-full', '!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto bg-light rounded-sm']"
          v-if="file.preview" />
        <div class="mr-2 lg:mr-3 py-1" v-else>
          <file-type :extension="file.extension" />
        </div>
        <div :class="[file.preview ? 'py-1 sm:py-2 lg:py-3' : '', 'font-mono text-xs']">
          {{ file.original_name | truncate(45, '...') }} – {{ file.size }}
        </div>
      </a>
    </template>
    <template v-else>
      <a :href="fileUri(file)" 
        target="_blank" 
        class="flex items-center no-underline hover:text-highlight py-2">
        <img 
          :src="thumbnailImageUri(file)" 
          height="100" 
          width="100"
          loading="lazy"
          class="!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm"
          v-if="file.preview" />
        <div class="mr-2 lg:mr-3 py-1" v-else>
          <file-type :extension="file.extension" />
        </div>
        <div class="font-mono text-xs">
          {{ file.original_name | truncate(45, '...') }} – {{ file.size }}<br>
         </div>
      </a>

      <router-link 
        :to="{name: 'markup', params: { slug: $route.params.slug, uuid: $route.params.uuid, fileUuid: file.uuid }}"
        class="font-mono text-xs text-gray-400 hover:text-highlight no-underline flex items-center mb-3 mt-1">
        <div class="relative">
          <annotation-icon class="h-4 w-4 mr-2"  aria-hidden="true" />
          <div class="rounded-full bg-highlight w-[7px] h-[7px] absolute -top-[2px] right-[6px]"></div>
        </div>
        Comments
      </router-link>
    </template>
  </div>
</template>
<script>
import { AnnotationIcon } from "@vue-hero-icons/outline";
import FileType from "@/components/ui/misc/FileType.vue";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    FileType,
    AnnotationIcon,
  },

  mixins: [
    Helpers,
  ],

  data() {
    return {
      truncateLimit: 3,
    }
  },

  props: {

    file: {
      type: Object,
      default: null
    },

    truncate: {
      type: Boolean,
      default: false
    },
    
    index: {
      type: Number,
      default: 0,
    },
    
    private: {
      type: Number,
      default: 0
    },

    intermediate: {
      type: Number,
      default: 0
    },
  },
}
</script>