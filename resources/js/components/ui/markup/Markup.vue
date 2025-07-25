<template>
<div class="min-w-[700px]">
  <div class="grid grid-cols-markup bg-white">
    <markup-menu 
      @addRectangle="addRectangle"
      @addCircle="addCircle"
      @addComment="addComment"
      @deleteElement="destroy"
      @selectShape="selectShape"
      @selectImage="fetch"
      :selectedShape="selectedShape"
      :canDelete="canDelete"
      :image="currentImage ? currentImage : $props.image"
      :images="$props.images"
      :project="$props.project">
    </markup-menu>
    <div 
      ref="stage"
      class="w-auto max-h-[100vh] overflow-hidden relative"
      :style="`aspect-ratio: ${currentImage ? currentImage.image_ratio : $props.image.image_ratio}`">
      <img 
        :src="largeImageUri(currentImage ? currentImage : $props.image)" 
        width="100%" 
        height="100%" 
        draggable="false"
        class="absolute max-h-[100vh] object-contain top-0 left-0 !m-0 !select-none -z-1" 
        ref="stageImage" 
        :style="`aspect-ratio: ${currentImage ? currentImage.image_ratio : $props.image.image_ratio}; drag: none; user-select: none;`"/>
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
        :data-shape-id="element.shape.id"
        :parent="true"
        :rotatable="true"
        :draggable="element.draggable"
        :resizable="element.resizable">
        <template v-if="element.type === 'comment'">
          <div class="relative">
            <annotation-icon class="h-6 w-6" aria-hidden="true" />
            <span 
              class="absolute -top-3 -right-3 bg-blue-700 text-white text-xs rounded-full w-[20px] h-[20px] font-bold flex items-center justify-center leading-none"
              v-if="getCommentNumber(element.uuid)">
              {{ getCommentNumber(element.uuid) }}
            </span>
          </div>
          <template v-if="element.uuid == selected && !isDragging && !isFresh">
            <textarea
              class="bg-highlight bg-opacity-80 p-1 text-white disabled:!text-white disabled:!opacity-100 mt-1 text-xs lg:p-2 w-40 min-h-[80px] !border-none rounded-md overflow-auto relative z-50"
              :disabled="!element.is_owner || element.is_locked"
              v-model="element.comment">
            </textarea>
          </template>
        </template>
      </vue-draggable-resizable>
    </div>
    <div class="bg-white border-l-2 border-l-gray-100 pt-2 pb-4 px-2 flex flex-col justify-between min-h-screen">
      <div class="max-h-[calc(100dvh_-_125px)] overflow-y-auto pr-1">
        <template v-if="comments.length">
          <markup-comment 
            v-for="(comment, index) in sortedComments" 
            :key="comment.uuid" 
            :comment="comment"
            :comment-number="index + 1"
            :highlighted="highlightedComment"
            @mouseover="addHighlight"
            @mouseout="removeHighlight">
          </markup-comment>
        </template>
      </div>
      <div class="flex justify-end">
        <template v-if="hasUnlockedElements || $store.state.hasUnlockedMarkUps">
          <a href="javascript:;" class="btn-create w-full" @click="$refs.modalStore.show()">
            <span class="block mr-2">{{ translate('Weiter') }}</span>
            <arrow-narrow-right-icon class="h-5 w-5" aria-hidden="true" />
          </a>
        </template>
      </div>
    </div>
  </div>
  <markup-modal-store ref="modalStore" @save="save" @saveWithMessage="saveWithMessage" />
</div>
</template>

<script>
import { SaveIcon, AnnotationIcon, ChevronLeftIcon, ChevronRightIcon, ArrowNarrowRightIcon } from "@vue-hero-icons/outline";
import Helpers from "@/mixins/Helpers";
import i18n from "@/i18n";
import NProgress from 'nprogress';
import VueDraggableResizable from 'vue-draggable-resizable'
import MarkupMenu from '@/components/ui/markup/Menu.vue';
import MarkupComment from '@/components/ui/markup/Comment.vue';
import MarkupModalStore from '@/components/ui/markup/ModalStore.vue';
import ContentFooter from "@/components/ui/layout/Footer.vue";

