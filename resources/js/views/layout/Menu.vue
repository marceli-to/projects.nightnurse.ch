<template>
<nav class="min-h-screen max-h-screen sticky top-0 flex flex-col items-stretch w-12 sm:w-64 bg-light py-4 sm:py-5">
  <div class="grow">
    <div class="mb-6 px-3 sm:px-5">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-5 sm:w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
    </div>
    <ul class="list-none !p-0 !m-0">
      <li class="block !pl-0 !m-0">
        <menu-item :route="'dashboard'">
          <AnnotationIcon class="h-5 w-5" aria-hidden="true" />
          <menu-item-label>
            Nachrichten
          </menu-item-label>
        </menu-item>
      </li>
      <li class="block !p-0 !m-0">
        <menu-item :route="'projects'">
          <FolderIcon class="h-5 w-5" aria-hidden="true" />
          <menu-item-label>
            Projekte
          </menu-item-label>
        </menu-item>
      </li>
      <li class="block !p-0 !m-0">
        <menu-item :route="'companies'">
          <UsersIcon class="h-5 w-5" aria-hidden="true" />
          <menu-item-label>
            Kunden
          </menu-item-label>
        </menu-item>
      </li>
      <li class="block !p-0 !m-0">
        <menu-item :route="'users'">
          <UserIcon class="h-5 w-5" aria-hidden="true" />
          <menu-item-label>
            Benutzer
          </menu-item-label>
        </menu-item>
      </li>
    </ul>
  </div>
  <div class="sm:hidden p-3 pb-0 text-gray-400">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
    </svg>
  </div>
  <div class="hidden sm:flex items-center px-5 pt-4">
    <div class="">
      <div class="flex items-center justify-center rounded-full bg-highlight text-white h-8 w-8">
        <span class="text-sm font-mono">MS</span>
      </div>
    </div>
    <div class="text-xs pl-3 text-gray-400">
      {{user}}<br>
      <div class="flex">
        <a href="" class="text-xs text-gray-400 font-normal hover:no-underline">Profile</a>
        <span class="inline-block px-2">&bull;</span>
        <a href="/logout" class="text-xs text-gray-400 font-normal hover:no-underline hover:text-highlight">Logout</a>
      </div>
    </div>
  </div>
</nav>
</template>
<script>

import { AnnotationIcon, UsersIcon, UserIcon, FolderIcon } from "@vue-hero-icons/outline";
import MenuItem from '@/components/ui/menu/Item';
import MenuItemLabel from '@/components/ui/menu/Label';

export default {

  components: {
    AnnotationIcon,
    FolderIcon,
    UsersIcon,
    UserIcon,
    MenuItem,
    MenuItemLabel,
  },

  props: {
    user: '',
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
