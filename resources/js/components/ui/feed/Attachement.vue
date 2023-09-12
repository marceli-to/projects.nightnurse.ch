<template>
  <div :class="[$props.private ? 'border-slate-300' : 'border-gray-200', 'first:border-t border-b']"
    v-if="($props.truncate && $props.index < truncateLimit) || !$props.truncate">
    <a :href="`${filePath}`" 
      target="_blank" 
      class="flex items-center no-underline hover:text-highlight py-2">
      <img 
        :src="`${thumbnailPath}`" 
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
  </div>
</template>
<script>
import FileType from "@/components/ui/misc/FileType.vue";

export default {

  components: {
    FileType,
  },

  data() {
    return {
      truncateLimit: 3,
      filePath: null,
      thumbnailPath: null,
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
  },

  mounted() {
    if (this.file.folder) {
      this.filePath = `/storage/uploads/${this.file.folder}/${this.file.name}`;
      this.thumbnailPath = `/img/thumbnail/${this.file.folder}/${this.file.name}`;
      return;
    }
    this.filePath = `/storage/uploads/${this.file.name}`;
    this.thumbnailPath = `/img/thumbnail/${this.file.name}`;
    return;
  },
}
</script>