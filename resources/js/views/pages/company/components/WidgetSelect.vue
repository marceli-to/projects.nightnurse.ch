<template>
  <div>
    <h2 class="text-lg lg:text-lg font-bold !mt-0 mb-4 sm:mb-3">{{ translate('Hauptkunde auswählen') }}</h2>
    <div class="mt-3 mb-6 pr-5 relative">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center">
      <search-icon class="w-5 h-5 text-gray-400" />
    </div>  
    <input type="text" v-model="keyword" :placeholder="translate('Suche...')" class="!text-base placeholder:text-sm placeholder:text-gray-400 !pl-7 !border-b !border-t !border-gray-200 focus:!border-gray-200" />
    </div>
    <nav class="max-h-96 lg:max-h-[42rem] pr-4">
      <company-selector 
        :keyword="keyword"
        :companyId="companyId"
        :companies="companies"
        :type="type"
        @deselectCompany="deselectCompany"
        @selectCompany="selectCompany"
        @selectMainCompany="selectMainCompany">
      </company-selector>
    </nav>
  </div>
</template>

<script>
import { SearchIcon } from "@vue-hero-icons/outline";
import i18n from "@/i18n";
import ErrorHandling from "@/mixins/ErrorHandling";
import CompanySelector from "@/views/pages/company/components/CompanySelector.vue";

export default {
  components: {
    CompanySelector,
    SearchIcon
  },
  
  mixins: [ErrorHandling, i18n],

  data() {
    return {
      keyword: null,
    }
  },
  props: {
    data: {
      type: Object,
      default: () => {}
    },
    companyId: {
      type: Number,
      default: null
    },
    companies: {
      type: Array,
      default: () => {}
    },
    type: {
      type: String,
      default: 'single'
    }
  },
  methods: {
    selectMainCompany(companyId) {
      this.$emit('selectMainCompany', companyId);
    },

    selectCompany(companyId) {
      this.$emit('selectCompany', companyId);
    },

    deselectCompany(companyId) {
      this.$emit('deselectCompany', companyId);
    }
  }
}
</script>