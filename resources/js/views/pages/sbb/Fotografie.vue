<template>
<div>
  <content-header>
    <template #title>
      {{ translate('Fotografie') }}
    </template>
  </content-header>
  
  <div class="max-w-5xl">
    <div class="mb-6 lg:mb-8">
      <p class="text-sm sm:text-base !text-black">
        {{ translate('Wir verfügen über professionelles Kameraequipment und fotografieren oft und gerne für unsere Projekte. Auch mehrere eigene Drohnen gehören zu unserer Ausrüstung.') }}
      </p>
    </div>
    
    <div class="text-base">
      <!-- Description Section -->
      <div class="border-b-2 border-gray-100">
        <button 
          @click="toggleSection('description')" 
          class="w-full flex items-center justify-between text-left"
        >
          <h2 class="font-semibold text-md lg:text-lg text-dark !my-2 lg:!my-3">
            {{ translate('Beschreibung') }}
          </h2>
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5 lg:h-6 lg:w-6 text-dark transition-transform duration-200"
            :class="{ 'rotate-45': openSections.description }"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
        </button>
        <div 
          v-show="openSections.description" 
          class="pb-3 lg:pb-6 text-dark space-y-4"
        >
          <p class="!mt-2">{{ translate('Selbst technisch gut gemachte Visualisierungen und Filme leiden oft, wenn Ihnen das richtige Hintergrundmaterial fehlt. Wir haben uns aus diesem Grund über die Jahre eine professionelle fotografische Ausrüstung zugelegt und Erfahrung damit aufgebaut. Auch Luftbilder und Filmaufnahmen gehören zum wichtigen Repertoire, um 360°-Touren, Visualisierungen und Animationen in ihrer Wirkung nicht schon im Grundlagenmaterial zu beschränken.') }}</p>
        </div>
      </div>

      <!-- Examples Section -->
      <div class="border-b-2 border-gray-100">
        <button 
          @click="toggleSection('examples')" 
          class="w-full flex items-center justify-between text-left"
        >
          <h2 class="font-semibold text-md lg:text-lg text-dark !my-2 lg:!my-3">
            {{ translate('Beispiele') }}
          </h2>
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5 lg:h-6 lg:w-6 text-dark transition-transform duration-200"
            :class="{ 'rotate-45': openSections.examples }"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
        </button>
        <div 
          v-show="openSections.examples" 
          class="pb-3 lg:pb-6 text-dark space-y-4"
        >
          <div class="mt-4 space-y-4">
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-fotografie-1.jpg" width="1200" height="800" class="w-full h-auto" alt="Fotografie Beispiel 1">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-fotografie-2.jpg" width="1200" height="800" class="w-full h-auto" alt="Fotografie Beispiel 2">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-fotografie-3.jpg" width="1200" height="800" class="w-full h-auto" alt="Fotografie Beispiel 3">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-fotografie-4.jpg" width="1200" height="800" class="w-full h-auto" alt="Fotografie Beispiel 4">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-fotografie-5.jpg" width="1200" height="800" class="w-full h-auto" alt="Fotografie Beispiel 5">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-fotografie-6.jpg" width="1200" height="800" class="w-full h-auto" alt="Fotografie Beispiel 6">
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Section -->
      <div class="border-b-2 border-gray-100">
        <button 
          @click="toggleSection('pricing')" 
          class="w-full flex items-center justify-between text-left"
        >
          <h2 class="font-semibold text-md lg:text-lg text-dark !my-2 lg:!my-3">
            {{ translate('Preise') }}
          </h2>
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5 lg:h-6 lg:w-6 text-dark transition-transform duration-200"
            :class="{ 'rotate-45': openSections.pricing }"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
        </button>
        <div 
          v-show="openSections.pricing" 
          class="pb-3 lg:pb-6"
        >
          <table class="w-full !m-0">
            <tr class="border-b-2 border-gray-100 last:border-b-0">
              <td class="py-3 lg:text-base text-dark">{{ translate('Fotografie, Still oder Drohne, pro Halbtag, mind. 6 Fotografien') }}</td>
              <td class="py-3 text-right lg:text-base font-semibold text-dark">CHF 400.00</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Back Link -->
    <div class="mt-8">
      <router-link :to="{ name: 'sbb' }" class="text-highlight hover:text-dark font-medium transition-colors duration-200 !no-underline !text-sm">
        {{ translate('Zurück zur Übersicht') }}
      </router-link>
    </div>
  </div>
</div>
</template>

<script>
import ContentHeader from "@/components/ui/layout/Header.vue";
import PageTitle from "@/mixins/PageTitle";
import i18n from "@/i18n";

export default {
  components: {
    ContentHeader
  },

  mixins: [PageTitle, i18n],

  data() {
    return {
      openSections: {
        description: false,
        examples: false,
        pricing: false
      }
    }
  },

  created() {
    this.setPageTitle('Fotografie');
  },

  mounted() {
    if (this.$store.state.user) {
      this.setPageTitle(this.translate('Fotografie'));
    }
  },

  methods: {
    toggleSection(section) {
      this.openSections[section] = !this.openSections[section];
    }
  }
}
</script>