<template>
  <div class="mr-2 sm:mr-5 relative after:top-0 after:-right-0.5 after:absolute after:block after:w-[2px] after:h-full after:bg-gray-100">
    <router-link :to="{name: 'messages', params: { uuid: project.uuid }}" class="relative text-dark font-normal no-underline">
      <div class="flex">
        <div v-for="file in message.files" :key="file.uuid">
          <img 
            :src="getThumbnailPath(file)" 
            height="100" 
            width="100"
            class="!mt-0 !mb-0 mr-1 lg:mr-2 block h-auto max-w-[50px] lg:max-w-[70px] bg-light rounded-sm"
            v-if="file.preview" />
        </div>
      </div>
      <div class="pr-2 mt-2">
        <span class="">{{ message.message_date_time }}</span><separator />{{ message.sender.acronym }}<separator /><span v-if="message.subject">{{message.subject}}</span><span v-else-if="message.body_preview">{{ message.body_preview }}</span><span v-else>–</span>
      </div>
      <!-- <span class="text-gray-500 text-xs sm:text-sm pl-1">
        <span class="">{{ message.message_date_time }}</span><separator />
        <span v-if="message.sender">{{message.sender.full_name | truncate(15, '...')}}</span>
        <separator class="hidden sm:inline" />
        <br class="sm:hidden">
      </span>
      <span class="block mt-1 pl-1 sm:pl-0 sm:inline-block sm:mt-0">
        <span v-if="message.subject">{{message.subject}}</span>
        <span v-else-if="message.body_preview">{{ message.body_preview }}</span>
        <span v-else>–</span>
      </span> -->
    </router-link>
  </div>
</template>
<script>

import Separator from "@/components/ui/misc/Separator.vue";

export default {

  components: {
    Separator
  },

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

  methods: {
      
    getThumbnailPath(file) {
      if (file.folder) {
        return `/img/thumbnail/${file.folder}/${file.name}`;
      }
      return `/img/thumbnail/${file.name}`;
    }
  }
}

</script>