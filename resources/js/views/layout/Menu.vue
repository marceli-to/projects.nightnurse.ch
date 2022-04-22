<template>
<nav class="h-full fixed top-0 flex flex-col items-stretch w-12 sm:w-16 bg-light border-r-2 border-gray-100 py-4 sm:py-5 z-50">
  <div class="grow">
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
  </div>

</nav>
</template>
<script>

import { UsersIcon, FolderIcon, OfficeBuildingIcon, UserCircleIcon, } from "@vue-hero-icons/outline";
import { LogoutIcon } from "@vue-hero-icons/solid"
import MenuItem from '@/components/ui/menu/Item';
import MenuItemLabel from '@/components/ui/menu/Label';

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

  props: {
    user: {},
  },

	data() {
    return {}
  },
  
	methods: {

  },

  watch: {
    '$route'() {
    }
  }
}
</script>
