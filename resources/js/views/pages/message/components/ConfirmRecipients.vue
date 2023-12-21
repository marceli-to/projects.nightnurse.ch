<template>
  <lightbox ref="lightbox" :maxWidth="'max-w-[280px] md:max-w-[400px]'">
    <div>
      <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">
        {{ translate('Empfänger bestätigen') }}
      </h2>
      <p class="text-dark">{{ message }}</p>
      <footer>
        <div class="flex justify-between items-center mt-4 lg:mt-6">
          <button type="submit" class="btn-secondary" @click.prevent="cancel()">
            {{ translate('Abbrechen') }}
          </button>
          <button type="submit" class="btn-primary p-2 md:!py-3 md:!px-3" @click.prevent="confirm()">
            {{ translate('Bestätigen') }}
          </button>
        </div>
      </footer>
    </div>
  </lightbox>
</template>
<script>
import i18n from "@/i18n";
import ErrorHandling from "@/mixins/ErrorHandling";
import Lightbox from "@/components/ui/layout/Lightbox.vue";

export default {

  components: {
    Lightbox
  },

  mixins: [ErrorHandling, i18n],

  data() {
    return {
      message: '',
      resolvePromise: undefined,
      rejectPromise: undefined,
    }
  },

  methods: {

    show(opts = {}) {
      this.$refs.lightbox.show();
      this.message = opts.message;
      return new Promise((resolve, reject) => {
        this.resolvePromise = resolve;
        this.rejectPromise = reject;
      })
    },
    
    cancel() {
      this.$refs.lightbox.hide();
      this.resolvePromise(false);
    },

    confirm() {
      this.$refs.lightbox.hide();
      this.resolvePromise(true);
    },
  }
}
</script>
