<template>
  <div v-if="$props.companies.length > 0" class="mb-6 lg:mb-8">
    <div class="mb-1 w-full max-h-[480px] overflow-y-auto pr-4">
      <div v-for="(company, index) in filteredCompanies" 
        :key="company.id" 
        class="flex form-check w-full py-2 px-1 border-b">
        <input 
          :type="[$props.type == 'single' ? 'radio' : 'checkbox']" 
          class="checkbox" 
          name="company"
          :value="company.id" 
          :id="company.id"
          :checked="isChecked(company.id)" 
          :data-company-name="company.name"
          :data-company-city="company.firstname"
          @change="select($event, company)">
          <label class="inline-block text-gray-800" :for="company.id">
            {{ company.name }}<template v-if="company.city">, {{ company.city }}</template>
          </label>
      </div>
    </div>
  </div>
</template>

<script>
import { ChevronDownIcon, ChevronUpIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";

export default {

  components: {
    ChevronUpIcon,
    ChevronDownIcon
  },

  mixins: [i18n],

  props: {

    companies: {
      type: Array,
      default: () => [],
    },

    companyId: {
      type: Number,
      default: null
    },

    keyword: {
      type: String,
      default: ''
    },

    type: {
      type: String,
      default: 'single'
    }
  },

  methods: {

    select(event, company) {
      const state = event.target.checked ? true : false;

      if (this.$props.type == 'single') {
        this.$emit('selectMainCompany', company.id);
        return;
      }

      if (this.$props.type == 'multiple' && state) {
        this.$emit('selectCompany', company.id);
        return;
      }
      else {
        this.$emit('deselectCompany', company.id);
        return;
      }
    },

    isChecked(id) {
      return this.$props.companyId === id;
    }
  },

  computed: {

    filteredCompanies() {
      if (!this.$props.keyword) return this.$props.companies;
      return this.companies.filter(company => {
        return (company.name ? company.name.toLowerCase() : '').includes(this.$props.keyword.toLowerCase()) || (company.city ? company.city.toLowerCase() : '').includes(this.$props.keyword.toLowerCase())
      })
    }
  }
}
</script>