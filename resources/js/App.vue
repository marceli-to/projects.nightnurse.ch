<template>
<div>
  <notifications />
  <page-header>
  </page-header>
  <page-main>
    <page-content>
      <router-view></router-view>
    </page-content>
  </page-main>
</div>
</template>
<script>
import PageHeader from '@/views/layout/Header.vue';
import PageMenu from '@/views/layout/Menu.vue';
import PageMain from '@/views/layout/Main.vue';
import PageContent from '@/views/layout/Content.vue';
export default {
  components: {
    PageMenu,
    PageMain,
    PageContent,
    PageHeader
  },
}
</script>