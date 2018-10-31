require('../css/app.css');

window.Popper = require('popper.js').default;
try {
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
    require('holderjs');
    var Bloodhound = require('bloodhound-js');
    require('typeahead.js');
    $(document).ready(function() {
        $('[data-toggle="popover"]').popover();
    })
    //require('summernote/dist/summernote-bs4');
} catch (e) {}

$(document).ready(function() {
    $("#accordian a").click(function() {
        var link = $(this);
        var closest_ul = link.closest("ul");
        var parallel_active_links = closest_ul.find(".active")
        var closest_li = link.closest("li");
        var link_status = closest_li.hasClass("active");
        var count = 0;

        closest_ul.find("ul").slideUp(function() {
            if (++count == closest_ul.find("ul").length)
                parallel_active_links.removeClass("active");
        });

        if (!link_status) {
            closest_li.children("ul").slideDown();
            closest_li.addClass("active");
        }
    });
    var searchResult = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        /*prefetch: '',/**/
        remote: {
            url: '/search/find/%QUERY%',
            wildcard: '%QUERY%',
            /*filter: function(list) {
                var uniques = [];
                // parse out unique names

                // my list was literally a list of first and last names so I concat them, you'll likely need to do a bit more work here
                return uniqes.concat(list);
            },/**/
        }
    });

    $('.typeahead').typeahead({
        highlight: true,
        hint: true,
        minLength: 1,
    },{
        source: searchResult,
        display: 'text',
        name: 'usersList',
        limit: 100,
        templates: {
            empty: [
                '<div class="list-group search-results-dropdown"><div class="list-group-item">Nothing found.</div></div>'
            ],
            header: [
                '<div class="list-group search-results-dropdown">'
                ],
            suggestion: function (e) {
                    return '<a href="' + e.link + '" class="list-group-item">' + e.text + '</a>';
                }
            }
    }
    );/**/
})