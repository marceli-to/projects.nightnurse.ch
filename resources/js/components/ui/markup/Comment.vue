<template>
  <div class="text-gray-300 flex mb-4 lg:mb-6">
    <div class="shrink-0">
      <annotation-icon :class="[comment.shape_uuid == highlighted ? 'text-highlight' : 'text-gray-300', 'h-5 w-5']" />
    </div>
    <div class="ml-2">
      <header class="text-xs font-mono">
        {{ comment.date }} – {{ comment.author }}
      </header>
      <div class="text-dark text-sm leading-[1.5] mt-1">
        {{ comment.comment }}
      </div>
    </div>
  </div>
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
      default: '',
    },
  },

  mixins: [i18n, Helpers],

}
</script>