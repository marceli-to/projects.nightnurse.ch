<template>
  <div class="mr-2 sm:mr-5 relative after:top-0 after:-right-0.5 after:absolute after:block after:w-[2px] after:h-full after:bg-gray-100">
    <div class="flex">
      <div v-for="file in message.files" :key="file.uuid">
        <a :href="fileUri(file)" target="_blank" class="group">
          <img 
            :src="thumbnailImageUri(file)" 
            height="100" 
            width="100"
            class="!mt-0 !mb-0 mr-1 lg:mr-2 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm group-hover:opacity-75 group-hover:transition-opacity"
            v-if="file.preview" />
        </a>
      </div>
    </div>
    <router-link :to="{name: 'messages', params: { uuid: project.uuid }}" class="relative text-dark font-normal no-underline pr-2">
      <span class="mt-2 inline-flex">{{ message.message_date_time }}</span><separator />{{ message.sender.acronym }}
    </router-link>
  </div>
</template>
<script>

import Separator from "@/components/ui/misc/Separator.vue";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    Separator
  },

  mixins: [
    Helpers,
  ],

  props: {
    message: {
      type: Object,
      required: true
    },

    project: {
      type: Object,
      required: true
    },
  },
}

</script>