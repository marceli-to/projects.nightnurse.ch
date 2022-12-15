<template>
  <div class="flex relative mt-4 w-full">
    <div v-for="reaction in $props.reactions" :key="reaction.type.uuid">
      <template v-if="reaction.type.name == 'thumbs-up'">
        <reaction-tool-tip :users="reaction.users">
          <thumbs-up-icon class="text-dark sm:h-5 sm:w-5 mr-1" />
        </reaction-tool-tip>
      </template>
      <template v-if="reaction.type.name == 'thumbs-down'">
        <reaction-tool-tip :users="reaction.users">
          <thumbs-down-icon class="text-dark sm:h-5 sm:w-5 mr-1" />
        </reaction-tool-tip>
      </template>
      <template v-if="reaction.type.name == 'smile'">
        <reaction-tool-tip :users="reaction.users">
          <smile-icon class="text-dark sm:h-5 sm:w-5 mr-1" />
        </reaction-tool-tip>
      </template>
      <template v-if="reaction.type.name == 'frown'">
        <reaction-tool-tip :users="reaction.users">
          <frown-icon class="text-dark sm:h-5 sm:w-5 mr-1" />
        </reaction-tool-tip>
      </template>
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
import ReactionToolTip from "@/components/ui/feed/ReactionToolTip.vue";

export default {

  components: {
    PlusSmIcon,
    ThumbsUpIcon,
    ThumbsDownIcon,
    SmileIcon,
    FrownIcon,
    ReactionToolTip
  },

  mixins: [i18n],

  props: {
    reactions: {
      type: [Array, Object],
    },
  },
}
</script>