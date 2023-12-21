<template>
  <lightbox ref="lightbox" :maxWidth="'max-w-[280px] md:max-w-[460px]'">
    <div>
      <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">
        {{ translate('Aktion auswählen') }}
      </h2>
      <p>
        {{ translate('Wählen Sie eine Aktion aus, um die Kommentare und Markierungen zu speichern und, falls gewünscht, eine Nachricht an die Projektbeteiligten zu senden.') }}
      </p>
      <div class="flex gap-x-2 justify-between">
        <a 
          href="javascript:;" 
          class="btn-primary !bg-gray-400 hover:!bg-dark p-2 md:!py-3 md:!px-4 !w-auto !flex justify-center items-center"
          @click="save()">
          {{ translate('Speichern & zurück') }}
        </a>
        <a 
          href="javascript:;" 
          class="btn-primary p-2 md:!py-3 md:!px-4 !w-auto !flex justify-center items-center"
          @click="saveWithMessage()">
          {{ translate('Speichern & Nachricht') }}
        </a>
      </div>
    </div>
  </lightbox>
</template>

<script>
import i18n from "@/i18n";
import { SaveIcon } from "@vue-hero-icons/outline";
import Lightbox from "@/components/ui/layout/Lightbox.vue";

export default {
  components: {
    Lightbox,
    SaveIcon,
  },
  
  mixins: [i18n],

  data() {
    return {
    }
  },
  props: {
   
  },
  methods: {

    show() {
      this.$refs.lightbox.show();
    },

    hide() {
      this.$refs.lightbox.hide();
    },

    save() {
      this.$emit('save');
      this.hide();
    },

    saveWithMessage() {
      this.$emit('saveWithMessage');
      this.hide();
    },
  }
}
</script>