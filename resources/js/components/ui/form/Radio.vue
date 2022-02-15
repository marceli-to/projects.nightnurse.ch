<template>
<div>
  <label>{{label}}</label>
  <div class="flex mt-2">
    <input
      v-model="value"
      type="radio"
      :name="name + '_1'"
      :id="name + '_1'"
      value="1"
      class="sr-only"
      @change="change($event.target.value)"
    >
    <label 
      :for="name + '_1'" 
      class="bg-transparent flex items-center justify-center mb-0 mr-3 sm:mr-5 py-2 sm:py-3 w-12 sm:w-16 border-theme text-gray-200 text-center normal-case cursor-pointer font-mono select-none">
      {{labelTrue}}
    </label>
    <input
      v-model="value"
      type="radio"
      :name="name + '_0'"
      :id="name + '_0'"
      value="0"
      class="sr-only"
      @change="change($event.target.value)"
    >
    <label 
      :for="name + '_0'" 
      class="bg-transparent flex items-center justify-center mb-0 mr-0 sm:mr-0 py-2 sm:py-3 w-12 sm:w-16 border-theme text-gray-200 text-center normal-case cursor-pointer font-mono select-none">
      {{labelFalse}}
    </label>
  </div>
</div>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      default: ''
    },
    model: {
      type: Number,
      default: null,
    },
    name: {
      type: String,
      default: ''
    },
    labelTrue: {
      type: String,
      default: 'Ja',
    },
    labelFalse: {
      type: String,
      default: 'Nein',
    }
  },

  data() {
    return {
      value: null,
    }
  },

  mounted() {
    this.value = this.$props.model;
  },

  methods: {
    change(value) {
      this.$emit('update:' + this.$props.name, parseInt(value));
    }
  }
}
</script>
