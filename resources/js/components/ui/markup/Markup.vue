<template>
<div class="w-full lg:flex lg:gap-x-4 pt-4 sm:pt-6 lg:pt-10 pb-32">
  <div class="w-full">
    <markup-menu 
      @addRectangle="addRectangle"
      @addCircle="addCircle"
      @addComment="addComment"
      @deleteElement="destroy"
      :canDelete="canDelete">
    </markup-menu>
    <div 
      ref="stage"
      class="w-full bg-no-repeat bg-contain bg-center-left bg-light"
      :style="`background-image: url(${largeImageUri(image)}); aspect-ratio: ${image.image_ratio}`">
      <vue-draggable-resizable 
        v-for="element in elements" :key="element.id"
        :w="element.shape.width" 
        :h="element.shape.height" 
        :x="element.shape.x"
        :y="element.shape.y"
        :class-name="getElementClassNames(element.uuid)"
        class-name-active="is-active" 
        @dragging="onDragging" 
        @resizing="onResize" 
        @resizestop="onResizeStop"
        @activated="onActivated(element.uuid)"
        @dragstop="onDragStop"
        :onDragStart="onDragStart"
        @deactivated="onDeactivated"
        :data-id="element.uuid"
        :parent="true"
        :draggable="element.draggable"
        :resizable="element.resizable">
        <template v-if="element.type === 'comment'">
          <annotation-icon class="h-6 w-6" aria-hidden="true" />
          <template v-if="element.uuid == selected && !isDragging">
            <textarea
              class="bg-highlight bg-opacity-80 p-1 text-white disabled:!text-white disabled:!opacity-100 mt-2 text-xs lg:p-2 w-40 min-h-[80px] !border-none rounded-md overflow-auto relative z-50"
              :disabled="!element.is_owner || element.is_locked"
              v-model="element.comment">
            </textarea>
          </template>
        </template>
      </vue-draggable-resizable>
    </div>
  </div>
  <div class="mt-5 lg:mt-0 w-full lg:w-[250px] xl:w-[350px]">
    <template v-if="comments.length">
      <markup-comment 
        v-for="(comment, index) in comments" 
        :key="comment.uuid" 
        :comment="comment"
        :highlighted="highlightedComment"
        @mouseover="addHighlight"
        @mouseout="removeHighlight">
      </markup-comment>
    </template>
  </div>
  <content-footer>
    <router-link :to="{name: 'messages', params: { slug: $props.project.slug, uuid: $props.project.uuid }}" class="form-helper form-helper-footer">
      <arrow-left-icon class="h-5 w-5" aria-hidden="true" />
      <span>{{ translate('Zurück') }}</span>
    </router-link>
    <a href="javascript:;" :class="[hasUnlockedElements == false ? 'pointer-events-none !bg-gray-200' : '', 'btn-create']" @click="lock()">
      <save-icon class="h-5 w-5" aria-hidden="true" />
      <span class="block ml-2">{{ translate('Speichern & Freigeben') }}</span>
    </a>
  </content-footer>
</div>
</template>

<script>
// https://vuejsexamples.com/vue2-component-for-resizable-rotable-and-draggable-elements/
import { ArrowLeftIcon, SaveIcon, AnnotationIcon } from "@vue-hero-icons/outline";
import Helpers from "@/mixins/Helpers";
import i18n from "@/i18n";
import NProgress from 'nprogress';
import VueDraggableResizable from 'vue-draggable-resizable'
import MarkupMenu from '@/components/ui/markup/Menu.vue';
import MarkupComment from '@/components/ui/markup/Comment.vue';
import ContentFooter from "@/components/ui/layout/Footer.vue";

