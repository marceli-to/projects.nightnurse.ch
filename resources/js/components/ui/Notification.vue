<template>
  <div class="notification is-toast" v-if="isLoaded && !isHidden">
    <div>
      <a href="javascript:;" @click.prevent="hide()">
        <x-icon aria-hidden="true" />
      </a>
      <h1 v-if="notification.title[_locale()]">
        {{  notification.title[_locale()] }}
      </h1>
      <div v-if="notification.text[_locale()]">
        {{  notification.text[_locale()] }}
      </div>
    </div>
  </div>
</template>
<script>
import { XIcon } from 'vue-feather-icons';
import i18n from "@/i18n";

export default {

  components: {
    XIcon,
  },

  mixins: [i18n],

  data() {
    return {
      notification: {
        title: null,
        text: null,
        publish: false
      },

      isLoaded: false,
      isHidden: false,

      routes: {
        find: '/api/notification/latest'
      },
    }
  },

  mounted() {
    if (sessionStorage.getItem('notification') == 'hidden') {
      this.isHidden = true;
    }
    else {
      this.fetch();
    }
  },
  
  methods: {
    fetch() {
      this.axios.get(`${this.routes.find}`).then(response => {
        this.notification = response.data;
        this.isLoaded = this.notification.publish;
      });
    },

    hide() {
      this.isHidden = true;
      sessionStorage.setItem('notification', 'hidden');
    }
  }
}
</script>