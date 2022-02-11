<template>
<div>
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    
    <page-header>
      <h1>Texte</h1>
      <router-link :to="{ name: 'article-create' }" class="feather-icon feather-icon--prepend">
        <plus-icon size="16"></plus-icon>
        <span>Hinzufügen</span>
      </router-link>
    </page-header>

    <div class="listing" v-if="articles.length">
      <draggable 
        :disabled="false"
        v-model="articles" 
        @end="order()"
        ghost-class="draggable-ghost"
        draggable=".listing__item">
        <div
          :class="[a.publish == 0 ? 'is-disabled' : '', 'listing__item is-draggable']"
          v-for="a in articles"
          :key="a.id"
        >
          <div class="listing__item-body">
            {{ a.title }}
          </div>
          <list-actions 
            :id="a.id" 
            :record="a"
            :hasDraggable="true"
            :routes="{edit: 'article-edit'}">
          </list-actions>
        </div>
      </draggable>
    </div>
    <div v-else>
      <p class="no-records">Es sind noch keine Texte vorhanden...</p>
    </div>
    
    <page-footer>
      <router-link :to="{ name: 'itdv' }" class="btn-primary">
        <span>Zurück</span>
      </router-link>
    </page-footer>
    
  </div>
</div>
</template>
<script>

// Icons
import { PlusIcon } from 'vue-feather-icons';

// Components
import ListActions from "@/components/ui/ListActions.vue";
import Separator from "@/components/ui/Separator.vue";
import draggable from "vuedraggable";
import PageFooter from "@/components/ui/PageFooter.vue";
import PageHeader from "@/components/ui/PageHeader.vue";

// Mixins
import ErrorHandling from "@/mixins/ErrorHandling";
import Helpers from "@/mixins/Helpers";

export default {

  components: {
    ListActions,
    Separator,
    PlusIcon,
    PageFooter,
    PageHeader,
    draggable,
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {
      isLoading: false,
      isFetched: false,
      articles: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/articles`).then(response => {
        this.articles = response.data.data;
        this.isFetched = true;
      });
    },

    toggle(id,event) {
      let uri = `/api/article/state/${id}`;
      this.isLoading = true;
      this.axios.get(uri).then(response => {
        const index = this.articles.findIndex(x => x.id === id);
        this.articles[index].publish = response.data;
        this.$notify({ type: "success", text: "Status geändert" });
        this.isLoading = false;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/article/${id}`;
        this.isLoading = true;
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },

    order() {
      let articles = this.articles.map(function(article, index) {
          article.order = index;
          return article;
      });
      if (this.debounce) return;
      this.debounce = setTimeout(function() {
        this.debounce = false;
        this.axios.post(`/api/articles/order`, {articles: articles}).then((response) => {
          this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
        });
      }.bind(this, articles), 500);
    },
  }
}
</script>