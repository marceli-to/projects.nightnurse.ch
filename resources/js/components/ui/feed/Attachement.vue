<template>
  <div :class="[$props.private ? 'border-slate-300' : 'border-gray-200', 'first:border-t border-b']" v-if="($props.truncate && $props.index < truncateLimit) || !$props.truncate">
    <template v-if="$props.intermediate">
      <a :href="fileUri(file)" 
        target="_blank" 
        :class="[file.preview ? 'flex-col mb-1 lg:mb-2' : 'items-center', 'flex no-underline hover:text-highlight py-2']">
        <img 
          :src="smallImageUri(file)" 
          height="100" 
          width="100"
          class="!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto w-full bg-light rounded-sm"
          v-if="file.preview" />
        <div class="mr-2 lg:mr-3 py-1" v-else>
          <file-type :extension="file.extension" />
        </div>
        <div :class="[file.preview ? 'py-1 lg:py-2' : '', 'font-mono text-xs']">
          {{ file.original_name | truncate(45, '...') }} – {{ file.size | filesize(file.size) }}
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
          class="!mt-0 !mb-0 mr-3 lg:mr-4 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm"
          v-if="file.preview" />
        <div class="mr-2 lg:mr-3 py-1" v-else>
          <file-type :extension="file.extension" />
        </div>
        <div class="font-mono text-xs">
          {{ file.original_name | truncate(45, '...') }} – {{ file.size | filesize(file.size) }}
        </div>
      </a>
    </template>
  </div>
</template>
<script>
import FileType from "@/components/ui/misc/FileType.vue";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    FileType,
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