export default {

  data() {
    return {  
      pageTitle: document.querySelector('title'),
      pageTitlePrefix: 'Project Room',
      pageTitleSuffix: 'Nightnurse',
    };
  },

  methods: {
    setPageTitle(value = null) {
      if (value) {
        this.pageTitle.textContent = `${this.pageTitlePrefix} - ${value} - ${this.pageTitleSuffix}`;
      }
      else {
        this.pageTitle.textContent = `${this.pageTitlePrefix} - ${this.pageTitleSuffix}`;
      }
    }
  }

};