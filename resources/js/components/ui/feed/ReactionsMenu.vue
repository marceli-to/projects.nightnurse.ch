<template>
  <div 
    :class="[$props.message.internal ? 'right-0 sm:translate-x-[100%]' : 'left-0 sm:translate-x-[-100%]', 'absolute bottom-0 sm:bottom-auto sm:top-[50%] translate-y-[100%] sm:translate-y-[-50%] p-2 lg:p-3 group/reactions-menu']"
    @mouseleave="hide()"
    v-if="!$props.message.deleted_at">

    <a 
      href="" class="!text-gray-300 group-hover/reactions-menu:opacity-40 relative"
      @mouseover="show()">
      <smile-icon class="sm:h-5 sm:w-5" />
      <plus-sm-icon :class="[$props.message.internal ? 'right-[-5px]' : 'left-[-5px]', 'h-3 w-3 absolute top-[-5px]']" />
    </a>

    <div v-if="hasReactions" class="flex sm:block">
      <a href="" 
        class="!text-gray-300 hover:!text-highlight block mr-[4px] sm:mt-1 sm:mr-0"
        v-for="reactionType in reactionTypes" 
        :key="reactionType.uuid"
        @click.prevent="store(reactionType.uuid)">
        <smile-icon class="sm:h-5 sm:w-5" v-if="reactionType.name == 'smile'" />
        <frown-icon class="sm:h-5 sm:w-5" v-if="reactionType.name == 'frown'" />
        <thumbs-down-icon class="sm:h-5 sm:w-5" v-if="reactionType.name == 'thumbs-down'" />
        <thumbs-up-icon class="sm:h-5 sm:w-5" v-if="reactionType.name == 'thumbs-up'" />
      </a>
    </div>

  </div>
</template>
<script>
import { TrashIcon, PlusSmIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";
import ThumbsUpIcon from '@/components/ui/icons/ThumbsUp';
import ThumbsDownIcon from '@/components/ui/icons/ThumbsDown';
import SmileIcon from '@/components/ui/icons/Smile';
import FrownIcon from '@/components/ui/icons/Frown';
import NProgress from 'nprogress';

export default {

  components: {
    PlusSmIcon,
    ThumbsUpIcon,
    ThumbsDownIcon,
    SmileIcon,
    FrownIcon,
    NProgress
  },

  mixins: [i18n],

  data() {
    return {
      reactionTypes: null,
      hasReactions: false,

      // Routes
      routes: {
        post: '/api/reaction'
      },
    };
  },

  props: {

    message: {
      type: Object,
    },

    canDelete: {
      type: Boolean,
      default: false,
    }
  },

  mounted() {
    this.reactionTypes = this.$store.state.reactionTypes;
  },

  methods: {

    store(uuid) {
      const data = {
        'message_uuid': this.$props.message.uuid,
        'type_uuid': uuid
      };
      NProgress.start();
      this.axios.post(this.routes.post, data).then(response => {
        this.$emit('reactionStored')
        NProgress.done();
      });
    },

    show() {
      this.hasReactions = true;
    },

    hide() {
      this.hasReactions = false;
    }
  },

}
</script>