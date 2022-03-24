const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */

    // mise a jour à partir du 03/09/2021
    .addEntry('app', './assets/app.js')
   // .addEntry('localitate', './assets/js/plug_localizor.js')
    .addEntry('affi', './assets/js/aff_affi.js')
    .addEntry('msg', './assets/js/aff_msg.js')
    .addEntry('secure', './assets/js/secure.js')
    .addEntry('geolocate', './assets/js/aff_geolocate.js')
    .addEntry('placernotice', './assets/js/plug_placernotice.js')
    .addEntry('adress', './assets/elements/indexadress.js')
    .addEntry('parameters', './assets/js/parameter_wb.js')
    .addEntry('msgwebsite', './assets/js/aff_msg_wb.js')
    .addEntry('resizor', './assets/js/aff_resizor.js')
    .addEntry('indexevent','./assets/elements/indexevent.js')
    .addEntry('indexpartner','./assets/elements/indexpartner.js')
    .addEntry('indexwebsite','./assets/elements/indexwebsite.js')
    .addEntry('newboard', './assets/js/aff_newboard.js')
    .addEntry('openday', './assets/js/aff_openday.js')
    .addEntry('post', './assets/js/aff_post.js')
    .addEntry('addarticlefood', './assets/js/aff_addarticlefood.js')
    .addEntry('cargo', './assets/js/aff_cargo.js')
    .addEntry('calendara', './assets/js/aff_calendara.js')
    .addEntry('offre', './assets/js/aff_offre.js')
    .addEntry('buller','./assets/js/aff_addbulle.js')
    .addEntry('calendaraffi', './assets/js/plug_calendar.js')
    .addEntry('addentity', './assets/elements/indexentity.js')
    .addEntry('localitate', './assets/elements/indexlocate.js')
    .addEntry('wyswyg', './assets/js/scripts/bootstrap-wysiwyg.js')
    .addEntry('hotkeys', './assets/js/scripts/external/jquery.hotkeys.js')
    .addEntry('prettify', './assets/js/scripts/external/google-code-prettify/prettify.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    .enableTypeScriptLoader()

    // uncomment if you use React
    .enablePreactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
