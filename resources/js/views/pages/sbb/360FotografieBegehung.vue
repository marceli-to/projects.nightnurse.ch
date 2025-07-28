<template>
  <div>
    <content-header>
      <template #title>
        {{ translate('360°-Fotografie/Begehung') }}
      </template>
    </content-header>
    
    <div class="max-w-5xl">
      <div class="mb-6 lg:mb-8">
        <p class="text-sm sm:text-base !text-black">
          {{ translate('Begehen Sie bereits gebaute Räume virtuell in 360° direkt über Ihren Browser') }}
        </p>
      </div>
      
      <div class="">
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
            class="pb-3 lg:pb-6 text-sm text-dark space-y-4"
          >
            <p class="!mt-2">{{ translate('Mit Hilfe einer 360° Kamera (Matterport Pro) nehmen wir bestehende Räume auf. Dabei werden viele Aufnahmen in einem sehr engen Abstand gemacht um die Räume aus vielen möglichen Perspektiven erleben zu können. Zusätzlich wird aus den Aufnahmen ein 3D-Modell erzeugt, welches unterstützend zur Navigation durch die Räume verwendet wird.') }}</p>
            <p class="!mt-2">{{ translate('Die Räume lassen sich dadurch komfortable virtuell am Bildschirm im Webbrowser oder Ihrem Tablet erkunden.') }}</p>
            <p class="!mt-2">{{ translate('Der Preis ist abhängig von der Grösse und Komplexität der Räume. Kontaktieren Sie uns dazu bitte mit Plänen und Fotos, dann können wir Ihnen eine verbindliche Angabe machen.') }}</p>
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
            class="pb-3 lg:pb-6 text-sm text-dark space-y-4"
          >
            <p class="!mt-2">{{ translate('Da wir noch keine der von uns aufgenommenen Räume zeigen dürfen, anbei ein Beispiel von der Matterport-Webseite:') }}</p>
            <div class="mt-4">
              <a href="https://matterport.com/discover/space/D8QznvatRbY" target="_blank" class="block hover:opacity-80 transition-opacity">
                <img src="/assets/img/sbb/nightnurse-sbb-360-fotografie-2.jpg" width="1200" height="800" class="w-full h-auto" alt="360° Fotografie Beispiel">
              </a>
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
            class="pb-3 lg:pb-6">
            <table class="w-full !m-0">
              <tr class="border-b border-gray-200 last:border-b-0">
                <td class="py-3 text-sm lg:text-base text-dark">{{ translate('im Stundenaufwand') }}</td>
                <td class="py-3 text-right text-sm lg:text-base font-bold text-dark">CHF 130.00</td>
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
    this.setPageTitle('360°-Fotografie/Begehung');
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