export default {

  components: {
    ContentFooter,
    ArrowLeftIcon,
    SaveIcon,
    AnnotationIcon,
    VueDraggableResizable,
    MarkupMenu,
    MarkupComment,
  },

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

      // Elements
      elements: [],

      // Comments
      comments: [],

      // Acitve comment
      highlightedComment: null,

      // Dragging
      dragStartX: 0,
      dragStartY: 0,

      // Selected element
      selected: null,
      lastSelected: null,

      // Stage dimensions
      stageRectangle: null,

      // Temp comment used for blur event
      tempComment: null,

      // States
      isFetched: false,
      isDragging: false,
      canDelete: false,
      hasUnlockedElements: false,

      // Routes
      routes: {
        get: '/api/markups',
        lock: '/api/markups/lock',
        create: '/api/markup',
        update: '/api/markup',
        delete: '/api/markup',
      },
    }
  },

  mounted() {
    this.fetch();
    this.stageRectangle = this.$refs.stage.getBoundingClientRect();
    window.addEventListener('resize', this.onResizeWindow);
    window.addEventListener('keydown', this.onKeyDown);
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.onResizeWindow);
  },

  methods: {

    // CRUD
    addRectangle() {
      const element = {
        is_locked: false,
        type: 'rectangle',
        resizable: true,
        draggable: true,
        shape: {
          id: this.getUuid(),
          className: 'shape', 
          width: 100, 
          height: 100, 
          x: 10, 
          y: 10, 
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    addCircle() {
      const element = {
        is_locked: false,
        type: 'circle',
        resizable: true,
        draggable: true,
        shape: {
          id: this.getUuid(),
          width: 100, 
          height: 100, 
          x: 10, 
          y: 10,
          className: 'shape shape--circle', 
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    addComment() {
      const element = {
        is_locked: false,
        type: 'comment',
        resizable: false,
        draggable: true,
        commentable: true,
        shape: {
          id: this.getUuid(),
          width: 20, 
          height: 20, 
          x: 10,
          y: 10,
          className: 'is-active shape shape--comment', 
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    create(element) {
      const data = {
        uuid: this.$props.image.uuid,
        element: element,
      };
      this.axios.post(this.routes.create, data).then(response => {
        element.uuid = response.data.markup.uuid;
        element.is_owner = response.data.markup.is_owner;
      });
    },

    // CRUD
    fetch() {
      NProgress.start();
      this.elements = [];
      this.comments = [];
      this.axios.get(`${this.routes.get}/${this.$props.image.uuid}`).then((response) => {
        const data = response.data.data;
        data.forEach((element) => {
          if (element.type === 'comment') {
            if (element.comment) {
              this.comments.push({
                uuid: element.uuid,
                comment: element.comment,
                author: element.author,
                author_company: element.author_company,
                date: element.date,
              });
            }
          }
          this.elements.push(element);
        });
        NProgress.done();
        this.setShape();
      })
    },

    update(element) {
      const data = {
        uuid: this.$props.image.uuid,
        element: element,
      };
      this.axios.put(`${this.routes.update}/${element.uuid}`, data).then(response => {
        if (element.comment && response.data.markup.type == 'comment') {

          // update or push comment
          const comment = this.comments.find(
            (r) => r.uuid === response.data.markup.uuid
          );
          if (comment) {
            comment.comment = response.data.markup.comment;
          }
          else {
            this.comments.push({
              uuid: response.data.markup.uuid,
              comment: response.data.markup.comment,
              author: response.data.markup.author,
              author_company: response.data.markup.author_company,
              date: response.data.markup.date,
            });
          }
          this.tempComment = null;
        }
      });
    },

    destroy() {
      if (!this.lastSelected) {
        return;
      }
      this.axios.delete(`${this.routes.delete}/${this.lastSelected}`).then(response => {
        this.removeElement();
      });
    },

    lock() {
      NProgress.start();
      this.axios.get(`${this.routes.lock}/${this.$props.image.uuid}`).then(response => {
        NProgress.done();
        this.$notify({ type: "success", text: this.translate('Markierungen und Kommentare gespeichert') });
        this.fetch();
        this.canDelete = false;
      });
    },

    getElement(id) {
      return this.elements.find(el => el.uuid === id);
    },

    getElementClassNames(id) {
      const element = this.getElement(id);
      let classNames = element.shape.className;

      if (!element.is_owner) {
        classNames += ' is-disabled';
      }

      if (element.is_locked) {
        classNames += element.is_locked ? ' is-locked' : '';
      }
      return classNames;
    },

    removeElement() {
      // find element in this.elements
      const elements = this.elements.filter(
        (r) => r.uuid !== this.lastSelected
      );
      
      // find comment in this.comments
      const comment = this.comments.find(
        (r) => r.uuid === this.lastSelected
      );

      // remove comment from this.comments
      if (comment) {
        const index = this.comments.indexOf(comment);
        this.comments.splice(index, 1);
      }

      this.elements = elements;
      this.lastSelected = null
      this.canDelete = false;
    },

    setShape() {
      this.elements.forEach(element => {
        if (!element.shape.relative_attributes) {
          return;
        }
        const stageRect = this.$refs.stage.getBoundingClientRect();
        const elementX = stageRect.width * element.shape.relative_attributes.x / 100;
        const elementY = stageRect.height * element.shape.relative_attributes.y / 100;
        const elementWidth = stageRect.width * element.shape.relative_attributes.width / 100;
        const elementHeight = stageRect.height * element.shape.relative_attributes.height / 100;

        // round to 0 decimal places, parse to int
        element.shape.x = parseInt(elementX.toFixed(0));
        element.shape.y = parseInt(elementY.toFixed(0));
        element.shape.width = parseInt(elementWidth.toFixed(0));
        element.shape.height = parseInt(elementHeight.toFixed(0));
      });
    },

    updateShape() {
      // get the current container dimensions
      const containerRect = this.$refs.stage.getBoundingClientRect();
      // calculate the ratio of old dimensions to new dimensions
      const widthRatio = containerRect.width / this.stageRectangle.width;
      const heightRatio = containerRect.height / this.stageRectangle.height;
      // update the initial container dimensions for next time
      this.stageRectangle = containerRect;
      // update the element dimensions
      this.elements.forEach(element => {
        element.shape.x *= widthRatio;
        element.shape.y *= heightRatio;
        element.shape.width *= widthRatio;
        element.shape.height *= heightRatio;
      });
    },

    // Events
    onResize(x, y, width, height) {
      const element = this.getElement(this.selected);
      if (element) {
        element.x = x
        element.y = y
        element.width = width
        element.height = height
      }
    },

    onResizeStop(x, y, width, height) {
      const element = this.getElement(this.selected);
      if (element) {
        element.shape.x = x
        element.shape.y = y
        element.shape.width = width
        element.shape.height = height
        this.update(element);
      }
    },

    onDragging(x, y) {
      this.isDragging = true;
      const element = this.getElement(this.selected);
      if (element) {
        element.shape.x = x;
        element.shape.y = y;
      }
    },

    onDragStart(x, y) {
      this.dragStartX = x;
      this.dragStartY = y;
    },

    onDragStop(x, y) {
      this.isDragging = false;
      const element = this.getElement(this.selected);
      // update only if the element has moved
      const canUpdate = (this.dragStartX !== x || this.dragStartY !== y) ? true : false;
      if (element && canUpdate) {
        element.shape.x = x;
        element.shape.y = y;

        // get the x and y position and the width and height of the element relative to the stage in percent
        const stageRect = this.$refs.stage.getBoundingClientRect();
        const elementRect = document.querySelector(`[data-id="${element.uuid}"]`).getBoundingClientRect();
        const elementY = (elementRect.top - stageRect.top) / stageRect.height * 100;
        const elementX = (elementRect.left - stageRect.left) / stageRect.width * 100;
        const elementWidth = elementRect.width / stageRect.width * 100;
        const elementHeight = elementRect.height / stageRect.height * 100;

        // create an object 'relative'
        element.shape.relative_attributes = {
          x: elementX,
          y: elementY,
          width: elementWidth,
          height: elementHeight,
        };

        // if the element.shape.x is right of theh center of the stage, move the comment to the left
        if (element.type == 'comment') {
          if (element.shape.x > stageRect.width / 2) {
            element.shape.className = 'shape shape--comment shape--comment-rtl';
          }
          else {
            element.shape.className = 'shape shape--comment shape--comment-ltr';
          }
        }

        this.dragStartX = null;
        this.dragStartY = null;
        this.update(element);
      }
    },

    addHighlight(id) {
      // add class 'is-active' to the element
      const element = this.getElement(id);
      if (element) {
        element.shape.className += ' is-active';
        this.highlightedComment = id;
      }
    },

    removeHighlight(id) {
      this.elements.forEach(element => {
        element.shape.className = element.shape.className.replace(' is-active', '');
      });
      this.highlightedComment = null;
    },

    onActivated(id) {
      const element = this.getElement(id);
      this.selected = id;

      if (element.type === 'comment') {
        this.tempComment = element.comment;
        this.highlightedComment = id;
      }

      if (!element.is_owner) {
        return;
      }
      this.canDelete = element.is_locked ? false : true;
      this.lastSelected = id;
    },

    onDeactivated() {
      const element = this.getElement(this.selected);
      if (element.is_owner && element.comment && element.comment !== this.tempComment) {
        this.update(element);
      }
      this.selected = null;
      this.highlightedComment = null;
    },

    onResizeWindow() {
      this.updateShape();
    },

    onKeyDown(event) {
      if (!this.lastSelected || !this.canDelete) {
        return;
      }
      if (event.key === 'Delete') {
        this.destroy();
      }
    },

    // Helpers
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
    },

    getUuid() {
      let dt = new Date().getTime();
      const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = (dt + Math.random()*16)%16 | 0;
        dt = Math.floor(dt/16);
        return (c=='x' ? r :(r&0x3|0x8)).toString(16);
      });
      return uuid;
    },

  },

  watch: {
    // watch for changes in elements (attribute 'is_locked')
    elements: {
      handler: function () {
        // if any element has 'is_locked' = false, alert('has unlocked elements')
        const unlockedElements = this.elements.filter(
          (r) => r.is_locked == false && r.is_owner == true
        );
        if (unlockedElements.length) {
          this.hasUnlockedElements = true;
        }
        else {
          this.hasUnlockedElements = false;
        }
      },
      deep: true
    }
  },
}
</script>

```
