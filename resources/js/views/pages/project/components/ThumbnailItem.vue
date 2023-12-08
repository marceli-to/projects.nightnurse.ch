<template>
  <div class="mb-1 lg:mb-2 relative after:top-0 after:-right-3 after:absolute after:block after:w-[2px] after:h-full after:bg-gray-100 last-child:after:hidden">
    <div class="flex flex-wrap gap-1 lg:gap-3">
      <div v-for="(file, index) in message.files" :key="file.uuid" class="">
        <a :href="fileUri(file)" target="_blank" class="group">
          <img 
            :src="thumbnailImageUri(file)" 
            height="100" 
            width="100"
            loading="lazy"
            class="!mt-0 !mb-0 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm group-hover:opacity-75 group-hover:transition-opacity"
            v-if="file.preview" />
        </a>
      </div>
    </div>
    <router-link :to="{name: 'messages', params: { slug: project.slug, uuid: project.uuid }}" class="relative text-dark font-normal no-underline pr-2">
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