const mix = require('laravel-mix');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
mix.webpackConfig({
    plugins: [
        new BrowserSyncPlugin({
            files: [
                'resources/views/**/*',
                'resources/views/**/**/**/*',
                'resources/**/*.js',
                'Modules/**/Resources/views/**/*.php',
                'Modules/**/**/*.php',
                'Modules/**/**/**/*.php',
                'routes/*.php',
            ]
        }, {reload: false})
    ]
});

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

mix.js('resources/js/app.js', 'public/js')
    .scripts('resources/js/bits.js','public/js/bits.js');
    // .sass('resources/sass/app.scss', 'public/css');
