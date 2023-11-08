<template>
  <lightbox ref="lightbox" :maxWidth="'max-w-[280px] md:max-w-[480px]'">
    <div>
      <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">
        {{ translate('Aktion bestätigen') }}
      </h2>
      <p>{{ translate('Bitte löschen bestätigen. «Löschen» markiert Projekt als gelöscht, diese können aber weiterhin angezeigt und wiederhergestellt werden. «Endgültig löschen» entfernt das Projekt sowie alle Dateien unwiderruflich.') }}</p>
      <footer>
        <div class="flex justify-between items-center gap-4 mt-6 lg:mt-8">
          <button type="submit" class="btn-primary !bg-gray-400 hover:!bg-dark p-2 md:!py-3 md:!px-3 !w-full" @click.prevent="softDelete()">
            {{ translate('Löschen') }}
          </button>
          <button type="submit" class="btn-primary !bg-red-500 hover:!bg-dark p-2 md:!py-3 md:!px-2 !w-full !flex justify-center items-center" @click.prevent="forceDelete()">
            <exclamation-icon class="w-5 h-5 shrink-0 mr-2" />
            {{ translate('Endgültig löschen') }}
          </button>
        </div>
      </footer>
    </div>
  </lightbox>
</template>
<script>
import { ExclamationIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";
import ErrorHandling from "@/mixins/ErrorHandling";
import Lightbox from "@/components/ui/layout/Lightbox.vue";

export default {

  components: {
    Lightbox,
    ExclamationIcon
  },

  mixins: [ErrorHandling, i18n],

  methods: {

    show() {
      this.$refs.lightbox.show();
    },
    
    softDelete() {
      this.$refs.lightbox.hide();
      this.$emit('softDelete');
    },

    forceDelete() {
      this.$refs.lightbox.hide();
      this.$emit('forceDelete');
    },
  }
}
</script>
