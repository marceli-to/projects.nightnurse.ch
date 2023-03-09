<template>
  <div class="mt-4 sm:mt-6 lg:mt-8 pb-12">
    <div v-for="(messages, day) in $props.feedItems" :key="day" class="relative">
      <template v-if="hasItems(messages)">
        <div 
          :class="[ message.private ? 'is-private' : message.internal ? 'is-internal' : '', 'group flex justify-center mb-3']" 
          v-for="(message, idx) in filteredItems(messages)" :key="idx">
          <div class="max-w-[280px] sm:max-w-[480px] w-auto inline-block p-2 bg-white border-2 border-gray-100 text-xs sm:text-sm text-dark relative rounded translate-x-1/3 group-[.is-internal]:bg-gray-100 group-[.is-internal]:-translate-x-1/3 group-[.is-private]:-translate-x-1/3 group-[.is-private]:bg-alice-blue  group-[.is-private]:border-alice-blue group-hover:opacity-80">
            <a href="javascript:;" @click="$emit('scrollTo', message.uuid)" class="no-underline text-xs text-gray-400 inline-block w-auto font-mono">
              <span class="font-bold" v-if="message.sender">{{message.sender.full_name}}:</span>
              <template v-if="message.subject">
                <span class="text-dark">{{ message.subject }}</span>
              </template>
              <template v-else-if="message.body_preview">
                <span class="text-dark">{{ message.body_preview }}</span>
              </template>
            </a>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>
<script>
import i18n from "@/i18n";
import FeedItemTimestamp from "@/components/ui/feed/TimeStamp.vue";

export default {

  components: {
    FeedItemTimestamp
  },

  mixins: [i18n],

  data() {
    return {
    }
  },

  props: {

    feedItems: {
      type: [Array, Object],
    },

    feedType: {
      type: String
    }
  },

  methods: {
    hasItems(messages) {
      let data = messages.filter(x => x.deleted_at == null);
      return data.length > 0 ? true : false;
    },

    filteredItems(items) {
      if (this.$props.feedType == 'private') {
        return items.filter(item => item.private === 1);
      }
      if (this.$props.feedType == 'public') {
        return items.filter(item => item.private === 0);
      }
      return items;
    },
  },
 
}
</script>