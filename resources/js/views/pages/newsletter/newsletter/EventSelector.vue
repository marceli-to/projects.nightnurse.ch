<template>
  <div class="sa-sm">
    <div class="cards cards__newsletter" v-if="data.length">
      <div class="card card__newsletter" v-for="s in selection" :key="s.id">
        <div>
          Edizione {{ s.event.number }}, {{ s.event.date }}
        </div>
        <div class="card__action">
          <div>
            <a
              href="javascript:;"
              class="feather-icon"
              @click.prevent="destroy(s.id,$event)"
            >
              <trash2-icon size="18"></trash2-icon>
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="sb-sm">
      <div class="select-wrapper is-card">
        <plus-icon size="16"></plus-icon>
        <select name="events" @change="change($event)">
          <option value="null">Edizione hinzufügen</option>
          <option v-for="e in events" :key="e.id" :value="e.id">Edizione {{ e.number }}, {{ e.date }}</option>
        </select>
      </div>
    </div>
  </div>
</template>
<script>

// Components
import ListActions from "@/components/ui/ListActions.vue";
import { XIcon, EditIcon, Trash2Icon, PlusIcon } from "vue-feather-icons";

export default {
  components: {
    ListActions,
    XIcon,
    EditIcon,
    Trash2Icon,
    PlusIcon
  },

  props: {

    label: {
      type: String,
      default: ""
    },

    labelSelected: {
      type: String,
      default: ""
    },

    name: {
      type: String,
      default: ""
    },

    data: {
      type: Array,
      default: null
    }
  },

  data() {
    return {
      events: [],
      selection: [],
      value: null,
    };
  },

  created() {
    this.fetch();
    this.selection = this.$props.data;
  },

  methods: {

    fetch() {
      this.axios.get(`/api/events`).then(response => {
        this.events = response.data.data;
      });
    },

    change(event) {
      let target = event.target, id = target.value, number = target.options[target.selectedIndex].innerHTML;
      const idx = this.selection.findIndex(x => x.event_id === parseInt(id));
      if (idx == -1 && id != "null") {
        let d = { event_id: parseInt(id), event: {number: number } };
        this.$parent.addEvent(d);
        event.target.selectedIndex = 0;
      }
    },

    destroy(id) {
      const idx = this.selection.findIndex(x => x.id === id);
      if (idx > -1) {
        this.$parent.removeEvent(id);
      }
    },
  }
};
</script>
