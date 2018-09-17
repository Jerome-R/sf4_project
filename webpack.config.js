 // yarn add @symfony/webpack-encore --dev

var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    // yarn add bootstrap-sass --dev
    .addEntry('js/app', './assets/js/main.js')
    .addStyleEntry('css/app', './assets/scss/global.scss')

    //.addPlugin( new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/))

    // uncomment if you use Sass/SCSS files
    // yarn add --dev sass-loader node-sass
    // .enableSassLoader()
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
    })

    // uncomment for legacy applications that require $/jQuery as a global variable
    // yarn add jquery --dev
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();

var config = Encore.getWebpackConfig();
config.node = { 
    console: false,
    fs: 'empty',
    net: 'empty',
    tls: 'empty' };
module.exports = config;
