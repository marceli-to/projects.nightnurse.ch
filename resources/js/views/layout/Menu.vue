<template>
<nav class="min-h-screen max-h-screen sticky top-0 flex flex-col items-stretch w-12 sm:w-16 bg-light border-r-2 border-gray-100 py-4 sm:py-5 z-20">
  <div class="grow">
    <div class="mb-6 px-3 sm:px-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-5 sm:w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
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
      <!-- <li class="block !p-0 !m-0">
        <menu-item :route="'users'">
          <users-icon class="h-5 w-5 sm:h-8 sm:w-8" aria-hidden="true" />
        </menu-item>
      </li> -->
      <li class="block !p-0 !m-0">
        <a href="/logout" class="text-gray-400 hover:text-highlight text-base font-normal no-underline flex items-center p-3 sm:p-5">
          <logout-icon class="h-5 w-5 sm:h-8 sm:w-8" aria-hidden="true" />
        </a>
      </li>
    </ul>
  </div>
  <!-- <div class="hidden sm:flex items-center px-4 pt-4">
    <div class="">
      <div class="flex items-center justify-center rounded-full bg-highlight text-white h-8 w-8">
        <span class="text-sm font-mono">MS</span>
      </div>
    </div>
    <div class="text-xs pl-3 text-gray-400 hidden">
      {{user.firstname}} {{user.name}}<br>
      <div class="flex">
        <a href="" class="text-xs text-gray-400 font-normal hover:no-underline">Profile</a>
        <span class="inline-block px-2">&bull;</span>
        <a href="/logout" class="text-xs text-gray-400 font-normal hover:no-underline hover:text-highlight">Logout</a>
      </div>
    </div>
  </div> -->
</nav>
</template>
<script>

import { UsersIcon, FolderIcon, OfficeBuildingIcon, LogoutIcon } from "@vue-hero-icons/outline";
import MenuItem from '@/components/ui/menu/Item';
import MenuItemLabel from '@/components/ui/menu/Label';

export default {

  components: {
    FolderIcon,
    UsersIcon,
    OfficeBuildingIcon,
    LogoutIcon,
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
