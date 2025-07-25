<template>
<div v-if="isFetched">
  <markup :image="file" :images="files" :project="project"></markup>
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
        files: {
          get: '/api/message/files',
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
        this.axios.get(`${this.routes.file.get}/${this.$route.params.imageUuid}`),
        this.axios.get(`${this.routes.files.get}/${this.$route.params.messageUuid}`),
      ]).then(axios.spread((...responses) => {
        this.project = responses[0].data;
        this.file = responses[1].data.data;
        this.files = responses[2].data.data;
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