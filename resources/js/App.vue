<template>
<div class="flex min-h-screen flex-col">
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
import PageMain from '@/views/layout/Main.vue';
import PageContent from '@/views/layout/Content.vue';
export default {
  components: {
    PageMain,
    PageContent,
    PageHeader
  },
}
</script>