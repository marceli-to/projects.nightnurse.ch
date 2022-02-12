module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'bright-red': '#eb0000',
        'magenta': '#ff008b',
        'cyan': '#87ffff',
        'light': '#f9f9f9',
        'space-black': '#222222',
        'midnight': '#292f4c',
      },
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
    // require('@tailwindcss/forms'),
    //require('daisyui'),
  ],

}