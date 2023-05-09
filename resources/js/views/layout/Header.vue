<template>
<header class="bg-light">
  <div class="bg-white w-full max-w-5xl px-4 lg:px-6 pt-2 sm:pt-4 pb-1 relative mx-auto flex justify-between items-center">
    <!--
    <router-link :to="{ name: 'projects' }" :force="true" class="btn-icon">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
    </router-link>
    -->
    <a href="/projects" class="btn-icon">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
    </a>
    <a href="javascript:;" @click="toggleMenu()" class="btn-icon sticky top-0 !mr-0">
      <menu-alt-3-icon aria-hidden="true" />
    </a>
  </div>
  <div class="fixed z-40 w-screen h-screen inset-0 bg-black bg-opacity-60 z-1001" v-if="hasMenu">
    <nav class="fixed z-50 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[300px] sm:max-w-lg bg-white rounded-md p-4 space-y-5">
      <div class="mb-3 sm:mb-4 relative">
        <a href="javascript:;" @click="toggleMenu()" class="text-gray-400 hover:text-highlight block absolute top-0 right-0">
          <x-icon class="h-6 w-6" aria-hidden="true" />
        </a>
      </div>
      <ul class="list-none !p-0 m-0 !mt-4">
        <li class="block !p-0 !m-0" v-if="$store.state.user.admin">
          <menu-item :route="'projects'">
            <folder-icon class="h-4 w-4 sm:h-6 sm:w-6 mr-2 sm:mr-4" aria-hidden="true" />
            {{ translate('Projekte') }}
          </menu-item>
        </li>
        <li class="block !p-0 !m-0" v-if="$store.state.user.admin">
          <menu-item :route="'companies'">
            <office-building-icon class="h-4 w-4 sm:h-6 sm:w-6 mr-2 sm:mr-4" aria-hidden="true" />
            {{ translate('Kunden') }}
          </menu-item>
        </li>
        <li class="block !p-0 !m-0" v-if="$store.state.user.admin">
          <menu-item :route="'notifications'">
            <speakerphone-icon class="h-4 w-4 sm:h-6 sm:w-6 mr-2 sm:mr-4" aria-hidden="true" />
            {{ translate('Notifications') }}
          </menu-item>
        </li>
        <li class="block !p-0 !m-0">
          <menu-item :route="'profile-edit'">
            <user-circle-icon class="h-4 w-4 sm:h-6 sm:w-6 mr-2 sm:mr-4" aria-hidden="true" />
            {{ translate('Profil') }}
          </menu-item>
        </li>
        <li class="block !p-0 m-0 !mt-4">
          <div class="flex items-center justify-between pt-3 py-2 sm:py-3 sm:pb-0 pr-0">
            <a href="/logout" class="text-gray-400 hover:text-highlight text-xs font-normal no-underline flex items-center p-1 ml-1">
              <logout-icon class="h-4 w-4 mr-2" aria-hidden="true" />
              {{ translate('Logout') }}
            </a>
            <a href="javascript:;" @click="switchLanguage()" class="text-gray-400 hover:text-highlight text-xs font-normal no-underline flex items-center p-1 mr-1">
              {{ translate('Switch language to') }}
              <template v-if="$store.state.user.language == 'en'">
                <flag-de-icon class="ml-2" />
              </template>
              <template v-if="$store.state.user.language == 'de'">
                <flag-en-icon class="ml-2" />
              </template>
            </a>
          </div>
        </li>
      </ul>
    </nav>
  </div>
  <notification />
</header>
</template>
<script>
import { XIcon, UsersIcon, FolderIcon, OfficeBuildingIcon, UserCircleIcon, MenuAlt3Icon, SpeakerphoneIcon } from "@vue-hero-icons/outline";
import { LogoutIcon } from "@vue-hero-icons/solid"
import MenuItem from '@/components/ui/menu/Item';
import MenuItemLabel from '@/components/ui/menu/Label';
import Notification from '@/components/ui/Notification';
import FlagDeIcon from '@/components/ui/icons/flag-de';
import FlagEnIcon from '@/components/ui/icons/flag-en';
import i18n from "@/i18n";

export default {

  components: {
    XIcon,
    FolderIcon,
    UsersIcon,
    OfficeBuildingIcon,
    MenuAlt3Icon,
    LogoutIcon,
    UserCircleIcon,
    SpeakerphoneIcon,
    MenuItem,
    MenuItemLabel,
    FlagDeIcon,
    FlagEnIcon,
    Notification
  },

  mixins: [i18n],

  data() {
    return {

      hasMenu: false,

      notification: null,

      routes: {
        post: '/api/profile/switch-language',
        notifications: '/api/notification/latest'
      },
    }
  },

  mounted() {
    this.fetchUser();
    window.addEventListener("keyup", event => {
      if (event.which === 27) {
        this.hideMenu();
      }
    });
  },
  
  methods: {

    fetchUser() {
      if (!this.$store.state.user) {
        this.axios.get(`/api/user/authenticated`).then(response => {
          this.$store.commit('user', response.data);
        });
      }
    },

    switchLanguage() {
      this.axios.post(`${this.routes.post}`).then(response => {
        let user = this.$store.state.user;
        user.language = user.language == 'de' ? 'en' : 'de';
        this.$store.commit('user', user);
      });
    },

    toggleMenu() {
      this.hasMenu = this.hasMenu ? false : true;
    },

    hideMenu() {
      this.hasMenu = false;
    }
  },

  watch: {
    '$route'() {
      this.hideMenu()
    }
  }
}
</script>