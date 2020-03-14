$(document).ready(function() {

    // When an iput has an error, and when that input's value was changed, remove the error class so that the style change
    $('.input_wrapper.error > input, .input_wrapper.error > select, .input_wrapper.error.radioes input').change(function() {
        $(this).parents('.input_wrapper.error').removeClass('error');
    });

    // Fixed table header
    var table_with_fixed_header = $('table.table_with_fixed_header');
    if (table_with_fixed_header.length) {

        var fixed_header = table_with_fixed_header.find('tr.fixed_header').clone();
        // appear position
        var header_height = $('section#header_wrapper').height();
        var left_position = table_with_fixed_header.offset().left;

        var fake_table = $("<table/>",{
            "css" : {
                "position" : "fixed",
                "display"  : "none",
                "top"      : header_height,
                "left"     : left_position
            }
        }).appendTo("section.default_table");
        fake_table.append(fixed_header);

        // handle scroll event
        $(window).bind("scroll", function() {
            var offset = $(this).scrollTop();
            //Because Nui Noi changed the container section element ('.default_table') to position relative so we now we have to get the position of the container to calculate.
            // var appear_offset = table_with_fixed_header.find('tr.fixed_header').position().top - header_height;
            // UPDATE: we have to put it here, so that this variable can be re-calculated in real time.
            var appear_offset = $('.default_table').position().top - header_height;

            if (!$('.modal-overlay').length || $('.modal-overlay').is(':hidden')) {
                if (offset >= appear_offset) {
                    fake_table.show();
                }
                else if (offset < appear_offset) {
                    fake_table.hide();
                }
            }
        });
    }

    // prevent multiple submit mechanism
    var submitting = false;

    $('[form-single-submit]').submit( (event) => {
        if (!submitting) {
            submitting = true;
        } else {
            event.preventDefault();
        }
    })

    // prevent multiple click mechanism
    var clicked = false;

    $('[single-click]').click( (event) => {
        if (!clicked) {
            clicked = true;
        } else {
            event.preventDefault();
        }
    })


    //// Extend jquery with some utility function ////
    // Check exists
    $.fn.exists = function () {
        return this.length !== 0;
    }

    // Include the company_code part to the url
    $.companyCodeIncludedUrl = function(url) {
        let currentCompanyCode = _.split(window.location.pathname, '/')[1];
        return '/' + currentCompanyCode + url;
    }
});