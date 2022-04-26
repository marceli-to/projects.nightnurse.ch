<template>
<nav class="h-full fixed top-0 flex flex-col items-stretch w-12 sm:w-16 bg-light border-r-2 border-gray-100 py-4 sm:py-5 z-50">
  <div class="grow relative">
    <div class="mb-6 px-3 sm:px-4">
      <router-link :to="{ name: 'projects' }" class="btn-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-5 sm:w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
      </router-link>
    </div>
    <ul class="list-none !p-0 !m-0">
      <li class="block !p-0 !m-0" v-if="$store.state.user.admin">
        <menu-item :route="'projects'">
          <folder-icon class="h-5 w-5 sm:h-8 sm:w-8" aria-hidden="true" />
        </menu-item>
      </li>
      <li class="block !p-0 !m-0" v-if="$store.state.user.admin">
        <menu-item :route="'companies'">
          <office-building-icon class="h-5 w-5 sm:h-8 sm:w-8" aria-hidden="true" />
        </menu-item>
      </li>
      <li class="block !p-0 !m-0">
        <menu-item :route="'profile-edit'">
          <user-circle-icon class="h-5 w-5 sm:h-8 sm:w-8" aria-hidden="true" />
        </menu-item>
      </li>
      <li class="block !p-0 !m-0">
        <a href="/logout" class="text-gray-400 hover:text-highlight text-base font-normal no-underline flex items-center p-3 sm:p-5">
          <logout-icon class="h-5 w-5 sm:h-8 sm:w-8" aria-hidden="true" />
        </a>
      </li>
    </ul>
    <a href="javascript:;" @click="switchLanguage()" class="flex justify-center absolute bottom-0 tranlate-y-[50%] w-full">
      <template v-if="$store.state.user.language == 'de'">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="#272727" d="M0 0h16v12H0z"/><path fill="#E31D1C" d="M0 4h16v4H0z"/><path fill="#FFD018" d="M0 8h16v4H0z"/></svg>
      </template>
      <template v-if="$store.state.user.language == 'en'">
        <svg width="16" height="11" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="#EEF3F8" d="M0 0h16v11H0z"/><path fill="#41479B" d="M0 0h9v7H0z"/><path fill="#DC251C" d="M9 0h7v1H9zM9 2h7v1H9zM9 4h7v1H9zM9 6h7v1H9zM0 8h16v1H0zM0 10h16v1H0z"/><path fill="#C5D0EC" d="M1 1h1v1H1zM3 1h1v1H3zM5 1h1v1H5zM7 1h1v1H7zM1 3h1v1H1zM3 3h1v1H3zM5 3h1v1H5zM2 4h1v1H2zM4 4h1v1H4zM6 4h1v1H6zM2 2h1v1H2zM4 2h1v1H4zM6 2h1v1H6zM7 3h1v1H7zM1 5h1v1H1zM3 5h1v1H3zM5 5h1v1H5zM7 5h1v1H7z"/></svg>
      </template>
    </a>
  </div>

</nav>
</template>
<script>

import { UsersIcon, FolderIcon, OfficeBuildingIcon, UserCircleIcon, } from "@vue-hero-icons/outline";
import { LogoutIcon } from "@vue-hero-icons/solid"
import MenuItem from '@/components/ui/menu/Item';
import MenuItemLabel from '@/components/ui/menu/Label';
import i18n from "@/i18n";

export default {

  components: {
    FolderIcon,
    UsersIcon,
    OfficeBuildingIcon,
    LogoutIcon,
    UserCircleIcon,
    MenuItem,
    MenuItemLabel,
  },

  mixins: [i18n],

  props: {
    user: {},
  },

	data() {
    return {
      routes: {
        post: '/api/profile/switch-language',
      },
    }
  },
  
	methods: {
    switchLanguage() {
      this.isLoading = true;
      this.axios.post(`${this.routes.post}`).then(response => {
        this.isLoading = false;
        let user = this.$store.state.user;
        user.language = user.language == 'de' ? 'en' : 'de';
        this.$store.commit('user', user);
      });
    },
  },

  watch: {
    '$route'() {
    }
  }
}
</script>
