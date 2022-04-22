export default {
  
  data() {
    return {

      de: {
        'Projekte': 'Projekte',
        'Projekt bearbeiten': 'Projekt bearbeiten',
        'Projekt erstellen': 'Projekt erstellen',
      },
      
      en: {
        'Projekte': 'Projects',
        'Projekt bearbeiten': 'Edit project',
        'Projekt erstellen': 'Create project',
      }
    }
  },

  methods: {

    translate(key) {
      return this[this._locale()][key];
    },

    _locale() {
      let ll = this.$store.state.user ? this.$store.state.user.language : 'de';
      return ll;
    },
  }
};