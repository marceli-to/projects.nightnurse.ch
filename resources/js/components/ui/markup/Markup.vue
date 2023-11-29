<template>
<div>
  <div class="sm:grid sm:grid-cols-12 sm:gap-x-4 min-w-[980px] overflow-x-auto" ref="markupStage">
    <div class="sm:col-span-9">

      <markup-menu 
        @addRectangle="addRectangle"
        @addCircle="addCircle"
        @addComment="addComment"
        @deleteElement="destroy"
        :canDelete="canDelete">
      </markup-menu>

      <div 
        ref="image"
        class="w-full aspect-video bg-no-repeat bg-contain bg-center bg-light"
        :style="`background-image: url(${largeImageUri(image)});`">
        <v-stage 
          ref="stage"
          :config="configStage"
          @mousedown="handleStageMouseDown"
          @touchstart="handleStageMouseDown">
          <v-layer ref="layer">
            <div v-for="(element, index) in elements" :key="element.name">
              <template v-if="element.shape.type === 'rect'">
                <v-rect :config="element.shape" @transformend="handleTransformEnd" @dragend="handleShapeDragEnd" />
              </template>
              <template v-if="element.shape.type === 'circle'">
                <v-circle :config="element.shape" @transformend="handleTransformEnd" @dragend="handleShapeDragEnd" />
              </template>
              <template v-if="element.shape.type === 'comment'">
                <v-image :config="element.shape" @dragend="handleCommentDragEnd" class="cursor-pointer"/>
              </template>
            </div>
            <v-transformer ref="transformer" />
          </v-layer>
        </v-stage>
      </div>
    </div>
    <div class="sm:col-span-3">
      <template v-if="comments.length">
        <comment 
          v-for="(comment, index) in comments" 
          :key="comment.uuid" 
          :comment="comment"
          :highlighted="highlightedComment">
        </comment>
      </template>
      <template v-if="hasCommentForm">
        <div class="form-group">
          <textarea 
            v-model="comment" 
            rows="5" 
            class="border-2 focus:border-gray-200 rounded-sm py-2 px-2 text-xs lg:text-sm placeholder:text-xs lg:placeholder:text-sm placeholder:text-gray-300" 
            :placeholder="translate('Ihr Kommentar')">
          </textarea>
          <div class="flex justify-end">
            <button type="submit" class="font-mono font-semi !leading-none text-xs text-dark hover:text-highlight mt-2 lg:mt-3" @click="save()" v-if="hasSelectedShape">
              {{ translate('Kommentar speichern') }}
            </button>
          </div>
        </div>
      </template>
    </div>
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
import i18n from "@/i18n";
import NProgress from 'nprogress';
import Konva from 'konva';
import { Stage, Layer, Rect, Circle, Image, Transformer } from 'vue-konva';
import Helpers from "@/mixins/Helpers";
import MarkupMenu from '@/components/ui/markup/Menu.vue';
import Comment from '@/components/ui/markup/Comment.vue';
import { ArrowLeftIcon, SaveIcon } from "@vue-hero-icons/outline";
import ContentFooter from "@/components/ui/layout/Footer.vue";

