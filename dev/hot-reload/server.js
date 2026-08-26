// const browserSync = require("browser-sync").create();

// browserSync.init({
//     proxy: "http://nginx:80",
//     port: 3000,
//     ui: {port: 3001 },
//      files: [
//     "/var/www/html/**/*.php",
//     "/var/www/html/**/*.css",
//     "/var/www/html/**/*.js",
//     "/var/www/html/**/*.html",
//     ],
//     watchOptions: {
//         ignoreInitial: true,
//     },
//     notify: false,
//     open: false,
// });

const browserSync = require("browser-sync").create();

browserSync.init({
    proxy: "http://nginx:80",
    port: 3000,
    ui: { port: 3001 },
    files: [
        {
            match: "/var/www/html/**/*.{php,css,js,html}",
            fn: function (event, file) {
                browserSync.reload();
            },
            options: {
                ignoreInitial: true,
                usePolling: true,
                interval: 1000,
            },
        },
    ],
    notify: false,
    open: false,
});