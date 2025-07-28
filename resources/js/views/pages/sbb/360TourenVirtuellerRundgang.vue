<template>
<div>
  <content-header>
    <template #title>
      {{ translate('360°-Touren, Virtueller Rundgang') }}
    </template>
  </content-header>
  
  <div class="max-w-5xl">
    <div class="mb-6 lg:mb-8">
      <p class="text-sm sm:text-base !text-black">
        {{ translate('Tauchen sie ein in Ihr Projekt mit virtuellen 360°-Rundgängen. Ohne technische Hilfsmittel, direkt über ihren Browser') }}
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
          class="pb-3 lg:pb-6  text-dark space-y-4"
        >
          <p class="!mt-2">{{ translate('Besteller für Visualisierungen von SBB-Projekte können 360°-Rundgänge bei uns in Auftrag geben. Diese bilden die ideale Grundlage für weitere Visualisierungsprodukte und können z.B. für Renderings oder Filme als Grundlage dienen. Aber auch als "Standalone" auf einer Projektwebsite lassen sich virtuelle Rundgänge gut einsetzen.') }}</p>
          <p class="!mt-2">{{ translate('360°-Bilder in virtuellen Rundgängen haben als Marketinginstrument mehrere Vorteile: Die Art der Darstellung lädt zum Verweilen und Auskundschaften des Projektes ein und verbessert so die Auseinandersetzung mit dem Projekt deutlich.') }}</p>
          <p class="!mt-2">{{ translate('360°-Touren von Nightnurse Images geben dem Betrachter die Möglichkeit, sich im virtuellen Raum umzusehen und die Route durch das Projekt frei zu wählen. Dies eignet sich ideal für Wohnungstouren, um eine Bürowelt zu begehen, aber um ein Gesamtprojekt mit mehreren Gebäuden oder Landschaftsräumen zu erleben.') }}</p>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Anforderungen Leistungsbeschrieb Rahmenvertrag:') }}</h3>
          <p class="!mt-2">{{ translate('Klein: 1-Zimmer-Wohnung ca. 30 m2, mind. 2 Hotspots') }}<br>{{ translate('Mittel: 2-Zimmer-Wohnung ca. 60 m2, mind. 4 Hotspots') }}<br>{{ translate('Gross: 3- bis 4-Zimmer-Wohnung ca. 90 m2, mind. 6 Hotspots') }}</p>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Inhaltliche Anforderungen:') }}</h3>
          <ul class="!mt-2 list-disc">
           <li class="!m-0 !mb-2 !p-0 ">{{ translate('Siehe Vorgaben für Visualisierungen') }}</li>
           <li class="!m-0 !mb-2 !p-0">{{ translate('Adäquate Aussicht mit Berücksichtigung des Stockwerks und Navigation') }}</li>
          </ul>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Technische Anforderungen:') }}</h3>
          <ul class="!mt-2 list-disc">
           <li class="!m-0 !mb-2 !p-0">{{ translate('Bedienbarkeit / Devices') }}</li>
           <li class="!m-0 !mb-2 !p-0">{{ translate('Optimiert für Mobile/Tablet (Touch)') }}</li>
           <li class="!m-0 !mb-2 !p-0">{{ translate('Optimiert für Desktop (Maus, Tastatur)') }}</li>
           <li class="!m-0 !mb-2 !p-0">
              {{ translate('Verschiedene Funktionalitäten') }}
              <ul class=" mt-2 list-disc space-y-1">
               <li class="!m-0 !mb-2 !p-0">{{ translate('Vogelperspektive (Modell von oben)') }}</li>
               <li class="!m-0 !mb-2 !p-0">{{ translate('Grundriss') }}</li>
               <li class="!m-0 !mb-2 !p-0">{{ translate('Navigation zwischen Räumen') }}</li>
               <li class="!m-0 !mb-2 !p-0">{{ translate('Informationen und Labels in den verschiedenen Räumen platzieren') }}</li>
              </ul>
            </li>
           <li class="!m-0 !mb-2 !p-0">{{ translate('Möglichkeit die Tour auf SBB eigenem Webserver zu hosten') }}</li>
          </ul>
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
            <a href="https://nightnurse.ch/360/22H27_SBB_Nordtrakt_HB/index.html" target="_blank" class="block hover:opacity-80 transition-opacity">
              <img src="/assets/img/sbb/nightnurse-sbb-360-tour-1.jpg" width="1200" height="800" class="w-full h-auto" alt="360° Tour Beispiel 1">
            </a>
            <a href="https://nightnurse.ch/360/19H43%20Markstein%20Roemerstrasse%20Baden/index.html" target="_blank" class="block hover:opacity-80 transition-opacity">
              <img src="/assets/img/sbb/nightnurse-sbb-360-tour-2.jpg" width="1200" height="800" class="w-full h-auto" alt="360° Tour Beispiel 2">
            </a>
            <a href="https://nightnurse.ch/360/18F26%20HRS%20Breeze%20Arbon/180803_A4/HRS_Breeze_360%20A4.html" target="_blank" class="block hover:opacity-80 transition-opacity">
              <img src="/assets/img/sbb/nightnurse-sbb-360-tour-3.jpg" width="1200" height="800" class="w-full h-auto" alt="360° Tour Beispiel 3">
            </a>
          </div>
        </div>
      </div>

      <!-- Workflow Section -->
      <div class="border-b-2 border-gray-100">
        <button 
          @click="toggleSection('workflow')" 
          class="w-full flex items-center justify-between text-left"
        >
          <h2 class="font-semibold text-md lg:text-lg text-dark !my-2 lg:!my-3">
            {{ translate('Workflow') }}
          </h2>
          <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-5 w-5 lg:h-6 lg:w-6 text-dark transition-transform duration-200"
            :class="{ 'rotate-45': openSections.workflow }"
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
        </button>
        <div 
          v-show="openSections.workflow" 
          class="pb-3 lg:pb-6 text-dark space-y-4"
        >
          <p class="!mt-2">
            <a href="/storage/220111_NNI_SBB_Visual_Workflow_DE.pdf" target="_blank" class="flex items-center text-highlight hover:text-dark transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="ml-2">{{ translate('Visual Workflow für 360°-Rundgänge') }}</span>
            </a>
          </p>
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
              <td class="py-3  lg:text-base text-dark">{{ translate('360°-Touren, Virtueller Rundgang, klein (bis 30m2), 2 Hotspots') }}</td>
              <td class="py-3 text-right  lg:text-base font-semibold text-dark">CHF 3'500.00</td>
            </tr>
            <tr class="border-b-2 border-gray-100 last:border-b-0">
              <td class="py-3  lg:text-base text-dark">{{ translate('360°-Touren, Virtueller Rundgang, mittel (bis 60m2), 4 Hotspots') }}</td>
              <td class="py-3 text-right  lg:text-base font-semibold text-dark">CHF 4'500.00</td>
            </tr>
            <tr class="border-b-2 border-gray-100 last:border-b-0">
              <td class="py-3  lg:text-base text-dark">{{ translate('360°-Touren, Virtueller Rundgang, gross (bis 90m2), 6 Hotspots') }}</td>
              <td class="py-3 text-right  lg:text-base font-semibold text-dark">CHF 5'500.00</td>
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
        workflow: false,
        pricing: false
      }
    }
  },

  created() {
    this.setPageTitle('360°-Touren, Virtueller Rundgang');
  },

  mounted() {
    this.fetchUser();
  },

  methods: {
    fetchUser() {
      if (!this.$store.state.user) {
        this.axios.get(`/api/user/authenticated`).then(response => {
          this.$store.commit('user', response.data);
          if (!this.$store.state.user.is_sbb) {
            this.$router.push({ name: 'forbidden' });
          }
        });
      }
    },

    toggleSection(section) {
      this.openSections[section] = !this.openSections[section];
    }
  }
}
</script>