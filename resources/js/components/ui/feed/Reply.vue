<template>
  <a href="javascript:;" 
    @click="$emit('scrollTo', originalMessage.uuid)"
    class="block !no-underline px-2 py-1 mb-2 lg:mb-4 border-l-2 border-gray-300 text-xs sm:text-sm text-gray-500 relative">
    <div :class="[originalMessage.body ? 'font-bold' : '', 'text-sm italic']" v-if="originalMessage.subject">
      {{ originalMessage.subject }}
    </div>
    <div class="text-sm italic" v-html="originalMessage.body"></div>
  </a>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  components: {
    
  },

  mixins: [ErrorHandling, i18n],

  props: {
    originalMessage: {
      type: Object,
      default: null
    }
  },
}
</script>