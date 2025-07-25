import NProgress from 'nprogress';
import i18n from "@/i18n";

export default {

  mixins: [i18n],

  data() {
    return { 
      errors: null,
    }
  },
  
  mounted() {

    window.intercepted.$on('response:401', data => {
      this.unauthorized(data);
    });

    window.intercepted.$on('response:403', data => {
      this.forbiddenError(data);
    });

    window.intercepted.$on('response:404', data => {
      this.notFoundError(data);
    });

    window.intercepted.$on('response:405', data => {
      this.notAllowed(data);
    });

    window.intercepted.$on('response:422', data => {
      this.validationError(data);
    });

    window.intercepted.$on('response:500', data => {
      this.serverError(data);
    });

  },

  beforeDestroy(){
    window.intercepted.$off('response:401', this.listener);
    window.intercepted.$off('response:403', this.listener);
    window.intercepted.$off('response:404', this.listener);
    window.intercepted.$off('response:405', this.listener);
    window.intercepted.$off('response:422', this.listener);
    window.intercepted.$off('response:500', this.listener);
  },

  methods: {

    validationError(data) {
      let errors = {};
      for (var key in data.body) {
        if (data.body.hasOwnProperty(key)) {
          errors[key.field] = true;
        }
      }
      this.errors = data.body;
      NProgress.done();
      this.$notify({ type: "danger", text: this.translate(`Bitte alle mit * markierten Felder prüfen!`), duration: -1});
    },

    serverError(data) {
      NProgress.done();
      this.$notify({ type: "danger", text: `${data.status} ${data.code}<br>${data.body.message}`, duration: -1});
    },

    notFoundError(data) {
      this.$notify({ type: "danger", text: `${data.status} ${data.code}`, duration: -1});
      this.$router.push({ name: 'not-found' });
    },

    notAllowed(data) {
      NProgress.done();
      this.$notify({ type: "danger", text: `${data.status} ${data.code}`, duration: -1});
    },

    forbiddenError(data) {
      NProgress.done();
      this.$notify({ type: "danger", text: `${data.status} - Zugriff verweigert!`, duration: -1});
      this.$router.push({ name: 'forbidden' });
    },

    unauthorized(data) {
      document.location.href = '/login';
    }
  },

};
