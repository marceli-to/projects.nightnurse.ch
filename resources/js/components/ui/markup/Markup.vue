<template>
  <div class="sm:grid sm:grid-cols-12 sm:gap-x-8">
    <div class="sm:col-span-9">

      <markup-menu 
        @addRectangle="addRectangle"
        @addCircle="addCircle"
        @addComment="addComment"
        @deleteElement="deleteElement"
        :hasSelectedShape="hasSelectedShape">
      </markup-menu>

      <div 
        ref="image"
        class="w-full aspect-video"
        :style="`background-image: url(${largeImageUri(image)}); background-repeat: no-repeat; background-size: contain; background-position: center;`">
        <v-stage 
          ref="stage"
          :config="configStage"
          @mousedown="handleStageMouseDown"
          @touchstart="handleStageMouseDown">
          <v-layer ref="layer">

            <div v-for="(element, index) in elements" :key="element.name">
              <template v-if="element.type === 'rect'">
                <v-rect :config="element" @transformend="handleTransformEnd" />
              </template>
              <template v-if="element.type === 'circle'">
                <v-circle :config="element" @transformend="handleTransformEnd" />
              </template>
              <template v-if="element.type === 'comment'">
                <v-image :config="element" @click="handleCommentClick"/>
              </template>
            </div>

            <v-transformer ref="transformer" />
          </v-layer>
        </v-stage>
      </div>
    </div>
    <div class="sm:col-span-3">
      <template v-if="hasCommentForm">
        <div class="form-group">
          <textarea 
            v-model="comment" 
            rows="5" 
            class="border-2 rounded p-1 lg:p-2 placeholder:text-sm lg:placeholder:text-base" 
            :placeholder="translate('Ihr Kommentar')">
          </textarea>
        </div>
      </template>
    </div>
  </div>
</template>
<script>
import i18n from "@/i18n";
import Konva from 'konva';
import { Stage, Layer, Rect, Circle, Image, Transformer } from 'vue-konva';
import Helpers from "@/mixins/Helpers";
import MarkupMenu from '@/components/ui/markup/Menu.vue';

