import SbbIndex from '@/views/pages/sbb/Index.vue';
import Sbb360Touren from '@/views/pages/sbb/360TourenVirtuellerRundgang.vue';
import SbbVisualisierungen from '@/views/pages/sbb/Visualisierungen.vue';
import SbbAnimationen from '@/views/pages/sbb/Animationen.vue';
import SbbFotografie from '@/views/pages/sbb/Fotografie.vue';
import SbbZusaetzlicheBearbeitung from '@/views/pages/sbb/ZusaetzlicheBearbeitung.vue';
import Sbb360FotografieBegehung from '@/views/pages/sbb/360FotografieBegehung.vue';

const routes = [
  {
    name: 'sbb',
    path: '/sbb',
    component: SbbIndex,
  },
  {
    name: 'sbb-360-touren-virtueller-rundgang',
    path: '/sbb/360-touren-virtueller-rundgang',
    component: Sbb360Touren,
  },
  {
    name: 'sbb-visualisierungen',
    path: '/sbb/visualisierungen',
    component: SbbVisualisierungen,
  },
  {
    name: 'sbb-animationen',
    path: '/sbb/animationen',
    component: SbbAnimationen,
  },
  {
    name: 'sbb-fotografie',
    path: '/sbb/fotografie',
    component: SbbFotografie,
  },
  {
    name: 'sbb-zusaetzliche-bearbeitung',
    path: '/sbb/zusaetzliche-bearbeitung',
    component: SbbZusaetzlicheBearbeitung,
  },
  {
    name: 'sbb-360-fotografie-begehung',
    path: '/sbb/360-fotografie-begehung',
    component: Sbb360FotografieBegehung,
  },
];

export default routes;