<template>
  <div>
    <content-header>
      <template #title>
       {{ translate('403 – Zugriff verweigert') }}
      </template>
    </content-header>
    <main>
      <p class="text-sm lg:text-base text-dark">
        {{ translate('Sorry, du hast keinen Zugriff auf diese Seite!') }}
      </p>
    </main>
  </div>
</template>
<script>
import ContentHeader from "@/components/ui/layout/Header.vue";
import i18n from "@/i18n";

export default {
  components: {
    ContentHeader
  },

  mixins: [i18n],
}
</script>