export default {

  mixins: [i18n, Helpers],

  components: {
    MarkupMenu,
  },

  props: {
    image: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      configStage: {
        width: 200,
        height: 200
      },

      elements: [],

      selectedShapeName: '',
      selectedShapeType: null,

      commmentIcon: null,
      comment: null,

      // States
      hasSelectedShape: false,
      hasCommentForm: false,
    }
  },

  // set width and height of canvas to match image
  mounted() {
    this.configStage.width = this.$refs.image.clientWidth;
    this.configStage.height = this.$refs.image.clientHeight;

    // add event handler for delete key
    window.addEventListener('keydown', (e) => {
      if (e.keyCode === 46) {
        this.deleteElement();
      }
    });
  },

  created() {
    const commmentIcon = new window.Image();
    commmentIcon.src = "/assets/img/annotation.svg";
    commmentIcon.onload = () => {
      this.commmentIcon = commmentIcon;
    };

    const commmentIconActive = new window.Image();
    commmentIconActive.src = "/assets/img/annotation-active.svg";
    commmentIconActive.onload = () => {
      this.commmentIconActive = commmentIconActive;
    };
  },

  methods: {

    handleTransformEnd(e) {
      // shape is transformed, let us save new attrs back to the node
      // find element in our state
      const element = this.elements.find(
        (r) => r.name === this.selectedShapeName
      );
      // update the state
      element.x = e.target.x();
      element.y = e.target.y();
      element.rotation = e.target.rotation();
      element.scaleX = e.target.scaleX();
      element.scaleY = e.target.scaleY();
      const desiredStrokeWidth = 3; // The desired stroke width in pixels
      if (e.target.scaleX() > e.target.scaleY()) {
        element.strokeWidth = desiredStrokeWidth / e.target.scaleX();
      } else {
        element.strokeWidth = desiredStrokeWidth / e.target.scaleY();
      }
    },

    // handleCommentDragStart(e) {
    //   e.target.image(this.commmentIconActive);
    // },

    // handleCommentDragEnd(e) {
    //   e.target.image(this.commmentIcon);
    // },

    handleCommentClick(e) {
      this.resetCommentIcons(e.target.name());
      this.selectedShapeName = e.target.name();
      this.hasSelectedShape = true;
      this.hasCommentForm = true;
    },

    handleStageMouseDown(e) {
     
      // clicked on stage - clear selection
      if (e.target === e.target.getStage()) {
        this.selectedShapeName = 'null';
        this.hasSelectedShape = false;
        this.hasCommentForm = false;
        this.resetCommentIcons();
        this.updateTransformer();
        return;
      }

      // clicked on transformer - do nothing
      const clickedOnTransformer = e.target.getParent().className === 'Transformer';
      if (clickedOnTransformer) {
        this.hasSelectedShape = false;
        return;
      }

      const clickedOnComment = e.target.attrs.type == 'comment';
      if (clickedOnComment) {
        this.hasSelectedShape = true;
        this.selectedShapeName = e.target.name();

        // set the image to this.commmentIconActive
        e.target.image(this.commmentIconActive);
        return;
      }

      // find clicked elements by its name
      const name = e.target.name();
      const element = this.elements.find((r) => r.name === name);
      if (element) {
        this.selectedShapeName = name;
        this.hasSelectedShape = true;
      } else {
        this.selectedShapeName = '';
        this.hasSelectedShape = false;
      }
      this.updateTransformer();
    },

    updateTransformer() {
      // here we need to manually attach or detach Transformer node
      const transformerNode = this.$refs.transformer.getNode();
      const stage = transformerNode.getStage();
      const { selectedShapeName } = this;

      const selectedNode = stage.findOne('.' + selectedShapeName);
      // do nothing if selected node is already attached
      if (selectedNode === transformerNode.node()) {
        return;
      }

      if (selectedNode) {
        // attach to another node
        transformerNode.nodes([selectedNode]);
      } 
      else {
        // remove transformer
        transformerNode.nodes([]);
      }
    },

    resetCommentIcons(exceptName = null) {

      // get all comments from this.elements
      const comments = this.elements.filter(
        (r) => r.type === 'comment'
      );

      // remove exceptName from comments
      if (exceptName) {
        comments.forEach((element, index) => {
          if (element.name === exceptName) {
            comments.splice(index, 1);
          }
        });
      }

      comments.forEach((element) => {
        if (element.type === 'comment') {
          const commmentIcon = new window.Image();
          commmentIcon.src = "/assets/img/annotation.svg";
          commmentIcon.onload = () => {
            element.image = commmentIcon;
          };
        }
      });
    },

    // CRUD
    deleteElement() {
      
      const elements = this.elements.filter(
        (r) => r.name !== this.selectedShapeName
      );
      this.elements = elements;
      this.selectedShapeName = '';
      this.updateTransformer();
    },

    addRectangle() {
      const rect = {
        type: 'rect',
        x: 10,
        y: 10,
        width: 100,
        height: 100,
        scaleX: 1,
        scaleY: 1,
        rotation: 0,
        name: 'rect_' + this.getRandomString(),
        draggable: true,
        stroke: this.getRandomColor(),
        strokeWidth: 3,
      };
      this.elements.push(rect);
    },

    addCircle() {
      const circle = {
        type: 'circle',
        x: 150,
        y: 150,
        width: 100,
        height: 100,
        scaleX: 1,
        scaleY: 1,
        rotation: 0,
        name: 'circle_' + this.getRandomString(),
        draggable: true,
        stroke: 'blue',
        strokeWidth: 2,
      };
      this.elements.push(circle);
    },

    addComment() {
      const comment = {
        type: 'comment',
        x: 150,
        y: 150,
        name: 'comment_' + this.getRandomString(),
        draggable: true,
        image: this.commmentIcon,
      };
      this.elements.push(comment);
    },

    getRandomString() {
      const randString = Math.random().toString(36).substring(2,24);
      return randString;
    },

    getRandomColor() {
      const letters = '0123456789ABCDEF';
      let color = '#';
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    }
  },

};
</script>