const mix = require('laravel-mix');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
mix.webpackConfig({
    plugins: [
        new BrowserSyncPlugin({
            files: [
                'resources/views/**/*',
                'resources/views/**/**/**/*',
                'resources/**/*.js',
                // 'Modules/**/Resources/views/**/*.php',
                // 'Modules/**/**/*.php',
                // 'Modules/**/**/**/*.php',
                'routes/*.php',
            ]
        }, {reload: false})
    ]
});

mix.js('resources/js/app.js', 'public/js')
    .scripts('resources/js/bits.js','public/js/bits.js')
    .sass('resources/sass/app.scss', 'public/css');
