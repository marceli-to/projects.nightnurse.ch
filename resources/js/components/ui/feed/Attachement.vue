<template>
  <div :class="[$props.private ? 'border-slate-300' : 'border-gray-200', 'first:border-t border-b']" v-if="($props.truncate && $props.index < truncateLimit) || !$props.truncate">
    <template v-if="$props.intermediate">
      <a :href="fileUri(file)" 
        target="_blank" 
        :class="[file.preview ? 'flex-col' : 'items-center', 'flex no-underline hover:text-highlight py-2 lg:pt-3']">
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
        <div :class="[file.preview ? 'py-1 sm:pt-2 lg:pt-3' : '', 'font-mono text-xs']">
          {{ file.original_name | truncate(45, '...') }} – {{ file.size }}
        </div>
      </a>
      <router-link 
        :to="{name: 'markup', params: { slug: $route.params.slug, uuid: $route.params.uuid, messageUuid: message.uuid, imageUuid: file.uuid }}"
        class="font-mono text-xs text-gray-400 hover:text-highlight no-underline flex items-center mb-3"
        v-if="file.preview">
        <div class="relative">
          <annotation-icon class="h-4 w-4 mr-2"  aria-hidden="true" />
          <div class="rounded-full bg-highlight w-[7px] h-[7px] absolute -top-[2px] right-[6px]" v-if="file.markups.length"></div>
        </div>
        <template v-if="file.locked_markups.length == 1">
          {{ file.locked_markups.length }} {{ translate('Kommentar') }}
        </template>
        <template v-else-if="file.locked_markups.length > 1">
          {{ file.locked_markups.length }} {{ translate('Kommentare') }}
        </template>
        <template v-else>
          {{ translate('Kommentieren') }}
        </template>
      </router-link>
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
        :to="{name: 'markup', params: { slug: $route.params.slug, uuid: $route.params.uuid, messageUuid: file.message_uuid, imageUuid: file.uuid }}"
        class="font-mono text-xs text-gray-400 hover:text-highlight no-underline flex items-center mb-3 mt-1"
        v-if="file.preview">
        <div class="relative">
          <annotation-icon class="h-4 w-4 mr-2"  aria-hidden="true" />
          <div class="rounded-full bg-highlight w-[7px] h-[7px] absolute -top-[2px] right-[6px]" v-if="file.locked_markups.length"></div>
        </div>
        <template v-if="file.locked_markups.length == 1">
          {{ file.locked_markups.length }} {{ translate('Kommentar') }}
        </template>
        <template v-else-if="file.locked_markups.length > 1">
          {{ file.locked_markups.length }} {{ translate('Kommentare') }}
        </template>
        <template v-else>
          {{ translate('Kommentieren') }}
        </template>
      </router-link>
    </template>
  </div>
</template>
<script>
import i18n from "@/i18n";
import { AnnotationIcon } from "@vue-hero-icons/outline";
import FileType from "@/components/ui/misc/FileType.vue";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    FileType,
    AnnotationIcon,
  },

  mixins: [i18n, Helpers],

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

    message: {
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