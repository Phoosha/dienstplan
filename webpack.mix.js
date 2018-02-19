let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts('resources/assets/js/menu.js', 'public/js/menu.js')
    .js('resources/assets/js/ui.js', 'public/js')
    .js('resources/assets/js/adminui.js', 'public/js')
    .js('resources/assets/js/datepicker.js', 'public/js');

mix.sass('resources/assets/sass/main.scss', 'public/css')
    .sass('resources/assets/sass/late.scss', 'public/css');

mix.extract([ 'jquery' ]);

if (mix.inProduction()) {
    mix.version();
} else {
    mix.browserSync('localhost');
}
