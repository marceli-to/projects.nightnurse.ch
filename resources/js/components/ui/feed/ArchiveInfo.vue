<template>
  <div class="mt-4 mb-8 flex justify-center leading-7">
    <div class="notification inline-block p-2 w-auto border-2 border-red-500 text-red-500 max-w-sm">
      {{ translate('Dieses Projekt ist archiviert. Alle Dateien werden ungefähr 30 Tage nach Abschluss gelöscht.') }}
    </div>
  </div>
</template>
<script>
import i18n from "@/i18n";
export default {
  mixins: [i18n],  
}
</script>