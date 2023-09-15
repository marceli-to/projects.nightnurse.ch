<template>
  <div class="flex flex-col items-end mb-4 lg:mb-8">
    <div 
      v-for="item in items" :key="item.id"
      class="sm:max-w-[70%] lg:max-w-[60%] w-full relative mb-3 lg:mb-6">
      <div class="p-3 lg:py-2 lg:pb-3 bg-gray-50 border-2 border-gray-100 text-sm sm:text-base text-dark relative rounded">
        <div class="mb-2 lg:mb-3 text-xs text-gray-400 inline-block w-auto font-mono relative">
          <span class="font-bold">{{ item.user }}</span>
          <span>{{ translate('am') }} {{ item.date }}</span>
        </div>
        <div class="text-base lg:text-sm break-words relative">
          <div class="flex">
            <star-icon 
              v-for="(n, index) in 5" :key="index"
              :class="[item.rating >= n ? 'text-yellow-400' : 'text-gray-200', 'block h-5 w-5 lg:h-6 lg:w-6']" aria-hidden="true" />
          </div>
          <div class="text-sm lg:text-md mt-1 lg:mt-2">
            {{ item.comment }}
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>

<script>
import { StarIcon } from "@vue-hero-icons/solid";
import i18n from "@/i18n";
import Separator from "@/components/ui/misc/Separator.vue";
export default {

  components: {
    Separator,
    StarIcon
  },

  mixins: [i18n],

  props: {
    items: {
      type: [Object, Array],
      required: true,
    },
  }

}

</script>