export default {

  components: {
    ContentFooter,
    SaveIcon,
    AnnotationIcon,
    ChevronRightIcon,
    ChevronLeftIcon,
    ArrowNarrowRightIcon,
    VueDraggableResizable,
    MarkupMenu,
    MarkupComment,
    MarkupModalStore,
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

    images: {
      type: Array,
      default: null,
    },
  },

  data() {
    return {

      // Elements
      elements: [],

      // Comments
      comments: [],

      // Active image
      currentImage: null,

      // Acitve comment
      highlightedComment: null,

      // Dragging
      dragStartX: 0,
      dragStartY: 0,

      // Selected element
      selected: null,
      selectedShape: null,
      lastSelected: null,

      // Stage dimensions
      stageRectangle: null,

      // Temp comment used for blur event
      tempComment: null,

      // States
      isFetched: false,
      isDragging: false,
      isFresh: false,
      canDelete: false,
      hasUnlockedElements: false,

      // Routes
      routes: {
        get: '/api/markups',
        save: '/api/markups/save',
        create: '/api/markup',
        update: '/api/markup',
        delete: '/api/markup',
      },
    }
  },

  mounted() {
    this.currentImage = this.$props.image;
    this.fetch(this.$props.image.uuid);
    this.stageRectangle = this.$refs.stage.getBoundingClientRect();

    window.addEventListener('resize', this.onResizeWindow);
    window.addEventListener('keydown', this.onKeyDown);

    // Add event listener for 'click on stage' and add element
    this.$refs.stage.addEventListener('click', (event) => {
      if (!this.selectedShape) {
        return;
      }
      const stageRect = this.$refs.stage.getBoundingClientRect();
      const stageX = event.clientX - stageRect.left;
      const stageY = event.clientY - stageRect.top;

      switch (this.selectedShape) {
        case 'circle':
          this.addCircle(stageX, stageY);
          this.$refs.stage.style.cursor = 'default';
          this.selectedShape = null;
        break;
        case 'rectangle':
          this.addRectangle(stageX, stageY);
          this.$refs.stage.style.cursor = 'default';
          this.selectedShape = null;
        break;
        case 'comment':
          this.addComment(stageX, stageY);
          this.$refs.stage.style.cursor = 'default';
          this.selectedShape = null;
        break;
      }

    });

    // On right mouse click, remove the selected element
    this.$refs.stage.addEventListener('contextmenu', (event) => {
      // only if this.selectedShape is not null
      if (!this.selectedShape) {
        return;
      }
      event.preventDefault();
      this.cancelShape();
    });

    // On 'esc' key, remove the selected element
    window.addEventListener('keyup', (event) => {
      if (event.key === 'Escape') {
        this.cancelShape();
      }
    });
  },

  beforeDestroy() {
    window.removeEventListener('resize', this.onResizeWindow);
    window.addEventListener('keydown', this.onKeyDown);
    this.$refs.stage.removeEventListener('click', this.onStageClick);
  },

  methods: {

    // CRUD
    selectShape(shape) {
      this.$refs.stage.style.cursor = 'copy';
      this.selectedShape = shape;
    },

    cancelShape() {
      this.$refs.stage.style.cursor = 'default';
      this.selectedShape = null;
    },

    addRectangle(x, y) {
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
          x: x - 50, 
          y: y - 50, 
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    addCircle(x, y) {
      const element = {
        is_locked: false,
        type: 'circle',
        resizable: true,
        draggable: true,
        shape: {
          id: this.getUuid(),
          width: 100, 
          height: 100, 
          x: x - 50, 
          y: y - 50,
          className: 'shape shape--circle', 
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    addComment(x, y) {
      const element = {
        is_locked: false,
        is_done: false,
        type: 'comment',
        resizable: false,
        draggable: true,
        commentable: true,
        shape: {
          id: this.getUuid(),
          width: 20, 
          height: 20, 
          x: x,
          y: y,
          className: 'shape shape--comment',
        }
      };
      this.isFresh = true;
      this.elements.push(element);
      this.create(element);
    },

    create(element) {

      const data = {
        uuid: this.currentImage.uuid,
        element: element,
      };
      this.axios.post(this.routes.create, data).then(response => {
        element.uuid = response.data.markup.uuid;
        element.is_owner = response.data.markup.is_owner;
        element.shape.relative_attributes = this.getRelativeAttributes(element);
        this.axios.put(`${this.routes.update}/${element.uuid}`, {element: element}).then(response => {
          this.selected = element.uuid;
          this.$store.commit('hasUnlockedMarkUps', true);
          if (element.commentable) {
            this.selected = null;
            this.isFresh = false;
            element.shape.className = this.getCommentClassNames(element);
          }
        })
      });
    },

    // CRUD
    fetch(imageUuid) {
      NProgress.start();
      this.elements = [];
      this.comments = [];
      this.currentImage = this.$props.images.find(image => image.uuid === imageUuid);
      this.axios.get(`${this.routes.get}/${imageUuid}`).then((response) => {
        const elements = response.data.markups;
        elements.forEach((element) => {
          if (element.type === 'comment') {
            if (element.comment) {
              this.comments.push({
                uuid: element.uuid,
                comment: element.comment,
                is_done: element.is_done,
                author: element.author,
                author_company: element.author_company,
                date: element.date,
                created_at: element.created_at,
              });
            }
          }
          this.elements.push(element);
        });
        this.$store.commit('hasUnlockedMarkUps', response.data.has_unlocked_markups);
        this.stageRectangle = this.$refs.stage.getBoundingClientRect();
        NProgress.done();
        this.setShape();
      })
    },

    update(element) {
      // Clean temporary CSS classes before saving
      const cleanElement = { ...element };
      cleanElement.shape = { ...element.shape };
      cleanElement.shape.className = element.shape.className.replace(' is-active', '');
      
      const data = {
        uuid: this.currentImage.uuid,
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
              is_done: response.data.markup.is_done,
              author: response.data.markup.author,
              author_company: response.data.markup.author_company,
              date: response.data.markup.date,
              created_at: response.data.markup.created_at,
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
        this.$store.commit('hasUnlockedMarkUps', response.data.has_unlocked_markups);
      });
    },

    save() {
      NProgress.start();
      this.axios.get(`${this.routes.save}/${this.$route.params.messageUuid}/1`).then(response => {
        NProgress.done();        
        this.$router.push({ name: 'messages', params: { slug: this.$props.project.slug, uuid: this.$props.project.uuid } });
        this.$notify({ type: "success", text: this.translate('Markierungen und Kommentare gespeichert') });
      });
    },

    saveWithMessage() {
      NProgress.start();
      this.axios.get(`${this.routes.save}/${this.$route.params.messageUuid}`).then(response => {
        NProgress.done();        
        // this.$store.commit('markupFiles', response.data.files);
        this.$store.commit('markupMessage', response.data.message_id);
        this.$router.push({ name: 'messages', params: { slug: this.$props.project.slug, uuid: this.$props.project.uuid } });
        this.$notify({ type: "success", text: this.translate('Markierungen und Kommentare gespeichert') });
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

    getCommentClassNames(element) {
      const stageRect = this.$refs.stage.getBoundingClientRect();
      if (element.shape.x > stageRect.width / 2) {
        return 'shape shape--comment shape--comment-rtl';
      }
      return 'shape shape--comment shape--comment-ltr';
    },

    getRelativeAttributes(element) {
      // get the x and y position and the width and height of the element relative to the stage in percent
      const stageRect = this.$refs.stage.getBoundingClientRect();
      const elementRect = document.querySelector(`[data-shape-id="${element.shape.id}"]`).getBoundingClientRect();
      const elementY = (elementRect.top - stageRect.top) / stageRect.height * 100;
      const elementX = (elementRect.left - stageRect.left) / stageRect.width * 100;
      const elementWidth = elementRect.width / stageRect.width * 100;
      const elementHeight = elementRect.height / stageRect.height * 100;

      // create an object 'relative'
      return {
        x: elementX,
        y: elementY,
        width: elementWidth,
        height: elementHeight,
      };
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

        // if the element is a comment and the element.shape.x is right of the center of the stage, move the comment to the left
        if (element.type == 'comment') {
          element.shape.className = this.getCommentClassNames(element);
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
        this.selected = id;
      }
    },

    removeHighlight(id) {
      this.elements.forEach(element => {
        element.shape.className = element.shape.className.replace(' is-active', '');
      });
      this.highlightedComment = null;
      this.selected = null;
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
      if (!element) {
        return;
      }

      if (element.is_owner && element.comment && element.comment !== this.tempComment) {
        this.update(element);
      }
      this.removeHighlight(this.selected);

      // wait 100ms and set 'canDelete' to false only if no other element is selected
      setTimeout(() => {
        if (!this.selected) {
          this.canDelete = false;
        }
      }, 100);
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

    getUuid() {
      let dt = new Date().getTime();
      const uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = (dt + Math.random()*16)%16 | 0;
        dt = Math.floor(dt/16);
        return (c=='x' ? r :(r&0x3|0x8)).toString(16);
      });
      return uuid;
    },

    getCommentNumber(uuid) {
      const sortedComments = this.sortedComments;
      const index = sortedComments.findIndex(comment => comment.uuid === uuid);
      return index !== -1 ? index + 1 : null;
    },
  },

  computed: {
    sortedComments() {
      return [...this.comments].sort((a, b) => {
        return new Date(a.created_at) - new Date(b.created_at);
      });
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
          this.$store.commit('hasUnlockedMarkUps', true);
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
