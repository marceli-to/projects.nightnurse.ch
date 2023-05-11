<template>
  <div class="notification border-2 border-red-500 text-red-500 mb-4 mt-2 md:mb-0 md:!absolute inline-block w-auto right-0 leading-snug p-2">
    {{ translate('Projekt ist archiviert und wird in 30 Tagen gelöscht.') }}
  </div>
</template>
<script>
import i18n from "@/i18n";
export default {
  mixins: [i18n],  
}
</script>