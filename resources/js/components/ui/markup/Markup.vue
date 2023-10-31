<template>

  <div 
    ref="stage"
    class="w-full aspect-video"
    :style="`background-image: url(${largeImageUri(image)}); background-repeat: no-repeat; background-size: contain; background-position: center;`">
    <fabric-canvas :width="width" :height="height">
      <fabric-circle 
        :id="2"
        :borderColor="'yellow'"
        :fill="'none'"
      >
      </fabric-circle>
      <fabric-rectangle 
        :id="1"
        :borderColor="'red'"
        :strokeWidth="1"
        :stroke="'red'"
        :fill="'rgba(0,0,0,0)'">
      </fabric-rectangle>
    </fabric-canvas>
</div>
</template>
<script>
import Helpers from "@/mixins/Helpers";
import vueFabricWrapper from "vue-fabric-wrapper";

export default {
  name: "App",
  mixins: [Helpers],
  props: {
    image: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      width: 0,
      height: 0,
    }
  },
  components: {
    FabricCanvas: vueFabricWrapper.FabricCanvas,
    FabricCircle: vueFabricWrapper.FabricCircle,
    Rectangle: vueFabricWrapper.Rectangle,
    FabricImage: vueFabricWrapper.FabricImageFromURL
  },

  // set width and height of canvas to match image
  mounted() {
    this.width = this.$refs.stage.clientWidth;
    this.height = this.$refs.stage.clientHeight;
  },


};
</script>