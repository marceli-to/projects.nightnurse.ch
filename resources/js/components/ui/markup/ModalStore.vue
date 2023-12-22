<template>
  <lightbox ref="lightbox" :maxWidth="'max-w-[360px] md:max-w-[480px]'">
    <div>
      <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">
        {{ translate('Aktion auswählen') }}
      </h2>
      <p>
        {{ translate('Möchten Sie zusätzlich zu den Kommentaren noch eine Nachricht erfassen?') }}
      </p>
      <div class="flex flex-col md:flex-row gap-y-2 md:gap-y-0 md:gap-x-2 md:justify-between">
        <a 
          href="javascript:;" 
          class="btn-primary !bg-gray-400 hover:!bg-dark p-1 md:!py-3 md:!px-4 !w-auto !flex justify-center items-center md:leading-0"
          @click="save()">
          {{ translate('Speichern & Schliessen') }}
        </a>
        <a 
          href="javascript:;" 
          class="btn-primary p-1 md:!py-3 md:!px-4 !w-auto !flex justify-center items-center md:leading-0"
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