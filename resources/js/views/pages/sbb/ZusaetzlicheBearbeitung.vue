<template>
<div>
  <content-header>
    <template #title>
      {{ translate('Zusätzliche Bearbeitung') }}
    </template>
  </content-header>
  
  <div class="max-w-5xl">
    <div class="mb-6 lg:mb-8">
      <p class="text-sm sm:text-base !text-black">
        {{ translate('Zusätzliche Bearbeitung im Stundenaufwand. Dieses Produkt ist "extra care" in kleinen Dosierungen, falls am Ende eines Projekts noch ein besonderer Wunsch erfüllt werden kann.') }}
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
          <p class="!mt-2">{{ translate('Wenn ein Visualisierungsprojekt kurz vor dem Abschluss steht, und noch letzte kleinere Anpassungen gewünscht werden, versuchen wir diese, wann immer möglich noch ohne zusätzliche Kosten umzusetzen. Das können wir bei Korrekturen bis zu einer Stunde Arbeitsaufwand.') }}</p>
          <p class="!mt-2">{{ translate('Auch grössere Anpassungen (>1 Stunde Arbeitsaufwand) machen wir gerne noch möglich. Wir verstehen, wenn nicht alle Wünsche im schlanken Prozess Platz finden. Für solche, allenfalls benötigte zusätzliche Schritte, ist diese Option, die Sie als Besteller unkompliziert über die Bestellplattform dem Auftrag hinzufügen können.') }}</p>
          <p class="!mt-2">{{ translate('Die Einheit ist pro Stunde - in Absprache mit ihrem Projektleiter können sie die richtige Anzahl der Stunden für Ihre Anpassung auswählen.') }}</p>
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
              <td class="py-3 lg:text-base text-dark">{{ translate('Zusätzliche Bearbeitung, im Stundenaufwand') }}</td>
              <td class="py-3 text-right lg:text-base font-semibold text-dark">CHF 130.00</td>
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
        pricing: false
      }
    }
  },

  created() {
    this.setPageTitle('Zusätzliche Bearbeitung');
  },

  mounted() {
    if (this.$store.state.user) {
      this.setPageTitle(this.translate('Zusätzliche Bearbeitung'));
    }
  },

  methods: {
    toggleSection(section) {
      this.openSections[section] = !this.openSections[section];
    }
  }
}
</script>