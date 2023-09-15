<template>
  <div class="flex justify-end mb-4 lg:mb-8">
    <form @submit.prevent="store" class="sm:max-w-[70%] lg:max-w-[60%] w-full relative">
      <div class="mb-2 lg:mb-4 relative flex">
        <a href="" 
          class="block"
          @click.prevent="setRating(index + 1)"
          v-for="(n, index) in 5" :key="index">
          <star-icon :class="[rating >= n ? 'text-yellow-400' : 'text-gray-200', 'block h-8 w-8']" aria-hidden="true" />
        </a>
      </div>
      <div class="form-group">
        <textarea 
          v-model="comment" 
          rows="5" 
          class="border-2 rounded p-1 lg:p-2 placeholder:text-sm lg:placeholder:text-base" 
          :placeholder="translate('Ihr Feedback')">
        </textarea>
      </div>
      <div class="form-group flex justify-end">
        <button type="submit" :class="[!allowSubmit ? 'pointer-events-none opacity-40' : '', 'btn-send']">
          <span class="block">{{ translate('Feedback senden') }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { StarIcon } from "@vue-hero-icons/solid";
import Helpers from "@/mixins/Helpers";
import i18n from "@/i18n";
import Separator from "@/components/ui/misc/Separator.vue";

export default {

  components: {
    Separator,
    StarIcon
  },

  mixins: [Helpers, i18n],


  data() {
    return {

      rating: 0,
      comment: null,
      project_uuid: this.uuid,

      routes: {
        get: '/api/feedbacks',
        store: '/api/feedback',
      },
    }
  },

  props: {
    uuid: {
      type: String,
      required: true
    }
  },

  methods: {

    store() {
      const data = {
        rating: this.rating,
        comment: this.comment,
        project_uuid: this.project_uuid
      };
      this.axios.post(this.routes.store, data).then(response => {
        this.$notify({ type: "success", text: this.translate('Feedback gespeichert') });
        this.$emit('stored', response.data);
        this.comment = null;
        this.rating = 0;
      });
    },

    setRating(rating) {
      this.rating = rating > this.rating ? rating : rating - 1;
    },
  },

  computed: {

    allowSubmit() {
      if (this.comment && this.comment.length > 2) {
        return true;
      }
      return false;
    },
  }
}

</script>