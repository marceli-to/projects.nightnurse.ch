<template>
  <div>
    <div class="form-group">
      <label>{{label}}</label>
      <select name="companies" @change="change($event)">
        <option value="null">Bitte wählen...</option>
        <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
    </div>

    <div v-if="selection.length" class="form-group">
      <label class="block mb-4">Kunden</label>
      <div class="flex">
        <a 
          href="javascript:;"
          class="rounded-full inline-block bg-dark px-4 py-2 text-white font-mono mr-4 text-base no-underline"
          v-for="s in selection" 
          :key="s.company.id"
          @click.prevent="destroy(s.id)">
          {{ s.company.name }}
        </a>
      </div>
    </div>

  </div>
</template>
<script>
export default {
  
  props: {
    label: {
      type: String,
      default: ""
    },

    labelSelected: {
      type: String,
      default: ""
    },

    projectId: {
      type: Number,
      default: 0,
    }
  },

  data() {
    return {
      companies: [],
      selection: [],

      // Routes
      routes: {
        destroy: '/api/project-company'
      },
    };
  },

  created() {
    this.fetch();
  },

  methods: {

    fetch() {
      this.axios.get(`/api/companies/clients`).then(response => {
        this.companies = response.data.data;
      });
      this.axios.get(`/api/project-companies/${this.$props.projectId}`).then(response => {
        this.selection = response.data.data;
      });
    },

    change(event) {
      let target = event.target, id = target.value, name = target.options[target.selectedIndex].innerHTML;
      const idx = this.selection.findIndex(x => x.id === parseInt(id));
      if (idx == -1 && id != "null") {
        let d = { id: parseInt(id), name: name };
        this.$parent.addCompany(d);
      }
    },

    destroy(id) {
      this.isLoading = true;
      this.axios.delete(`${this.routes.destroy}/${id}`).then(response => {
        this.fetch();
        this.isLoading = false;
      });
    },
  }
};
</script>
