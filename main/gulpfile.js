var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
 mix.less('app.less', 'resources/assets/css');

 mix.styles([
     'libs/jquery-ui-1.10.4.custom.css',
     'libs/bootstrap-editable.css',
     'libs/bootstrap-multiselect.css',
     'libs/cropper.css',
     'libs/custom_calendar.css',
     'libs/dropzone.css',
     'libs/jquery.fileupload.css',
     'libs/lightbox.css',
     'libs/morris-0.4.3.min.css',
     'libs/sb-admin.css',
     'libs/select2.css',
     'libs/select2-bootstrap.css',
     'libs/timeline.css',
     'libs/bootstrap3-wysihtml5.css',
     'libs/bootstrap-switch.css',
     'app.css'
 ]);

 mix.version('public/css/all.css');

 mix.scripts([
    'libs/jquery-1.10.2.js',
    'libs/jquery-ui-1.10.4.custom.min.js',
    'libs/bootstrap.min.js',
    'libs/jquery.validate.js',
    'libs/messages_pl.js',
    'libs/bootstrap-notify.min.js',
    'libs/bootstrap-timepicker.js',
     'libs/moment.min.js',
     'libs/bootstrap-editable.min.js',
     'libs/bootstrap-multiselect.js',
     'libs/bootstrap.touchspin.js',
     'libs/cropper.min.js',
     'libs/dropzone.min.js',
     'libs/jquery.calendario.js',
     'libs/jquery.fileupload.js',
     'libs/jquery.iframe-transport.js',
     'libs/lightbox.min.js',
     'libs/masonry.pkgd.min.js',
     'libs/modernizr.custom.63321.js',
     'libs/morris.js',
     'libs/raphael-2.1.0.min.js',
     'libs/select2.min.js',
     'libs/select2_locale_pl.js',
     'libs/bootstrap3-wysihtml5.all.js',
     'libs/wysihtml5-parser.js',
     'libs/bootstrap-slider.js',
     'libs/bootstrap-switch.js',
     'main.js'
 ]);

});