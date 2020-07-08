var Encore = require('@symfony/webpack-encore');

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

    .copyFiles({
        from: './assets/themes/default/front/fonts',
        to: 'themes/default/front/fonts/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/themes/default/front/images',
        to: 'themes/default/front/images/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/themes/default/back/fonts',
        to: 'themes/default/back/fonts/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/themes/default/back/images',
        to: 'themes/default/back/images/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/themes/default/super/fonts',
        to: 'themes/default/super/fonts/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/themes/default/super/images',
        to: 'themes/default/super/images/[path][name].[ext]',
    })

    .configureFilenames({
        css: 'css/[name].css',
        js: 'js/[name].js'
    })

    /*
     * ENTRY CONFIG
     *
     */
    .addEntry('default_app', './assets/themes/default/front/js/default.jsx')
    .addEntry('default_homepage', './assets/themes/default/front/js/pages/homepage.jsx')
    .addEntry('default_security', './assets/themes/default/front/js/pages/security.jsx')
    .addEntry('default_legales', './assets/themes/default/front/js/pages/legales.jsx')
    .addEntry('default_contact', './assets/themes/default/front/js/pages/contact.jsx')
    .addEntry('default_booking', './assets/themes/default/front/js/pages/booking.jsx')

    .addEntry('default_admin', './assets/themes/default/back/js/default.jsx')
    .addEntry('default_admin_ticket', './assets/themes/default/back/js/pages/ticket.jsx')

    .addEntry('default_super', './assets/themes/default/super/js/default.jsx')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // enables React
    .enableReactPreset()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    
    //.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
