<template>
<div>
  <content-header>
    <template #title>
      {{ translate('Visualisierungen') }}
    </template>
  </content-header>
  
  <div class="max-w-5xl">
    <div class="mb-6 lg:mb-8">
      <p class="text-sm sm:text-base !text-black">
        {{ translate('Nightnurse SBB-Vermarktungsvisualisierungen sind realistisch und doch stimmungsvoll. Die richtige Wahl der Kamera ist dabei genau so entscheidend wie die passende Einrichtung.') }}
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
          <p class="!mt-2">{{ translate('Visualisierungen (Standbilder) sind die ideale Ergänzung zu 360°-Touren. Sie können auf Websites und in Präsentationen genauso eingesetzt werden, wie auf Drucksachen und Bautafeln.') }}</p>
          <p class="!mt-2">{{ translate('Als Besteller von SBB-Projekten können Sie sie ganz einfach hier in Auftrag geben. Die Bilder lassen sich leicht auf Grundlage einer 360°-Tour erstellen, können aber auch einzeln in Auftrag gegeben werden.') }}</p>
          <p class="!mt-2">{{ translate('Wir produzieren die Visualisierungen von Ihren Architektur- oder Innenarchitektur-Plänen, oder auch von vorhandenen 3D-Modellen.') }}</p>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Anforderungen Leistungsbeschrieb Rahmenvertrag:') }}</h3>
          <ul class="!mt-2 list-disc">
            <li class="!m-0 !mb-2 !p-0">{{ translate('2 Korrekturrunden sind einzurechnen') }}</li>
            <li class="!m-0 !mb-2 !p-0">
              {{ translate('Fixpreis für Fotografie (falls nicht von SBB zur Verfügung gestellt), pro Projekt:') }}
              <ul class="mt-2 list-disc space-y-1">
                <li class="!m-0 !mb-2 !p-0">{{ translate('Fotografie vor Ort') }}</li>
                <li class="!m-0 !mb-2 !p-0">{{ translate('Drohnenfotografie mind. 6 Fotos pro Halbtag (siehe Rubrik «Fotografie»)') }}</li>
              </ul>
            </li>
            <li class="!m-0 !mb-2 !p-0">
              {{ translate('Einheitspreis für zusätzliche Leistungen:') }}
              <ul class="mt-2 list-disc space-y-1">
                <li class="!m-0 !mb-2 !p-0">{{ translate('Autorkorrekturen (siehe Rubrik «zusätzliche Bearbeitung»)') }}</li>
              </ul>
            </li>
          </ul>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Inhaltliche Anforderungen:') }}</h3>
          <ul class="!mt-2 list-disc">
            <li class="!m-0 !mb-2 !p-0">{{ translate('Zielgruppengerechte Darstellungen der Visualisierungen z.B. Familien, Singles, Kreative etc. (auf Basis projektspezifischer Nutzungsanalyse und ggf. weiteren Angaben SBB)') }}</li>
            <li class="!m-0 !mb-2 !p-0">{{ translate('Zielgruppengerechte Möblierung und Ausstattung der Räume (Style)') }}</li>
            <li class="!m-0 !mb-2 !p-0">{{ translate('Belebung von Innenräumen wie Gastro-/Retailflächen und bei Aussenvisualisierungen, Plätzen etc. mit Personen. Wohnungen sollen ohne Personen dargestellt werden. Es soll jedoch klar ersichtlich sein, dass die Wohnung bewohnt wird.') }}</li>
            <li class="!m-0 !mb-2 !p-0">{{ translate('Transport und Darstellung der architektonischen Inhalte und Vermarktungsidee') }}</li>
          </ul>
          
          <h3 class="!mt-6 font-semibold text-dark text-md lg:text-lg">{{ translate('Technische Anforderungen:') }}</h3>
          <ul class="!mt-2 list-disc">
            <li class="!m-0 !mb-2 !p-0">{{ translate('Ausschnitte der Bilder sollten im Hoch- und Querformat genutzt werden können (genügend Bildinformation links und rechts des Objektes, auch für Webanwendungen (z.B. Banner mit starkem Querformat etc.)') }}</li>
            <li class="!m-0 !mb-2 !p-0">{{ translate('Bildbreite ca. 4000 Pixel entspricht in etwa 34 cm bei einer Auflösung von 300 ppi.') }}</li>
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
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-visualisierung-1.jpg" width="1200" height="800" class="w-full h-auto" alt="Visualisierung Beispiel 1">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-visualisierung-2.jpg" width="1200" height="800" class="w-full h-auto" alt="Visualisierung Beispiel 2">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-visualisierung-3.jpg" width="1200" height="800" class="w-full h-auto" alt="Visualisierung Beispiel 3">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-visualisierung-4.jpg" width="1200" height="800" class="w-full h-auto" alt="Visualisierung Beispiel 4">
            </div>
            <div class="block">
              <img src="/assets/img/sbb/nightnurse-sbb-visualisierung-5.jpg" width="1200" height="800" class="w-full h-auto" alt="Visualisierung Beispiel 5">
            </div>
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
              <span class="ml-2">{{ translate('Visual Workflow für Visualisierungen') }}</span>
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
              <td class="py-3 lg:text-base text-dark">{{ translate('Vermarktungsvisualisierung') }}</td>
              <td class="py-3 text-right lg:text-base font-semibold text-dark">CHF 1'750.00</td>
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
    this.setPageTitle('Visualisierungen');
  },

  mounted() {
    if (this.$store.state.user) {
      this.setPageTitle(this.translate('Visualisierungen'));
    }
  },

  methods: {
    toggleSection(section) {
      this.openSections[section] = !this.openSections[section];
    }
  }
}
</script>