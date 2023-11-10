<template>
  <div :class="[message.internal ? 'bg-gray-100' : '', 'py-1 mb-2']">
    <router-link :to="{name: 'messages', params: { slug: project.slug }}" class="relative text-dark font-normal no-underline">
      <span class="text-gray-500 text-xs sm:text-sm pl-1">
        <span class="">{{ message.message_date_time }}</span><separator />
        <span v-if="message.sender">{{message.sender.full_name | truncate(15, '...')}}</span>
        <separator class="hidden sm:inline" />
        <br class="sm:hidden">
      </span>
      <span class="block mt-1 pl-1 sm:pl-0 sm:inline-block sm:mt-0">
        <span v-if="message.subject">{{message.subject}}</span>
        <span v-else-if="message.body_preview">{{ message.body_preview }}</span>
        <span v-else>–</span>
      </span>
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
  }
}

</script>