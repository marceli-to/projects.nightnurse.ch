<template>
  <div class="absolute -top-8 font-mono text-gray-300 text-tiny w-full text-center" v-if="show()">
    {{$props.current.message_date_string}}
  </div>
  <div v-else></div>
</template>
<script>
export default {
  props: {
    data: {
      type: Array,
      default: [],
    },
    current: {
      type: Object,
      default: [],
    },
    index: {
      type: Number,
      default: 0
    }
  },

  methods: {
    show() {
      if (this.$props.index < 1) {
        return true;
      }
      else {
        if (this.$props.data[this.$props.index-1].message_date == this.$props.current.message_date) {
          return false;
        }
      }
      return true;
    },
  }

}
</script>