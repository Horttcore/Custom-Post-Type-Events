let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix
    .react('assets/js/index.js', './dist/js/blocks.js')
    .sass('assets/sass/styles.sass', './dist/css/style.css')
    .sass('assets/sass/editorStyles.sass', './dist/css/editor.css')
    .sourceMaps();
