const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.less('resources/assets/less/app.less', 'public/css').version();

mix.styles([
    'resources/assets/css/libs/jquery-ui-1.10.4.custom.css',
    'resources/assets/css/libs/bootstrap-editable.css',
    'resources/assets/css/libs/bootstrap-multiselect.css',
    'resources/assets/css/libs/cropper.css',
    'resources/assets/css/libs/custom_calendar.css',
    'resources/assets/css/libs/dropzone.css',
    'resources/assets/css/libs/jquery.fileupload.css',
    'resources/assets/css/libs/lightbox.css',
    'resources/assets/css/libs/morris-0.4.3.min.css',
    'resources/assets/css/libs/sb-admin.css',
    'resources/assets/css/libs/select2.css',
    'resources/assets/css/libs/select2-bootstrap.css',
    'resources/assets/css/libs/timeline.css',
    'resources/assets/css/libs/bootstrap3-wysihtml5.css',
    'resources/assets/css/libs/bootstrap-switch.css',
], 'public/css/all.css').version();

mix.scripts([
    'resources/assets/js/libs/jquery-1.10.2.js',
    'resources/assets/js/libs/jquery-ui-1.10.4.custom.min.js',
    'resources/assets/js/libs/bootstrap.min.js',
    'resources/assets/js/libs/jquery.validate.js',
    'resources/assets/js/libs/messages_pl.js',
    'resources/assets/js/libs/bootstrap-notify.min.js',
    'resources/assets/js/libs/bootstrap-timepicker.js',
    'resources/assets/js/libs/moment.min.js',
    'resources/assets/js/libs/bootstrap-editable.min.js',
    'resources/assets/js/libs/bootstrap-multiselect.js',
    'resources/assets/js/libs/bootstrap.touchspin.js',
    'resources/assets/js/libs/cropper.min.js',
    'resources/assets/js/libs/dropzone.min.js',
    'resources/assets/js/libs/jquery.calendario.js',
    'resources/assets/js/libs/jquery.fileupload.js',
    'resources/assets/js/libs/jquery.iframe-transport.js',
    'resources/assets/js/libs/lightbox.min.js',
    'resources/assets/js/libs/masonry.pkgd.min.js',
    'resources/assets/js/libs/modernizr.custom.63321.js',
    'resources/assets/js/libs/morris.js',
    'resources/assets/js/libs/raphael-2.1.0.min.js',
    'resources/assets/js/libs/select2.min.js',
    'resources/assets/js/libs/select2_locale_pl.js',
    'resources/assets/js/libs/bootstrap3-wysihtml5.all.js',
    'resources/assets/js/libs/wysihtml5-parser.js',
    'resources/assets/js/libs/bootstrap-slider.js',
    'resources/assets/js/libs/bootstrap-switch.js',
    'resources/assets/js/main.js'
], 'public/js/main.js').version();