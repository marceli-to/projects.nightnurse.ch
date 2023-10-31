<template>
  <div class="relative">
    <img 
      :src="largeImageUri(image)" 
      height="100" 
      width="100"
      class="w-full h-auto block"
      ref="markupRef"
      @click="showMarkerArea">
      
  </div>
</template>

<script>
import * as markerjs2 from 'markerjs2';
import Helpers from "@/mixins/Helpers";

export default {

  components: {
  },

  mixins: [
    Helpers
  ],
  
  props: {

    image: {
      type: Object,
      default: null
    },
  },

  data() {
    return {
      markupState: null,
    }
  },

  mounted() {
  },

  methods: {
    showMarkerArea() {
      // create a marker.js MarkerArea
      const markerArea = new markerjs2.MarkerArea(this.$refs.markupRef);
      markerArea.settings.displayMode = 'popup';
      // attach an event handler to assign annotated image back to our image element
      markerArea.addEventListener('render', event => {
        this.$refs.markupRef.src = event.dataUrl;
        this.markupState = event.state;
      });

      markerArea.addEventListener("close", (event) => {
        console.log(event.state);
        console.log(this.markupState);
      });

      // launch marker.js
      markerArea.show(); 
      
      if (this.markupState) {
        markerArea.restoreState(this.markupState);
      }

    }
  },

}
</script>