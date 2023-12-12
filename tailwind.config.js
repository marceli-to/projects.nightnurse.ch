module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {

    extend: {
      gridTemplateColumns: {
        'markup': '56px minmax(auto, 1fr) 300px',
      },
      fontFamily: {
        'sans': ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans']
      },
      fontSize: {
        tiny: ['.675rem', '1'],
        xs: ['.8rem', '1.25'],
        md: ['0.9375rem', '1.55'],
      },
      colors: {
        'highlight': '#ff008b',
        'dark': '#222222',
        'light': '#f9f9f9',
        'alice-blue': '#e8f1ff',
        'alice-blue-dark': '#c7dbfe',
      },
      zIndex: {
        '900': '900',
        '1001': '1001',
      }
    },
  },

  plugins: [
    require('@tailwindcss/typography'),
  ],

}