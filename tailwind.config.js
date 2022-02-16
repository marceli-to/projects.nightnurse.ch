module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {

    extend: {
      fontSize: {
        tiny: ['.775rem', '1']
      },
      colors: {
        'highlight': '#ff008b',
        'dark': '#222222',
        'light': '#f9f9f9',
      },
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
  ],

}