<template>
  <div 
    ref="stage"
    class="w-full aspect-video bg-no-repeat bg-contain bg-center bg-light"
    :style="`background-image: url(${largeImageUri(image)});`">
    <vue-draggable-resizable 
      :w="element.width" 
      :h="element.height" 
      :x="element.x"
      :y="element.y"
      :class-name="element.className"
      class-name-active="is-active" 
      @dragging="onDrag" 
      @resizing="onResize" 
      @activated="onActivated(element.id)"
      @deactivated="onDeactivated"
      :data-id="element.id"
      :parent="true"
      :resizable="element.resizable"
      v-for="element in elements" :key="element.id">
      <!-- X: {{ element.x }} / Y: {{ element.y }} - Width: {{ element.width }} / Height: {{ element.height }} -->
    </vue-draggable-resizable>
  </div>
</template>
<script>
// https://vuejsexamples.com/vue2-component-for-resizable-rotable-and-draggable-elements/
import VueDraggableResizable from 'vue-draggable-resizable'
import Helpers from "@/mixins/Helpers";
import i18n from "@/i18n";

export default {

  mixins: [i18n, Helpers],

  props: {
    project: {
      type: Object,
      default: null,
    },
    image: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      elements: [
        { id: 1, width: 100, height: 100, x: 0, y: 0, className: 'shape', resizable: true },
        { id: 2, width: 100, height: 100, x: 0, y: 0, className: 'shape shape--circle', resizable: true },
        { id: 3, width: 50, height: 50, x: 0, y: 0, className: 'shape', resizable: true }
      ],

      selected: null,
      initialContainerRect: null, // store initial dimensions
    }
  },

  mounted() {
    this.initialContainerRect = this.$refs.stage.getBoundingClientRect();
    window.addEventListener('resize', this.onResizeWindow);
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.onResizeWindow);
  },

  methods: {
    onResize(x, y, width, height) {
      const element = this.elements.find(el => el.id === this.selected)
      if (element) {
        element.x = x
        element.y = y
        element.width = width
        element.height = height
      }
    },

    onDrag(x, y) {
      const element = this.elements.find(el => el.id === this.selected)
      if (element) {
        element.x = x
        element.y = y
      }
    },

    onActivated(id) {
      this.selected = id;
    },

    onDeactivated() {
      this.selected = null;
    },

    onResizeWindow() {
      // get the current container dimensions
      const containerRect = this.$refs.stage.getBoundingClientRect();
      // calculate the ratio of old dimensions to new dimensions
      const widthRatio = containerRect.width / this.initialContainerRect.width;
      const heightRatio = containerRect.height / this.initialContainerRect.height;
      // update the initial container dimensions for next time
      this.initialContainerRect = containerRect;
      // update the element dimensions
      this.elements.forEach(element => {
        element.x *= widthRatio;
        element.y *= heightRatio;
        element.width *= widthRatio;
        element.height *= heightRatio;
      });
    }
  }
}
</script>

```
