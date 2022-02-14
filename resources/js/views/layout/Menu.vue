<template>
<nav class="min-h-screen flex flex-col items-stretch w-12 sm:w-64 bg-light py-4 sm:py-5">
  <div class="grow">
    <div class="mb-6 px-3 sm:px-5">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40.14 44.9" class="w-5 sm:w-8 h-auto"><polygon points="8.16 0 0 10.21 0 44.9 8.16 34.7 8.16 0"></polygon><polygon points="29.25 43.31 10.89 17.11 10.89 1.6 29.25 27.79 29.25 43.31" class="brand-fill"></polygon><polygon points="40.14 0 31.98 10.21 31.98 44.9 40.14 34.7 40.14 0" class="brand-fill"></polygon></svg>
    </div>
    <ul class="list-none !p-0 !m-0">
      <li class="block !pl-0  !m-0">
        <a href="" class="text-gray-400 text-base font-normal no-underline flex items-center p-3 sm:p-5 hover:text-magenta">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
          </svg>
          <span class="hidden sm:block pl-3">
            Messages
          </span>
        </a>
      </li>
      <li class="block !p-0 !m-0">
        <a href="" class="text-gray-400 text-base font-normal no-underline flex items-center p-3 sm:p-5 hover:text-magenta">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
          </svg>
          <span class="hidden sm:block pl-3">Projects</span>
        </a>
      </li>
      <li class="block !p-0 !m-0">
        <a href="" class="text-gray-400 text-base font-normal no-underline flex items-center p-3 sm:p-5 hover:text-magenta">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <span class="hidden sm:block pl-3">Clients</span>
        </a>
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
      <div class="flex items-center justify-center rounded-full bg-magenta text-white h-8 w-8">
        <span class="text-sm font-mono">MS</span>
      </div>
    </div>
    <div class="text-xs pl-3 text-gray-400">
      {{user}}<br>
      <div class="flex">
        <a href="" class="text-xs text-gray-400 font-normal hover:no-underline">Profile</a>
        <span class="inline-block px-2">&bull;</span>
        <a href="/logout" class="text-xs text-gray-400 font-normal hover:no-underline hover:text-magenta">Logout</a>
      </div>
    </div>
  </div>
</nav>
<!--
  <div>

    <nav :class="[!menuVisible ? '' : 'is-visible', 'page']">
      <header>
        <span>
          <strong>{{user}}</strong><br>
          <a href="/logout" class="feather-icon feather-icon--prepend">
            <log-out-icon size="12"></log-out-icon>
            <span>Logout</span>
          </a>
        </span>
        <a href="javascript:;" @click="toggleMenu()" class="feather-icon ">
          <arrow-right-icon size="24"></arrow-right-icon>
        </a>
      </header>
      <ul>
        <li>
          <router-link :to="{name: 'events'}">
            <span>Edizione</span>
          </router-link>
        </li>
      </ul>
    </nav>
  </div>
  -->
</template>
<script>

// Theme
export default {

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