export default {

  mixins: [i18n, Helpers],

  components: {
    MarkupMenu,
    Comment,
    ContentFooter,
    ArrowLeftIcon,
    SaveIcon,
    NProgress
  },

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
      configStage: {
        width: 200,
        height: 200
      },

      colors: {
        default: 'blue',
        highlight: '#ff008b',
      },

      elements: [],

      comments: [],
      highlightedComment: null,
      selectedShapeName: '',
      selectedShapeType: null,

      commmentIcon: null,
      comment: null,

      // States
      isFetched: false,
      hasSelectedShape: false,
      hasCommentForm: false,
      canDelete: false,
      hasUnlockedElements: false,

      routes: {
        get: '/api/markups',
        lock: '/api/markups/lock',
        create: '/api/markup',
        update: '/api/markup',
        delete: '/api/markup',
        comment: '/api/markup/comment',
      }
    }
  },

  // set width and height of canvas to match image
  mounted() {
    this.configStage.width = this.$refs.image.clientWidth;
    this.configStage.height = this.$refs.image.clientHeight;

    this.preloadIcons();
    this.fetch();

    // add event handler for delete key
    window.addEventListener('keydown', (e) => {
      if (!this.hasSelectedShape || !this.canDelete) {
        return;
      }
      if (e.keyCode === 46) {
        this.destroy();
      }
    });

    // add event handler for outside click (markupState)
    document.addEventListener('click', (e) => {
      if (this.$refs.markupStage && !this.$refs.markupStage.contains(e.target)) {
        this.reset();
      }
    });
  },

  methods: {

    handleTransformEnd(e) {
      // shape is transformed, let us save new attrs back to the node
      // find element in our state
      const element = this.elements.find(
        (r) => r.shape.name === this.selectedShapeName
      );
      // update the state
      element.shape.x = e.target.x();
      element.shape.y = e.target.y();
      element.shape.rotation = e.target.rotation();
      element.shape.scaleX = e.target.scaleX();
      element.shape.scaleY = e.target.scaleY();
      const desiredStrokeWidth = 3; // The desired stroke width in pixels
      if (e.target.scaleX() > e.target.scaleY()) {
        element.shape.strokeWidth = desiredStrokeWidth / e.target.scaleX();
      } else {
        element.shape.strokeWidth = desiredStrokeWidth / e.target.scaleY();
      }
      this.update(element);
    },

    handleShapeDragEnd(e) {
      // shape is dragged, let us save new attrs back to the node
      // find element in our state
      const element = this.elements.find(
        (r) => r.shape.name === this.selectedShapeName
      );
      // update the state
      element.shape.x = e.target.x();
      element.shape.y = e.target.y();
      element.shape.stroke = this.colors.default;
      this.update(element);
    },

    handleCommentDragEnd(e) {
      const element = this.elements.find(
        (r) => r.shape.name === this.selectedShapeName
      );
      // update the state
      element.shape.x = e.target.x();
      element.shape.y = e.target.y();
      this.update(element);
    },

    handleCommentClick(e) {

      console.log(e.target.attrs);

      if (e.target.attrs.editable) {
        this.hasSelectedShape = true;
        this.canDelete = true;

        if (e.target.attrs.commentable) {
          this.hasCommentForm = true;
        }
      }
      this.selectedShapeName = e.target.name();
      this.resetComments(this.selectedShapeName);
      this.highlightedComment = this.selectedShapeName;
      e.target.image(this.commmentIconActive);

    },

    handleStageMouseDown(e) {
     
      // clicked on stage - clear selection
      if (e.target === e.target.getStage()) {
        this.reset();
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
        this.handleCommentClick(e);
        return;
      }

      // find clicked elements by its name
      const name = e.target.name();
      const element = this.elements.find((r) => r.shape.name === name);

      if (element && element.shape.owner !== this.$store.state.user.uuid || element.shape.editable === false) {
        this.$refs.transformer.getNode().nodes([]);
        return;
      }

      if (element) {
        element.shape.stroke = this.colors.highlight;
        this.selectedShapeName = name;
        this.hasSelectedShape = true;
        if (element.shape.editable) {
          this.canDelete = true;
        }

      } 
      else {
        element.shape.stroke = this.colors.default;
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

    resetComments(exceptName = null) {

      // remove highlight from all comments
      this.highlightedComment = null;

      // get all comments from this.elements
      const comments = this.elements.filter(
        (r) => r.shape.type === 'comment'
      );

      // remove exceptName from comments
      if (exceptName) {
        comments.forEach((element, index) => {
          if (element.shape.name === exceptName) {
            comments.splice(index, 1);
          }
        });
      }

      comments.forEach((element) => {
        if (element.shape.type === 'comment') {
          const commmentIcon = new window.Image();
          commmentIcon.src = "/assets/img/annotation.svg";
          commmentIcon.onload = () => {
            element.shape.image = commmentIcon;
          };
        }
      });
    },

    resetShapesColors() {
      this.elements.forEach((element) => {
        if (element.shape.type === 'comment') {
          return;
        }
        element.shape.stroke = this.colors.default;
      });
    },

    reset() {
      this.selectedShapeName = 'null';
      this.hasSelectedShape = false;
      this.hasCommentForm = false;
      this.canDelete = false;
      this.resetComments();
      this.resetShapesColors();
      this.updateTransformer();
    },

    // CRUD
    fetch() {
      NProgress.start();
      this.elements = [];
      this.comments = [];
      this.axios.get(`${this.routes.get}/${this.$props.image.uuid}`).then((response) => {
        const data = response.data.data;
        data.forEach((element) => {
          if (element.shape.type === 'comment') {
            element.shape.image = this.commmentIcon;
            if (element.comment) {
              this.comments.push({
                uuid: element.uuid,
                shape_uuid: element.shape_uuid,
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
      })
    },

    create(element) {
      const data = {
        uuid: this.$props.image.uuid,
        element: element,
      };
      this.axios.post(this.routes.create, data).then(response => {
      });
    },

    update(element) {
      const data = {
        uuid: this.$props.image.uuid,
        element: element,
      };
      this.axios.put(`${this.routes.update}/${element.shape.name}`, data).then(response => { });
    },

    save() {
      const data = {
        shape_uuid: this.selectedShapeName,
        comment: this.comment,
      };
      NProgress.start();
      this.axios.post(`${this.routes.comment}`, data).then(response => {
        this.comments.push(response.data.comment);
        const element = this.elements.find(
          (r) => r.shape.name === this.selectedShapeName
        );
        element.shape.commentable = false;
        this.comment = null;
        this.hasCommentForm = false;
        this.selectedShapeName = '';
        this.resetComments();
        NProgress.done();
      });
    },

    lock() {
      NProgress.start();
      this.axios.get(`${this.routes.lock}/${this.$props.image.uuid}`).then(response => {
        NProgress.done();
        this.$notify({ type: "success", text: this.translate('Markierungen und Kommentare gespeichert') });
        this.fetch();
      });
    },

    destroy() {
      if (!this.selectedShapeName) {
        return;
      }
      this.axios.delete(`${this.routes.delete}/${this.selectedShapeName}`).then(response => {
        this.removeElement();
      });
    },

    addRectangle() {
      const element = {
        is_locked: 0,
        shape: {
          type: 'rect',
          x: 50,
          y: 50,
          width: 50,
          height: 50,
          scaleX: 1,
          scaleY: 1,
          rotation: 0,
          name: this.getUuid(),
          draggable: true,
          stroke: 'blue',
          strokeWidth: 3,
          editable: true,
          owner: this.$store.state.user.uuid
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    addCircle() {
      const element = {
        is_locked: 0,
        shape: {
          type: 'circle',
          x: 50,
          y: 50,
          width: 100,
          height: 100,
          scaleX: 1,
          scaleY: 1,
          rotation: 0,
          name: this.getUuid(),
          draggable: true,
          stroke: 'blue',
          strokeWidth: 3,
          editable: true,
          owner: this.$store.state.user.uuid
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    addComment() {
      const element = {
        is_locked: 0,
        shape: {
          type: 'comment',
          x: 50,
          y: 50,
          name: this.getUuid(),
          draggable: true,
          commentable: true,
          editable: true,
          image: this.commmentIcon,
          owner: this.$store.state.user.uuid
        }
      };
      this.elements.push(element);
      this.create(element);
    },

    highlightComment(name) {
      // remove attribute is_highlighted from all other comments
      this.comments.forEach((element) => {
        if (element.shape_uuid == name) {
          element.is_highlighted = true;
        }
        else {
          element.is_highlighted = false;
        }
      });
    },

    removeElement() {
      const elements = this.elements.filter(
        (r) => r.shape.name !== this.selectedShapeName
      );
      
      // find comment in this.comments
      const comment = this.comments.find(
        (r) => r.shape_uuid === this.selectedShapeName
      );

      // remove comment from this.comments
      if (comment) {
        const index = this.comments.indexOf(comment);
        this.comments.splice(index, 1);
      }

      this.elements = elements;
      this.selectedShapeName = '';
      this.hasSelectedShape = false;
      this.hasCommentForm = false;
      this.canDelete = false;
      this.updateTransformer();
    },

    preloadIcons() {
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
      // https://stackoverflow.com/questions/105034/create-guid-uuid-in-javascript
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
        // if any element has 'is_locked' = 0, alert('has unlocked elements')
        const unlockedElements = this.elements.filter(
          (r) => r.is_locked == 0
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

};
</script>