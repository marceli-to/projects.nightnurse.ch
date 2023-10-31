<template>
  <div class="relative">
    <nav class="bg-black pl-2 py-2 !leading-none">
      <ul class="flex gap-x-4 !m-0 !p-0">
        <li class="block !m-0 !p-0">
          <select class="p-0 bg-black accent-white text-white !ring-0 !shadow-none" v-model="selectedShape">
            <option value="circle">Circle</option>
            <option value="square">Square</option>
          </select>
        </li>
        <li>
          <a href="" class="text-xs text-white font-mono no-underline" @click.prevent="toggleMode('resize')">Resize</a>
        </li>
        <li>
          <a href="" class="text-xs text-white font-mono no-underline" @click.prevent="toggleMode('move')">Move</a>
        </li>
      </ul>
    </nav>
    <div
      height="100"
      width="100"
      class="w-full h-auto block aspect-video"
      :style="`background-image: url(${largeImageUri(image)}); background-size: cover; background-position: top left; background-repeat: no-repeat;`"
      ref="image"
      @mousedown="startModeAction"
    >
      <!-- SVG shape overlay -->
      <svg
        v-if="selectedShape"
        :width="shapeWidth"
        :height="shapeHeight"
        :style="{
          position: 'absolute',
          left: `${shapeLeft}px`,
          top: `${shapeTop}px`,
          cursor: mode === 'resize' ? 'nwse-resize' : 'move',
        }"
        @mousedown.stop="startModeAction"
      >
        <template v-if="selectedShape === 'circle'">
          <!-- Create a resizable circle -->
          <circle
            :cx="shapeWidth / 2"
            :cy="shapeHeight / 2"
            :r="shapeWidth / 2"
            fill="transparent"
            stroke="red"
          />
        </template>
        <template v-else-if="selectedShape === 'square'">
          <!-- Create a resizable square -->
          <rect
            :width="shapeWidth"
            :height="shapeHeight"
            fill="transparent"
            stroke="blue"
            stroke-width="4"
        </template>
      </svg>
    </div>
  </div>
</template>

<script>
import Helpers from "@/mixins/Helpers";

export default {
  components: {},
  mixins: [Helpers],
  props: {
    image: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      selectedShape: null,
      shapeLeft: 0,
      shapeTop: 0,
      shapeWidth: 50, // Initial width
      shapeHeight: 50, // Initial height
      mode: 'move', // Initial mode is 'move'
      isActionInProgress: false,
      actionStartX: 0,
      actionStartY: 0,
    };
  },

  methods: {
    toggleMode(newMode) {
      this.mode = newMode;
    },
    startModeAction(event) {
      if (this.selectedShape) {
        this.isActionInProgress = true;
        this.actionStartX = event.clientX;
        this.actionStartY = event.clientY;
      }
    },
    modeAction(event) {
      if (this.isActionInProgress) {
        if (this.mode === 'resize') {
          // Resize logic
          this.shapeWidth += event.clientX - this.actionStartX;
          this.shapeHeight += event.clientY - this.actionStartY;
        } else if (this.mode === 'move') {
          // Move logic
          this.shapeLeft += event.clientX - this.actionStartX;
          this.shapeTop += event.clientY - this.actionStartY;
        }

        this.actionStartX = event.clientX;
        this.actionStartY = event.clientY;
      }
    },
    endModeAction() {
      this.isActionInProgress = false;
    },
  },
  created() {
    window.addEventListener("mousemove", this.modeAction);
    window.addEventListener("mouseup", this.endModeAction);
  },
  beforeDestroy() {
    window.removeEventListener("mousemove", this.modeAction);
    window.removeEventListener("mouseup", this.endModeAction);
  },
};
</script>
