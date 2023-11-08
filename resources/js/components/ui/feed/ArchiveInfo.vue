<template>
  <div class="mt-4 mb-8 flex justify-center">
    <div :class="[$props.state == 'concluded' ? 'border-blue-500 text-blue-500' : 'border-red-500 text-red-500', 'notification inline-block p-2 w-auto border-2  max-w-sm md:max-w-[600px] text-center leading-5']">
      <template v-if="state == 'archived'">
        {{ translate('Dieses Projekt ist archiviert. Alle Dateien werden ungefähr 30 Tage nach Abschluss gelöscht.') }}
      </template>
      <template v-if="state == 'concluded'">
        {{ translate('Dieses Projekt ist abgeschlossen.') }}
      </template>
      <template v-if="state == 'trashed'">
        {{ translate('Dieses Projekt wurde gelöscht.') }}
      </template>
    </div>
  </div>
</template>
<script>
import i18n from "@/i18n";
export default {
  mixins: [i18n],  

  props: {
    state: {
      type: String,
      required: true,
    },
  }
}
</script>