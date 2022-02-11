<template>
<div class="form-row">
  <loading-indicator v-if="isLoading"></loading-indicator>
  <div v-if="isFetched" class="is-loaded">
    <div >
      <draggable 
        :disabled="false"
        v-model="newsletterArticles" 
        @end="order()"
        ghost-class="draggable-ghost"
        draggable=".card"
        class="cards cards__newsletter" v-if="newsletterArticles.length">
        <div
          :class="[a.publish == 0 ? 'is-disabled' : '', 'card card__newsletter is-draggable']"
          v-for="a in newsletterArticles"
          :key="a.id"
        >
          <div>
            <div class="underline">{{ a.title }}</div>
            <div>{{ a.text }}</div>
          </div>
          <div class="card__action">
            <div>
              <router-link
                :to="{name: 'newsletter-article-edit', params: { newsletterId: $props.newsletterId, id: a.id }}"
                class="feather-icon"
              >
                <edit-icon size="18"></edit-icon>
              </router-link>
            </div>
            <div>
              <a
                href="javascript:;"
                class="feather-icon"
                @click.prevent="destroy(a.id,$event)"
              >
                <trash2-icon size="18"></trash2-icon>
              </a>
            </div>
          </div>
        </div>
      </draggable>
    </div>
  </div>
</div>
</template>
<script>

// Icons
import { PlusIcon, EditIcon, Trash2Icon } from 'vue-feather-icons';

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
    EditIcon,
    Trash2Icon,
    PageFooter,
    PageHeader,
    draggable,
  },

  props: {
    newsletterId: {
      type: Number
    }
  },

  mixins: [ErrorHandling, Helpers],

  data() {
    return {
      isLoading: false,
      isFetched: false,
      newsletterArticles: [],
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/newsletter/articles/${this.$props.newsletterId}`).then(response => {
        this.newsletterArticles = response.data.data;
        this.isFetched = true;
      });
    },

    destroy(id, event) {
      if (confirm("Bitte löschen bestätigen!")) {
        let uri = `/api/newsletter/article/${id}`;
        this.isLoading = true;
        this.axios.delete(uri).then(response => {
          this.fetch();
          this.isLoading = false;
        });
      }
    },

    order() {
      let newsletterArticles = this.newsletterArticles.map(function(article, index) {
          article.order = index;
          return article;
      });
      if (this.debounce) return;
      this.debounce = setTimeout(function() {
        this.debounce = false;
        this.axios.post(`/api/newsletter/articles/order`, {newsletterArticles: newsletterArticles}).then((response) => {
          this.$notify({type: 'success', text: 'Reihenfolge angepasst'});
        });
      }.bind(this, newsletterArticles), 500);
    },
  }
}
</script>