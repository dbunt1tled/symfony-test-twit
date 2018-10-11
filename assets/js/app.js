require('../css/app.css');

window.Popper = require('popper.js').default;
try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
    require('holderjs');
    $(document).ready(function() {
        $('[data-toggle="popover"]').popover();
    })
    //require('summernote/dist/summernote-bs4');
} catch (e) {}