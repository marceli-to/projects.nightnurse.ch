<template>
<div>
  <content-header>
    <template #title>
      {{ translate('Animationen') }}
    </template>
  </content-header>
  
  <div class="max-w-5xl">
    <div class="mb-6 lg:mb-8">
      <p class="text-sm sm:text-base !text-black">
        {{ translate('Kaum ein Medium ist heute so beliebt und kann so vielseitig eingesetzt werden, wie animierte Kurzfilme.') }}
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
          <p class="!mt-2">{{ translate('Nichts zieht die Aufmerksamkeit stärker auf sich, als bewegte Bilder. Vom einfachen Cinemagraph (Standbilder mit animierten Teilen) über kurze Teaser-Sequenzen bis zu vollständigen Architekturfilmen haben wir mit drei Standardlängen im SBB Rahmenvertrag sicher eine gute Lösung vorverhandelt.') }}</p>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Anforderungen Leistungsbeschrieb Rahmenvertrag:') }}</h3>
          <p class="!mt-2">{{ translate('Klein (ca. 30 Sekunden), einfache Animation (Walkthrough)') }}<br>{{ translate('Mittel (ca. 60 Sekunden), Kombination Realfilm/3D oder 3D-Animation') }}<br>{{ translate('Gross (ca. 2 Minuten), Kombination Realfilm/3D oder 3D-Animation') }}</p>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Inhaltliche Anforderungen:') }}</h3>
          <ul class="!mt-2 list-disc">
            <li class="!m-0 !mb-2 !p-0">{{ translate('Walkthrough') }}</li>
            <li class="!m-0 !mb-2 !p-0">{{ translate('Kombination von realen (z.B. Filmaufnahmen, Drohnenaufnahmen) und virtuellen Inhalten (siehe Vorgaben für Visualisierungen) zur Veranschaulichung der Umgebung oder von Innenräumen') }}</li>
          </ul>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Technische Anforderungen:') }}</h3>
          <ul class="!mt-2 list-disc">
            <li class="!m-0 !mb-2 !p-0">{{ translate('Ausreichende und unterschiedliche Auflösungen für Web, Präsentationen, Inserate, Online-Werbung etc.') }}</li>
          </ul>
          
          <p class="!mt-6">{{ translate('Für alle Anforderungen, die über die Anforderungen des Rahmenvertrags hinausgehen, bietet die Rubrik "Custom Filme" Zugriff auf unseren gesamten Erfahrungsschatz was Animationen betrifft.') }}</p>
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
          <div class="mt-4">
            <a href="/assets/img/sbb/nightnurse-sbb-animation-demo.mp4" target="_blank" class="block hover:opacity-80 transition-opacity">
              <img src="/assets/img/sbb/nightnurse-sbb-animation-1.jpg" width="1200" height="800" class="w-full h-auto" alt="Animation Beispiel">
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
            <a href="/storage/220111_NNI_SBB_Visual_Workflow_Animationen_DE.pdf" target="_blank" class="flex items-center text-highlight hover:text-dark transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="ml-2">{{ translate('Visual Workflow Animationen') }}</span>
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
              <td class="py-3 lg:text-base text-dark">{{ translate('Film - Klein (ca. 30 Sekunden), einfache Animation (Walkthrough)') }}</td>
              <td class="py-3 text-right lg:text-base font-semibold text-dark">CHF 6'690.00</td>
            </tr>
            <tr class="border-b-2 border-gray-100 last:border-b-0">
              <td class="py-3 lg:text-base text-dark">{{ translate('Film - (ca. 60 Sekunden), Kombination Realfilm/3D oder 3D-Animation') }}</td>
              <td class="py-3 text-right lg:text-base font-semibold text-dark">CHF 13'380.00</td>
            </tr>
            <tr class="border-b-2 border-gray-100 last:border-b-0">
              <td class="py-3 lg:text-base text-dark">{{ translate('Film - (ca. 2 Minuten), Kombination Realfilm/3D oder 3D-Animation') }}</td>
              <td class="py-3 text-right lg:text-base font-semibold text-dark">CHF 26'760.00</td>
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
    this.setPageTitle('Animationen');
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