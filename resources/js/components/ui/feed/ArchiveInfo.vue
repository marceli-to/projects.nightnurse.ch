<template>
  <div class="mt-4 mb-8 flex justify-center leading-snug">
    <div class="notification inline-block p-2 w-auto border-2 border-red-500 text-red-500">
      {{ translate('Dieses Projekt ist archiviert und wird in 30 Tagen gelöscht.') }}
    </div>
  </div>
</template>
<script>
import i18n from "@/i18n";
export default {
  mixins: [i18n],  
}
</script>