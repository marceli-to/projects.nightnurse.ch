const mix = require('laravel-mix');

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            //'vue$': 'vue/dist/vue.esm.js',
            '@': __dirname + '/resources/js/',
        },
    },
});


mix.js("resources/js/app.js", "public/js").vue()
    .postCss("resources/css/app.css", "public/css", [
    require("tailwindcss"),
]);

