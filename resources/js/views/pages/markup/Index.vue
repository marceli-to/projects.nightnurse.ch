<template>
<div v-if="isFetched">
  <!-- 
  <header class="mb-4 md:mb-6 pt-2 sm:pt-3 pb-2 md:pb-4 sticky top-0 bg-white z-40 border-bottom -ml-[1px] pl-[1px]">
    <div class="relative">
      <div class="text-xl lg:text-2xl font-bold mb-2 sm:mb-3 flex items-end sm:max-w-2xl leading-snug sm:leading-normal">
        <div class="text-dark" v-if="project.company">
          <div class="font-normal text-sm">
            {{project.company.name}}
          </div>
          <span :style="`color: ${project.color}`">{{project.number}} – {{project.name}}</span>
        </div>
        <div class="text-dark" v-else>
          {{project.number}} {{project.name}}
        </div>
      </div>
      <div class="max-w-sm sm:max-w-xl">
        <div class="grid grid-cols-12 sm:gap-2 md:gap-4">
          <div class="col-span-4 text-gray-400">
            <div class="text-xs font-mono pb-0.5">{{ translate('Projektleiter') }}</div>
            <div class="text-sm text-dark">
              {{project.manager.full_name}}
              <a :href="`tel:${project.manager.phone}`" class="flex items-center text-dark hover:text-highlight no-underline" v-if="project.manager.phone">
                {{project.manager.phone}}
              </a>
            </div>
          </div>
          <div class="col-span-4 text-gray-400">
            <div class="text-xs font-mono pb-0.5">{{ translate('Projektstart') }}</div>
            <div class="text-sm text-dark">{{project.date_start ? project.date_start : '-'}}</div>
          </div>
          <div class="col-span-4 text-gray-400">
            <div class="text-xs font-mono pb-0.5">{{ translate('Abgabetermin') }}</div>
            <div class="text-sm text-dark">{{project.date_end ? project.date_end : '-'}}</div>
          </div>
        </div>
      </div>
    </div>
  </header>
  -->
  <markup :image="file" :project="project"></markup>
</div>
</template>
<script>
import i18n from "@/i18n";
import NProgress from 'nprogress';
import Helpers from "@/mixins/Helpers";
import PageTitle from "@/mixins/PageTitle";
import ErrorHandling from "@/mixins/ErrorHandling";
import Markup from "@/components/ui/markup/Markup.vue";

export default {

  components: {
    NProgress,
    Markup,
  },

  mixins: [ErrorHandling, Helpers, PageTitle, i18n],

  data() {
    return {

      // Project data
      project: [],

      // File data
      file: {},

      // Routes
      routes: {
        project: {
          get: '/api/project',
        },
        file: {
          get: '/api/message/file',
        },
      },

      // States
      isLoading: false,
      isFetched: false,
    };
  },

  mounted() {
    this.fetch();
  },

  methods: {

    fetch() {
      NProgress.start();
      this.isFetched = false;
      this.axios.all([
        this.axios.get(`${this.routes.project.get}/${this.$route.params.uuid}`),
        this.axios.get(`${this.routes.file.get}/${this.$route.params.fileUuid}`),
      ]).then(axios.spread((...responses) => {
        this.project = responses[0].data;
        this.file = responses[1].data;
        this.setPageTitle(this.project.title);
        this.isFetched = true;
        NProgress.done();
      }));
    },

  },

  watch: {
  },

}
</script>