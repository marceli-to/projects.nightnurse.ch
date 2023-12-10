<template>
  <a href="javascript:;" 
    @mouseover="$emit('mouseover', comment.uuid)" 
    @mouseout="$emit('mouseout')"
    :class="[highlighted == comment.uuid ? 'bg-light' : '', 'no-underline text-gray-400 flex mb-4 lg:mb-6 p-1 lg:p-2 rounded-md w-auto']">
    <div class="shrink-0">
      <annotation-icon class="text-gray-400 h-5 w-5" />
    </div>
    <div class="ml-2">
      <header class="text-xs text-gray-400 font-mono">
        {{ comment.date }} – {{ comment.author }}
      </header>
      <div class="text-dark text-sm leading-[1.5] mt-1">
        {{ comment.comment }}
      </div>
    </div>
  </a>
</template>
<script>
import i18n from "@/i18n";
import Helpers from "@/mixins/Helpers";
import { AnnotationIcon } from "@vue-hero-icons/outline";

export default {

  components: {
    AnnotationIcon,
  },

  props: {
    comment: {
      type: Object,
      required: true,
    },
    highlighted: {
      type: String,
      default: null,
    },
  },

  mixins: [i18n, Helpers],

}
</script>