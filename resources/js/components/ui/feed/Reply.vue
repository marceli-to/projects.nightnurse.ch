<template>
  <div class="p-2 mb-2 lg:mb-4 border-2 border-zinc-100 text-sm sm:text-base text-dark relative rounded-md bg-zinc-50 group-[.is-internal]:bg-zinc-50 group-[.is-private]:bg-slate-500 group-[.is-private]:border-slate-400 group-[.is-private]:text-slate-100">
    <div :class="[originalMessage.body ? 'font-bold' : '', 'text-sm italic']" v-if="originalMessage.subject">
      {{ originalMessage.subject }}
    </div>
    <div class="text-sm italic" v-html="originalMessage.body"></div>
  </div>
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