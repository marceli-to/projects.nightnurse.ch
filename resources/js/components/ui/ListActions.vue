<template>
  <div class="listing__item-action">

    <div v-if="hasClearQueue">
      <a
        href="javascript:;"
        @click.prevent="clearQueue(id,$event)"
        title="Queue leeren"
      >
        <x-circle-icon size="18" class="feather-icon"></x-circle-icon>
      </a>
    </div>

    <div v-if="hasMail">
      <a
        href="javascript:;"
        @click.prevent="mail(id,$event)"
      >
        <mail-icon size="18" class="feather-icon"></mail-icon>
      </a>
    </div>

    <div v-if="hasBilling">
      <a
        href="javascript:;"
        @click.prevent="updateBilling(id,$event)"
      >
        <span v-if="record.is_paid" class="feather-icon is-ok">
          <dollar-sign-icon size="18"></dollar-sign-icon>
        </span>
        <span v-else>
          <dollar-sign-icon size="18" class="feather-icon"></dollar-sign-icon>
        </span>
      </a>
    </div>

    <div v-if="hasParticipants">
      <router-link
        :to="{name: routes.participants, params: { id: id }}"
        class="feather-icon"
      >
        <users-icon size="18"></users-icon>
      </router-link>
    </div>

    <div v-if="hasBookings">
      <router-link
        :to="{name: routes.bookings, params: { id: id }}"
        class="feather-icon"
      >
        <clipboard-icon size="18"></clipboard-icon>
      </router-link>
    </div>

    <div v-if="hasDownload">
      <a
        :href="'/storage/uploads/' + record.file"
        target="_blank"
        class="feather-icon"
      >
        <download-cloud-icon size="18"></download-cloud-icon>
      </a>
    </div>

    <div v-if="hasEdit">
      <router-link
        :to="{name: routes.edit, params: { id: id }}"
        class="feather-icon"
      >
        <edit-icon size="18"></edit-icon>
      </router-link>
    </div>

    <div v-if="hasToggle">
      <a
        href="javascript:;"
        @click.prevent="toggle(id,$event)"
      >
        <span v-if="record.publish" class="feather-icon">
          <eye-icon size="18"></eye-icon>
        </span>
        <span v-else>
          <eye-off-icon size="18" class="feather-icon"></eye-off-icon>
        </span>
      </a>
    </div>

    <div v-if="hasCopy">
      <a
        href="javascript:;"
        class="feather-icon"
        @click.prevent="copy(id,$event)"
      >
        <copy-icon size="18"></copy-icon>
      </a>
    </div>

    <div v-if="hasDestroy">
      <a
        href="javascript:;"
        class="feather-icon"
        @click.prevent="destroy(id,$event)"
      >
        <trash2-icon size="18"></trash2-icon>
      </a>
    </div>

  </div>
</template>
<script>

// Icons
import { 
  EyeIcon,
  EyeOffIcon,
  EditIcon,
  Trash2Icon,
  CopyIcon,
  DownloadCloudIcon,
  SettingsIcon,
  LayersIcon,
  UserIcon,
  UsersIcon,
  DollarSignIcon,
  ClipboardIcon,
  MailIcon,
  XCircleIcon
}
from 'vue-feather-icons';

export default {

  components: {
    EyeIcon,
    EyeOffIcon,
    EditIcon,
    Trash2Icon,
    CopyIcon,
    DownloadCloudIcon,
    SettingsIcon,
    LayersIcon,
    UserIcon,
    UsersIcon,
    DollarSignIcon,
    ClipboardIcon,
    MailIcon,
    XCircleIcon
  },

  props: {

    id: {
      type: Number,
      default: null
    },

    hasEdit: {
      type: Boolean,
      default: true
    },
    
    hasDestroy: {
      type: Boolean,
      default: true
    },
    
    hasCatalog: {
      type: Boolean,
      default: false
    },

    hasCatalogItems: {
      type: Boolean,
      default: false
    },

    hasSettings: {
      type: Boolean,
      default: false
    },

    hasToggle: {
      type: Boolean,
      default: true
    },

    hasCopy: {
      type: Boolean,
      default: false
    },

    hasDownload: {
      type: Boolean,
      default: false,
    },

    hasDraggable: {
      type: Boolean,
      default: false,
    },

    hasParticipants: {
      type: Boolean,
      default: false,
    },

    hasMail: {
      type: Boolean,
      default: false,
    },

    hasBookings: {
      type: Boolean,
      default: false,
    },

    hasBilling: {
      type: Boolean,
      default: false,
    },

    hasClearQueue: {
      type: Boolean,
      default: false,
    },

    isCollapsible: {
      type: Boolean,
      default: false,
    },

    model: {
      type: String,
      default: null,
    },
    routes: Object,
    record: Object,
  },

  methods: {

    toggle(id,$event) {
      if (this.hasDraggable || this.isCollapsible) {
        this.$parent.$parent.toggle(id,$event,this.model);
      }
      else {
        this.$parent.toggle(id,$event,this.model);
      }
    },

    destroy(id,$event) {
      if (this.hasDraggable || this.isCollapsible) {
        this.$parent.$parent.destroy(id,$event,this.model);
      }
      else {
        this.$parent.destroy(id,$event,this.model);
      }
    },

    copy(id,$event) {
      this.$parent.copy(id,$event,this.model);
    },

    updateBilling(id) {
      this.$parent.updateBilling(id);
    },

    mail(id) {
      this.$parent.maildialog(id);
    },

    clearQueue(id) {
      this.$parent.clearQueue(id)
    }
  },
}
</script>

