module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {

    extend: {
      fontFamily: {
        'sans': ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans']
      },
      fontSize: {
        tiny: ['.675rem', '1']
      },
      colors: {
        'highlight': '#ff008b',
        'dark': '#222222',
        'light': '#f9f9f9',
      },
      zIndex: {
        '1001': '1001',
      }
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
  ],

}