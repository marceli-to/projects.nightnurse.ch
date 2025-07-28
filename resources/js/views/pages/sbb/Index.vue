<template>
<div>
  <content-header>
    <template #title>
      {{ translate('SBB Bestellportal') }}
    </template>
  </content-header>
  
  <div class="max-w-5xl">
    <div class="mb-6 lg:mb-8">
      <p class="text-sm sm:text-base !text-black">
        {{ translate('Animationen, Virtuelle Rundgänge und Visualisierungen nach Rahmenvertrag schnell und einfach bestellen.') }}
      </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
      <article class="bg-white overflow-hidden border-2 border-gray-100 transition-shadow duration-200">
        <figure class="aspect-video !m-0 !p-0">
          <router-link :to="{ name: 'sbb-360-touren-virtueller-rundgang' }" class="block h-full">
            <img src="/assets/img/sbb/nightnurse-sbb-360-tour-1-preview.jpg" width="1200" height="800" class="w-full h-full object-cover !m-0 !p-0" alt="360°-Touren">
          </router-link>
        </figure>
        <div class="p-2 lg:p-4">
          <h3 class="font-bold !text-xl lg:!text-2xl text-dark !mb-3 !mt-0">{{ translate('360°-Touren, Virtueller Rundgang') }}</h3>
          <p class="text-sm text-dark !mb-2">{{ translate('Wohnungen oder Gewerberäume dargestellt in 360°') }}</p>
          <router-link :to="{ name: 'sbb-360-touren-virtueller-rundgang' }" class="text-highlight hover:text-dark font-medium text-sm transition-colors duration-200 !no-underline">
{{ translate('mehr...') }}
          </router-link>
        </div>
      </article>
      
      <article class="bg-white overflow-hidden border-2 border-gray-100 transition-shadow duration-200">
        <figure class="aspect-video !m-0 !p-0">
          <router-link :to="{ name: 'sbb-visualisierungen' }" class="block h-full">
            <img src="/assets/img/sbb/nightnurse-sbb-visualisierung-1-preview.jpg" width="1200" height="800" class="w-full h-full object-cover !m-0 !p-0" alt="Visualisierungen">
          </router-link>
        </figure>
        <div class="p-2 lg:p-4">
          <h3 class="font-bold !text-xl lg:!text-2xl text-dark !mb-3 !mt-0">{{ translate('Visualisierungen') }}</h3>
          <p class="text-sm text-dark !mb-2">{{ translate('Wohnungen oder Gewerberäume dargestellt als Standbild') }}</p>
          <router-link :to="{ name: 'sbb-visualisierungen' }" class="text-highlight hover:text-dark font-medium text-sm transition-colors duration-200 !no-underline">
{{ translate('mehr...') }}
          </router-link>
        </div>
      </article>
      
      <article class="bg-white overflow-hidden border-2 border-gray-100 transition-shadow duration-200">
        <figure class="aspect-video !m-0 !p-0">
          <router-link :to="{ name: 'sbb-animationen' }" class="block h-full">
            <img src="/assets/img/sbb/nightnurse-sbb-animation-1-preview.jpg" width="1200" height="800" class="w-full h-full object-cover !m-0 !p-0" alt="Animationen">
          </router-link>
        </figure>
        <div class="p-2 lg:p-4">
          <h3 class="font-bold !text-xl lg:!text-2xl text-dark !mb-3 !mt-0">{{ translate('Animationen') }}</h3>
          <p class="text-sm text-dark !mb-2">{{ translate('Nichts zieht die Aufmerksamkeit stärker auf sich, als bewegte Bilder.') }}</p>
          <router-link :to="{ name: 'sbb-animationen' }" class="text-highlight hover:text-dark font-medium text-sm transition-colors duration-200 !no-underline">
{{ translate('mehr...') }}
          </router-link>
        </div>
      </article>
      
      <article class="bg-white overflow-hidden border-2 border-gray-100 transition-shadow duration-200">
        <figure class="aspect-video !m-0 !p-0">
          <router-link :to="{ name: 'sbb-fotografie' }" class="block h-full">
            <img src="/assets/img/sbb/nightnurse-sbb-fotografie-1-preview.jpg" width="1200" height="800" class="w-full h-full object-cover !m-0 !p-0" alt="Fotografie">
          </router-link>
        </figure>
        <div class="p-2 lg:p-4">
          <h3 class="font-bold !text-xl lg:!text-2xl text-dark !mb-3 !mt-0">{{ translate('Fotografie') }}</h3>
          <p class="text-sm text-dark !mb-2">{{ translate('Hintergrundaufnahmen für 360°-Touren, Visualisierungen und Animationen') }}</p>
          <router-link :to="{ name: 'sbb-fotografie' }" class="text-highlight hover:text-dark font-medium text-sm transition-colors duration-200 !no-underline">
{{ translate('mehr...') }}
          </router-link>
        </div>
      </article>
      
      <article class="bg-white overflow-hidden border-2 border-gray-100 transition-shadow duration-200">
        <figure class="aspect-video !m-0 !p-0">
          <router-link :to="{ name: 'sbb-zusaetzliche-bearbeitung' }" class="block h-full">
            <img src="/assets/img/sbb/nightnurse-sbb-zusaetzliche-bearbeitung-preview.jpg" width="1200" height="800" class="w-full h-full object-cover !m-0 !p-0" alt="Zusätzliche Bearbeitung">
          </router-link>
        </figure>
        <div class="p-2 lg:p-4">
          <h3 class="font-bold !text-xl lg:!text-2xl text-dark !mb-3 !mt-0">{{ translate('Zusätzliche Bearbeitung') }}</h3>
          <p class="text-sm text-dark !mb-2">{{ translate('Zusatzaufwand, falls nach Abschluss des SBB-Workflows noch Wünsche offen sind') }}</p>
          <router-link :to="{ name: 'sbb-zusaetzliche-bearbeitung' }" class="text-highlight hover:text-dark font-medium text-sm transition-colors duration-200 !no-underline">
{{ translate('mehr...') }}
          </router-link>
        </div>
      </article>
      
      <article class="bg-white overflow-hidden border-2 border-gray-100 transition-shadow duration-200">
        <figure class="aspect-video !m-0 !p-0">
          <router-link :to="{ name: 'sbb-360-fotografie-begehung' }" class="block h-full">
            <img src="/assets/img/sbb/nightnurse-sbb-360-fotografie-1-preview.jpg" width="1200" height="800" class="w-full h-full object-cover !m-0 !p-0" alt="360°-Fotografie/Begehung">
          </router-link>
        </figure>
        <div class="p-2 lg:p-4">
          <h3 class="font-bold !text-xl lg:!text-2xl text-dark !mb-3 !mt-0">{{ translate('360°-Fotografie/Begehung') }}</h3>
          <p class="text-sm text-dark !mb-2">{{ translate('Bestehende Räume in 360° fotografiert und 3D aufgenommen.') }}</p>
          <router-link :to="{ name: 'sbb-360-fotografie-begehung' }" class="text-highlight hover:text-dark font-medium text-sm transition-colors duration-200 !no-underline">
{{ translate('mehr...') }}
          </router-link>
        </div>
      </article>
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

  created() {
    this.setPageTitle(this.translate('SBB Bestellportal'));
  }
}
</script>