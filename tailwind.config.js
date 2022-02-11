module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'theme-red': '#eb0000',
        'theme-red-dark': '#c60018',
        'theme-magenta': '#ff008b',
        'theme-cyan': '#87ffff',
        'theme-light': '#f9f9f9',
        'theme-dark': '#222222'
      },
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
    // require('@tailwindcss/forms'),
    //require('daisyui'),
  ],

}