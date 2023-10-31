<template>
  <div class="relative">
    <nav class="bg-black pl-2 py-2 !leading-none">
      <ul class="flex gap-x-4 !m-0 !p-0">
        <li class="block !m-0 !p-0">
          <a href="" :class="[mode == 'resize' ? 'text-red-400' : 'text-gray-100', 'text-xs text-white font-mono no-underline']" @click.prevent="toggleMode('resize')">
            Resize
          </a>
        </li>
        <li class="block !m-0 !p-0">
          <a href="" class="text-xs text-white font-mono no-underline" @click.prevent="toggleMode('move')">Move</a>
        </li>
      </ul>
    </nav>
    <div 
      height="100" 
      width="100"
      class="w-full h-auto block aspect-video"
      :style="`background-image: url(${largeImageUri(image)}); background-size: cover; backhround-position: top left; background-repeat: no-repeat;`"
      ref="image"
      @click="addSquare">
      <div
        v-for="(circle, index) in circles"
        :key="index"
        class="circle-markup"
        :style="{ top: circle.y + 'px', left: circle.x + 'px', width: circle.radius + 'px', height: circle.radius + 'px' }"
        @mousedown="startResizeCircle(index, $event)"
      ></div>
      <div
        v-for="(square, index) in squares"
        :key="index"
        class="square-markup"
        :style="{ top: square.y + 'px', left: square.x + 'px', width: square.width + 'px', height: square.height + 'px', borderColor: resizingSquareIndex === index ? 'red' : 'green' }"
        @mousedown="startResizeSquare(index, $event)"
      ></div>

    </div>

  </div>
</template>

<script>
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
      state: null,
      mode: null,
      squares: [], // Array to store square markups
      circles: [], // Array to store circle markups
      resizingCircleIndex: null, // Index of the circle being resized
      resizingSquareIndex: null, // Index of the square being resized
      startResizeCircleX: 0,
      startResizeCircleY: 0,
    }
  },

  mounted() {
  },

  methods: {
    addCircle(event) {
      // Get the click coordinates relative to the image
      const image = this.$refs.image;
      const rect = image.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      // Add a default-sized circle markup
      this.circles.push({ x, y, radius: 40 });
    },

    startResizeCircle(index, event) {
      this.resizingCircleIndex = index;
      this.startResizeCircleX = event.clientX;
      this.startResizeCircleY = event.clientY;

      // Capture mousemove and mouseup events for resizing
      window.addEventListener('mousemove', this.resizeCircle);
      window.addEventListener('mouseup', this.stopResizeCircle);
    },

    resizeCircle(event) {
      if (this.resizingCircleIndex !== null) {
        const circle = this.circles[this.resizingCircleIndex];
        const dx = event.clientX - this.startResizeCircleX;
        const dy = event.clientY - this.startResizeCircleY;
        
        // Adjust the radius based on the mouse movement
        circle.radius += dx;

        // Ensure the radius is non-negative
        if (circle.radius < 0) {
          circle.radius = 0;
        }

        // Update the starting point for the next mousemove event
        this.startResizeCircleX = event.clientX;
        this.startResizeCircleY = event.clientY;
      }
    },

    stopResizeCircle() {
      // Stop capturing mousemove and mouseup events
      window.removeEventListener('mousemove', this.resizeCircle);
      window.removeEventListener('mouseup', this.stopResizeCircle);
      this.resizingCircleIndex = null;
    },

    // Square
    addSquare(event) {
      if (this.mode === 'resize') {
        return;
      }
      // Get the click coordinates relative to the image
      const image = this.$refs.image;
      const rect = image.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      // Add a default-sized circle markup
      this.squares.push({ x, y, width: 40, height: 40 });
    },

    startResizeSquare(index, event) {
      this.mode = 'resize';
      this.resizingSquareIndex = index;
      this.startResizeSquareX = event.clientX;
      this.startResizeSquareY = event.clientY;

      // Capture mousemove and mouseup events for resizing
      window.addEventListener('mousemove', this.resizeSquare);
      window.addEventListener('mouseup', this.stopResizeSquare);
    },

    resizeSquare(event) {
      if (this.resizingSquareIndex !== null) {
        const square = this.squares[this.resizingSquareIndex];
        const dx = event.clientX - this.startResizeSquareX;
        const dy = event.clientY - this.startResizeSquareY;
        
        // Adjust the radius based on the mouse movement
        square.width += dx;
        square.height += dy;

        // Ensure the radius is non-negative
        if (square.width < 0) {
          square.width = 0;
        }
        if (square.height < 0) {
          square.height = 0;
        }

        // Update the starting point for the next mousemove event
        this.startResizeSquareX = event.clientX;
        this.startResizeSquareY = event.clientY;
      }
    },

    stopResizeSquare() {
      // Stop capturing mousemove and mouseup events
      window.removeEventListener('mousemove', this.resizeSquare);
      window.removeEventListener('mouseup', this.stopResizeSquare);
      this.resizingSquareIndex = null;
    },

    toggleMode(mode) {
      this.mode = this.mode == 'resize' ? null : 'resize';
    },
  },

}
</script>
<style>
.circle-markup {
  position: absolute;
  border: 2px solid #ff0000; /* Red border for circles */
  border-radius: 50%; /* Make it a circle */
  background-color: transparent;
  cursor: pointer;
}

.square-markup {
  position: absolute;
  border: 2px solid #ff0000; /* Red border for circles */
  background-color: transparent;
  cursor: pointer;
}
</style>