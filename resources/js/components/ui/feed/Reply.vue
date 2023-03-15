<template>
  <a href="javascript:;" 
    @click="$emit('scrollTo', originalMessage.uuid)"
    class="block !no-underline px-2 py-1 mb-2 lg:mb-4 border-l-2 border-gray-300 group-[.is-private]:border-alice-blue-dark text-sm sm:text-base text-gray-500 relative">
    <span :class="[originalMessage.body ? 'font-bold' : '', 'text-sm italic block']" v-if="originalMessage.subject">
      {{ originalMessage.subject }}
    </span>
    <span class="text-sm italic block" v-html="originalMessage.body"></span>
  </a>
</template>
<script>
import ErrorHandling from "@/mixins/ErrorHandling";
import i18n from "@/i18n";
import NProgress from 'nprogress';

export default {

  mixins: [ErrorHandling, i18n],

  props: {
    originalMessage: {
      type: Object,
      default: null
    }
  },
}
</script>