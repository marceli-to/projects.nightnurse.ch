<template>
  <div>
    <div class="upload-listing">
      <div>
        <figure
          :class="[image.publish == 0 ? 'is-disabled' : '', 'upload-item']"
          v-for="image in images"
          :key="image.id"
        >
          <a :href="`/storage/uploads/${image.name}`" target="_blank" class="upload__preview aspect-square" v-if="image.filetype == 'svg'">
            <img :src="`/storage/uploads/${image.name}`" height="300" width="300">
          </a>
          <a :href="getSource(image, 'cache')" target="_blank" class="upload__preview" v-else>
            <img :src="getSource(image, 'thumbnail')" height="300" width="300">
          </a>

          <div class="upload__actions">
            <image-actions 
              :image="image" 
              :hasPreview="false"
              :hasEdit="false"
              :hasCrop="image.filetype == 'svg' ? false : true"
              :publish="image.publish" 
              :imagePreviewRoute="'cache'">
            </image-actions>
          </div>
        </figure>
      </div>
    </div>

    <div :class="[hasOverlayCropper ? 'is-visible' : '', 'upload-overlay-cropper']">
      <div class="upload-overlay__loader" v-if="isLoading">Bild wird geladen...</div>
      <div class="upload-overlay__close" v-if="!isLoading">
        <a
          href="javascript:;"
          class="feather-icon icon-close-overlay"
          @click.prevent="hideCropper()"
        >
          <x-icon size="24"></x-icon>
        </a>
      </div>
      <div class="upload-overlay-cropper__wrapper" v-if="!isLoading">
        <div :class="'is-' + overlayItem.orientation">
          <div class="cropper-info">{{ cropW }} x {{ cropH }}px</div>
          <cropper
            :src="cropImage"
            :defaultPosition="defaultPosition"
            :defaultSize="defaultSize"
            :stencilProps="{
              aspectRatio: this.ratioW/this.ratioH,
              linesClassnames: {
                default: 'line',
              },
              handlersClassnames: {
                default: 'handler'
              }
            }"
            @change="change"
          ></cropper>
          <div class="form-buttons">
            <a
              href="javascript:;"
              class="btn-primary"
              @click.prevent="saveCoords(overlayItem)"
            >Speichern</a>
            <a href="javascript:;"
                class="btn-secondary"
                @click.prevent="hideCropper()">
              Abbrechen
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

// Radio Button
import RadioButton from "@/components/ui/RadioButton.vue";

// Image components
import ImageActions from "@/components/images/Actions.vue";

// Image mixins
import ImageEdit from "@/components/images/mixins/edit";
import ImageCrop from "@/components/images/mixins/crop";
import ImageUtils from "@/components/images/mixins/utils";

// Draggable
import draggable from 'vuedraggable';

// Cropper
import { Cropper } from "vue-advanced-cropper";

// Icons
import { XIcon } from 'vue-feather-icons';

export default {
  components: {
    ImageActions,
    RadioButton,
    XIcon,
    Cropper,
    draggable
  },

  props: {
    images: Array,

    updateOnChange: {
      type: Boolean,
      default: false
    },

    imagePreviewRoute: {
      type: String,
      default: "cache"
    },

    ratioW: {
      type: Number,
      default: 0
    },

    ratioH: {
      type: Number,
      default: 0
    }
  },


  data() {
    return {
      imageData: null,
      view: 'grid',
      isLoading: false,
      ratio: {
        w: null,
        h: null
      }
    };
  },

  mixins: [ImageUtils, ImageEdit, ImageCrop],

  mounted() {
    this.imageData = this.$props.images;
    this.ratio.w = this.$props.ratioW;
    this.ratio.h = this.$props.ratioH;
  },

  methods: {

  }

};
</script>