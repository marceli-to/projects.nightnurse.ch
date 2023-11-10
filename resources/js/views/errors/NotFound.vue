<template>
  <div>
    <content-header>
      <template #title>
       {{ translate('404 – Seite nicht gefunden') }}
      </template>
    </content-header>
    <main>
      <p class="text-sm lg:text-base text-dark">
        {{ translate('Sorry, ich konnte die gewünschte Seite leider nicht finden!') }}
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