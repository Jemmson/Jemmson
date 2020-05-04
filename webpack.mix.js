let mix = require('laravel-mix')
var path = require('path')

const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')


// require ('laravel-mix-tailwind');

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

// mix.less('resources/assets/less/app.less', 'public/css')
//   .js('resources/assets/js/app.js', 'public/js')
//   .webpackConfig({
//     resolve: {
//       modules: [
//         path.resolve(__dirname, 'vendor/laravel/spark/resources/assets/js'),
//         'node_modules'
//       ],
//       alias: {
//         'vue$': 'vue/dist/vue.js'
//       }
//     }
//   });

mix.extend('vuetify', new class {

        // webpackRules() {
        //   return {
        //     test: /\.s(c|a)ss$/,
        //     use: [
        //       'vue-style-loader',
        //       'css-loader',
        //       {
        //         loader: 'sass-loader',
        //         options: {
        //           implementation: require('sass'),
        //           sassOptions: {
        //             fiber: require('fibers'),
        //             indentedSyntax: true
        //           },
        //         },
        //       },
        //     ],
        //   }
        // }

        webpackPlugins() {
            return new VuetifyLoaderPlugin()
        }

    }()
);

// .copy('node_modules/sweetalert/dist/sweetalert.min.js', 'public/js/sweetalert.min.js')
//     .copy('node_modules/sweetalert/dist/sweetalert.css', 'public/css/sweetalert.css')

mix.less('resources/assets/less/app.less', 'public/css')
    .js('resources/assets/js/app.js', 'public/js')
    .version()
    .copy('node_modules/sweetalert2/dist/sweetalert2.min.js', 'public/js/sweetalert.min.js')
    .webpackConfig({
        resolve: {
            modules: [
                path.resolve(__dirname, 'vendor/laravel/spark/resources/assets/js'),
                'node_modules'
            ],
            alias: {
                'vue$': 'vue/dist/vue.js'
            }
        }
    })