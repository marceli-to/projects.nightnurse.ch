export default {

  methods: {

    moneyFormat(amount) {
      return amount.toFixed(2);
    },

    shorten(str, length = 10, suffix = "...") {
      return str.substring(0, length) + suffix;
    },

    randomString() {
      return Math.random().toString(36).slice(2);
    },

    fileUri(file) {
      if (file.folder) {
        return `/storage/uploads/${file.folder}/${file.name}`;
      }
      return `/storage/uploads/${file.name}`;
    },

    thumbnailUri(file) {
      if (file.folder) {
        return `/img/thumbnail/${file.folder}/${file.name}`;
      }
      return `/img/thumbnail/${file.name}`;
    },
  }
};