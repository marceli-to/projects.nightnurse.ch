export default {
  skin_url: '/assets/js/tinymce/skins/custom',
  branding: false,
  menubar: false,
  statusbar: false,
  external_plugins: {
    link: '/assets/js/tinymce/plugins/link/plugin.min.js',
  },
  plugins: ['lists', 'code', 'link', 'paste'],
  toolbar: 'undo redo | bold | italic | bullist | link | removeformat | code',
  paste_as_text: true,
  height: "360px",
  style_formats_merge: false,
  force_br_newlines : true,
  style_formats: [{
    title: 'Text',
    items: [
      { title: 'Worttrennung deaktivieren', inline: 'span', styles: { "white-space": 'nowrap' } },
      { title: 'Überschrift 1', block : 'h1'},
      { title: 'Überschrift 2', block : 'h2'},
      { title: 'Überschrift 3', block : 'h3'},
    ],
  }],

  link_list: '/filelist',
};
