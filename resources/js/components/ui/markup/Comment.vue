<template>
  <a href="javascript:;" 
    @mouseover="$emit('mouseover', comment.uuid)" 
    @mouseout="$emit('mouseout')"
    :class="[highlighted == comment.uuid ? 'bg-gray-100' : '', 'no-underline text-gray-400 flex mb-3 lg:mb-3 p-1 lg:p-2 rounded-md w-full']">
    <div class="shrink-0 relative -top-2">
      <span class="font-bold rounded-full w-[20px] h-[20px] inline-flex items-center justify-center leading-none text-xs bg-gray-300 text-white">
        {{ commentNumber }}
      </span>
    </div>
    <div class="ml-2 flex-1">
      <header class="text-xs text-gray-400 font-mono">
        <span>{{ comment.date }} – {{ comment.author }}</span>
      </header>
      <div class="text-dark text-sm leading-[1.5] mt-1">
        <span :class="[comment.is_done ? 'line-through opacity-60' : '']">{{ comment.comment }}</span>
        <template v-if="$store.state.user.admin && !comment.is_done">
          <button 
            @click.stop="markAsDone"
            class="text-xs block text-highlight hover:text-dark transition-colors duration-200 mt-1">
            {{ translate('Erledigt') }}
          </button>
        </template>
      </div>
    </div>
  </a>
</template>
<script>
import i18n from "@/i18n";
import Helpers from "@/mixins/Helpers";
import { AnnotationIcon } from "@vue-hero-icons/outline";

export default {

  components: {
    AnnotationIcon,
  },

  props: {
    comment: {
      type: Object,
      required: true,
    },
    highlighted: {
      type: String,
      default: null,
    },
    commentNumber: {
      type: Number,
      required: true,
    },
  },

  mixins: [i18n, Helpers],

  methods: {
    markAsDone() {
      this.axios.put(`/api/markup/${this.comment.uuid}/done`)
        .then(response => {
          this.$notify({ 
            type: "success", 
            text: this.translate('Kommentar als erledigt markiert'),
            duration: 0
          });
          this.comment.is_done = true;
        })
        .catch(error => {
          this.$notify({ 
            type: "danger", 
            text: this.translate('Fehler beim Markieren des Kommentars'),
            duration: 0
          });
        });
    }
  }

}
</script>