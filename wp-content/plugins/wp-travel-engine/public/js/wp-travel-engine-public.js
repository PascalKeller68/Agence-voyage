jQuery(document).ready(function ($) {
    if (!wte.single_showtabs) {
        $('.tab-inner-wrapper .tab-anchor-wrapper:first-child').addClass('nav-tab-active');
        $('.nb-tab-trigger').on('click',function () {
            $('.nb-tab-trigger').removeClass('nav-tab-active');
            $('.nb-tab-trigger').parent().parent().removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $(this).parent().parent().addClass('nav-tab-active');
            var configuration = $(this).data('configuration');
            $('.nb-configurations').hide();
            $('.nb-' + configuration + '-configurations').show();
        });

        // http://www.entheosweb.com/tutorials/css/tabs.asp
        $(".tab_content").hide();
        $(".tab_content:first").show();

        /* if in tab mode */
        $("ul.tabs li").on('click',function () {
            $(".tab_content").hide();
            var activeTab = $(this).attr("rel");
            $("#" + activeTab).fadeIn();

            $("ul.tabs li").removeClass("active");
            $(this).addClass("active");

            $(".tab_drawer_heading").removeClass("d_active");
            $(".tab_drawer_heading[rel^='" + activeTab + "']").addClass("d_active");
        });
        /* if in drawer mode */
        $(".tab_drawer_heading").on('click',function () {
            $(".tab_content").hide();
            var d_activeTab = $(this).attr("rel");
            $("#" + d_activeTab).fadeIn();

            $(".tab_drawer_heading").removeClass("d_active");
            $(this).addClass("d_active");

            $("ul.tabs li").removeClass("active");
            $("ul.tabs li[rel^='" + d_activeTab + "']").addClass("active");
        });


        /* Extra class "tab_last"
            to add border to right side
            of last tab */
        $('ul.tabs li').last().addClass("tab_last");
    }
    $(function () {
        var $radios = $('.payment-check');
        if ($radios.is(':checked') === false && $radios.is(':visible')) {
            $radios.filter('[value=paypal]').prop('checked', true);
            $('.stripe-button').removeClass('active');
            $('.stripe-button-el').hide();
            $('#wp-travel-engine-order-form').attr('action', WP_OBJ.link.paypal_link);
        }
    });

    $('body').on('click', '.paypal-form', function (e) {

        var url = $('.stripe_checkout_app').attr('src');

        //Then assign the src to null, this then stops the video been playing
        $('.stripe_checkout_app').attr('src', '');

        // Finally you reasign the URL back to your iframe, so when you hide and load it again you still have the link
        $('.stripe_checkout_app').attr('src', url);

        $('#wp-travel-engine-order-form').submit();
    });

    $('body').on('click', '.payment-check', function (e) {
        if ($(this).is(':checked')) {
            if ($(this).attr('value') == 'stripe') {
                $('#wp-travel-engine-order-form').attr('action', WP_OBJ.link.form_link);
                $('.paypal-form').hide();
                $('.stripe-form').fadeIn('slow');
                $('.stripe-button').addClass('active');
                $('.stripe-button-el').show();
            }
            if ($(this).attr('value') == 'paypal') {
                $('#wp-travel-engine-order-form').attr('action', WP_OBJ.link.paypal_link);
                $('.stripe-button').removeClass('active');
                $('.stripe-button-el').hide();
                $('.paypal-form').fadeIn('slow');
            }
        }
    });

    $('body').on('click', '.check-availability', function (e) {
        e.preventDefault();
        //   if( $('#nestable1').is(':visible') )  {
        //       $('html, body').animate({
        //               scrollTop: $("#nestable1").offset().top
        //            }, 2000);
        //   }
        //   if( $('#nestable1').is(':hidden') )
        //   {
        //       $class = $("#nestable1").parent().parent().parent().attr('class').match(/\d+/);
        //       $('.nb-tab-trigger').removeClass('nav-tab-active');
        //       $('.tab-anchor-wrapper').removeClass('nav-tab-active');
        //       $('.nb-configurations').css('display','none');
        //       $('.nb-' + $class + '-configurations').css('display','block');

        //       $('.nb-tab-trigger[data-configuration=' + $class + ']').addClass('nav-tab-active');
        //       $('.nb-tab-trigger[data-configuration=' + $class + ']').parent().addClass('nav-tab-active');
        //       $('html, body').animate({
        //           scrollTop: $("#nestable1").offset().top
        //        }, 2000);
        //   }
        $('.date-time-wrapper').fadeIn('slow');
    });

    // if( $('#nestable1').length < 1 ){
    //   $('.date-time-wrapper').fadeIn('slow');
    // }

    $('body').on('click', '.check-availability', function (e) {
        e.preventDefault();

        // if( $('#nestable1').length < 1 ){
        $('.wp-travel-engine-price-datetime').focus();
        // }
    });



    $('body').on('click', '.wp-travel-engine-cart', function (e) {
        e.preventDefault();
        trip_id = $(this).attr('data-id');
        nonce = $(this).attr('data-nonce');
        jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url: WTEAjaxData.ajaxurl,
            data: { action: 'wp_add_trip_cart', trip_id: trip_id, nonce: nonce },
            success: function (response) {
                if (response.type === 'already') {
                    $('.wp-cart-message-' + trip_id).css('color', 'orange');
                    $('.wp-cart-message-' + trip_id).html(response.message).fadeIn('slow').delay(3000).fadeOut('slow');
                }
                else if (response.type === 'success') {
                    $('.wp-cart-message-' + trip_id).css('color', 'green');
                    $('.wp-cart-message-' + trip_id).html(response.message).fadeIn('slow').delay(3000).fadeOut('slow');
                }
                else {
                    $('.wp-cart-message-' + trip_id).css('color', 'red');
                    $('.wp-cart-message-' + trip_id).html(response.message).fadeIn('slow').delay(3000).fadeOut('slow');
                }
                if ($('.wte-update-cart-button-wrapper:visible').length < 1) {
                    $('.wte-update-cart-button-wrapper').css('display', 'block');
                }
            }
        });
    });

    $('#price-loading').fadeOut(2000);
    $('.price-holder').fadeIn(2000);

    $('body').on('change', '.travelers-number', function (e) {
        $val = $(this).val();
        $new_val = $(this).parent().parent().siblings('.trip-price-holder').children('.cart-price-holder').text().replace(/,/g, '');
        $total = $val * $new_val;
        $total = addCommas($total);
        $(this).parent().parent().siblings('.cart-trip-total-price').children('.cart-trip-total-price-holder').text($total);
        $sum = 0;
        $('.cart-trip-total-price-holder').each(function (index) {
            $tcost = $(this).text().replace(/,/g, '');
            $sum = parseInt($sum) + parseInt($tcost);
        });
        $sum = addCommas($sum);
        $('.total-trip-price').text($sum);
        $value = 0;
        $val1 = parseInt($('span.travelers-number').text());
        $('input.travelers-number').each(function (index) {
            if ($(this).val() !== '') {
                $value = parseInt($value) + parseInt($(this).val());
            }
        });
        $travelers = parseInt($value) + parseInt($val1);
        $('.total-trip-travelers').text($travelers);
    });

    $('#wp-travel-engine-cart-form').on('submit', function (e) {
        e.preventDefault();
        var data2 = $('#wp-travel-engine-cart-form').serialize();
        var nonce = $('#update_cart_action_nonce').val();
        jQuery.ajax({
            type: 'post',
            url: WTEAjaxData.ajaxurl,
            data: { action: 'wte_update_cart', nonce: nonce, data2: data2 },
            success: function () {
                $('.wte-update-cart-msg').text(WPMSG_OBJ.ajax.success);
                $('.wte-update-cart-msg').css('color', 'green').fadeIn('slow').delay(3000).fadeOut('slow');
            }
        });
    });

    $("#wte_payment_options").on('change', function (e) {
        var val = $('#wte_payment_options :selected').val();
        e.preventDefault();
        if (val == '') {
            return;
        }
        $('#price-loader').fadeIn("slow").delay("3000").fadeOut("3000");
    });
    $('.accordion-tabs-toggle').next().hasClass('show');
    $('.accordion-tabs-toggle').next().removeClass('show');
    $('.accordion-tabs-toggle').next().slideUp(350);
    $(document).on('click', '.faq-row .accordion-tabs-toggle', function () {
        var $this = $(this);
        $this.siblings('.faq-content').toggleClass('show');
        $this.toggleClass('active');
        $this.siblings('.faq-content').slideToggle(350);
        $this.find('.dashicons.dashicons-arrow-down.custom-toggle-tabs').toggleClass('open');
    });
    $(document).on('click', '.expand-all-faq', function (e) {
        e.preventDefault();
        if ($(this).children('svg').hasClass('fa-toggle-off')) {
            $(this).children('svg').toggleClass('fa-toggle-on');
        }
        if ($(this).children('svg').hasClass('fa-toggle-on')) {
            $(this).children('svg').toggleClass('fa-toggle-off');
        }
        $('.faq-row .accordion-tabs-toggle').toggleClass('active');
        $('.faq-row').children('.faq-content').toggleClass('show');
        $('.faq-row').children('.faq-content').slideToggle(350);
        $('.faq-row').find('.dashicons.dashicons-arrow-down.custom-toggle-tabs').toggleClass('open');
    });

    $('form[name="wte_enquiry_contact_form"]').on('submit',function (e) {
        e.preventDefault();
        var isValid = $('#wte_enquiry_contact_form').parsley().isValid();

        if (!isValid) return;

        $('#enquiry_submit_button').prop('disabled', true);
        var redirect = jQuery('#redirect-url').val();
        var EnquiryDetails = new FormData(this);

        $.ajax({
            dataType: 'json',
            type: 'post',
            processData: false,
            contentType: false,
            url: WTEAjaxData.ajaxurl,
            data: EnquiryDetails,
            success: function (response) {
                if (response.type === 'success') {
                    jQuery(".success-msg").html(response.message).fadeIn('slow').delay('3000').fadeOut('3000', function () {
                        window.location.href = redirect;
                    });
                }
                else if (response.type === 'error') {
                    jQuery('#enquiry_email').css('border', '1px solid red');
                    jQuery(".failed-msg").html(response.message).fadeIn('slow').delay('3000').fadeOut('slow', function () {
                        jQuery('#enquiry_email').css('border', '1px solid #d1d1d1');
                        jQuery('#enquiry_submit_button').prop('disabled', false);
                    });
                }
                else {
                    jQuery(".failed-msg").html(response.message).fadeIn('slow').delay('3000').fadeOut('slow', function () {
                        $('#enquiry_submit_button').prop('disabled', false);
                    });
                }
            }
        });
    });

    $('#wp-travel-engine-order-form').on('submit',function (e) {
        var val = $('#wte_payment_options :selected').val();
        //Do not insert Amount field if it is Himalayan Bank Payment Gateway
        if (val != 'Himalayan-Bank') {
            var form_obj = $(this);
            var other_amt = form_obj.find('input[name=amount]').val();
            if (!isNaN(other_amt) && other_amt.length > 0) {
                options_val = other_amt;
                //insert the amount field in the form with the custom amount
                $('<input>').attr({
                    type: 'hidden',
                    id: 'amount',
                    name: 'amount',
                    value: options_val
                }).appendTo(form_obj);
            }
        }
        return;
    });

    $("#wte_payment_options").on('change', function (e) {
        var val = $('#wte_payment_options :selected').val();
        e.preventDefault();
        if (val == '' || val == 'Test Payment') {
            $('#wte-checkout-payment-fields').html('');
            $('#wp-travel-engine-order-form').attr('action', Url.normalurl);
            $('.wp-travel-engine-billing-details-wrapper').html(response.data);
            $('.stripe-button:visible').remove();
            $('.stripe-button-el').remove();
            $('.wp-travel-engine-submit').show();
            $('.wte-authorize-net-wrap').remove();
            return;
        }
        if (val == 'PayPal') {
            jQuery.ajax({
                type: 'post',
                url: WTEAjaxData.ajaxurl,
                data: { action: 'wte_payment_gateway', val: val },
                success: function (response) {
                    if (val == 'PayPal') {
                        $('#wp-travel-engine-order-form').attr('action', Url.paypalurl);

                        $('.wp-travel-engine-billing-details-wrapper').html(response.data);
                        $('#wte-checkout-payment-fields').html(response.data);

                        $('.stripe-button:visible').remove();
                        $('.stripe-button-el').remove();
                        $('.wp-travel-engine-submit').show();
                        $('.wte-authorize-net-wrap').remove();
                    }
                    if (val == 'Test Payment') {
                        $('#wp-travel-engine-order-form').attr('action', Url.normalurl);
                        $('.wp-travel-engine-billing-details-wrapper').html(response.data);
                        $('.stripe-button:visible').remove();
                        $('.stripe-button-el').remove();
                        $('.wp-travel-engine-submit').show();
                        $('.wte-authorize-net-wrap').remove();
                    }
                }
            });
        }
    });

    $("body").on("keyup", "#cost_includes", function (e) {
        $('#include-result').val($('#cost_includes').val());
        $('#include-result').val('<li>' + $('#include-result').val().replace(/\n/g, '</li><li>') + '</li>');
    });

    $("body").on("keyup", "#cost_excludes", function (e) {
        $('#exclude-result').val($('#cost_excludes').val());
        $('#exclude-result').val('<li>' + $('#exclude-result').val().replace(/\n/g, '</li><li>') + '</li>');
    });

    $("body").on("keyup", ".itinerary-content", function (e) {
        $(this).siblings('.itinerary-content-inner').val($(this).val());
        $(this).siblings('.itinerary-content-inner').val('<p>' + $(this).val().replace(/\n/g, '</p><p>') + '</p>');
    });

    $(document).on('click', '.expand-all-itinerary', function (e) {
        e.preventDefault();
        $(this).children('i').toggleClass('fa-toggle-on');
        $('.itinerary-row').children('.itinerary-content').toggleClass('show');
        $('.itinerary-row').children('.itinerary-content').slideToggle(350);
        $('.itinerary-row').find('.dashicons.dashicons-arrow-down.custom-toggle-tabs').toggleClass('rotator');
    });

    $(document).on('click', '.less-no', function (e) {
        $val = $(this).next('input').val();
        if ($val == 0) return;
        $val = parseInt($val) - 1;
        $(this).next('input').val($val);
    });

    $(document).on('click', '.more-no', function (e) {
        $val = $(this).prev('input').val();
        if ($val == '') {
            $val = 1;
            $(this).prev('input').val($val);
            return;
        }
        $val = parseInt($val) + 1;
        $(this).prev('input').val($val);
        return;
    });

    $('#wp-travel-engine-new-checkout-form').parsley();

    if ($('input[name=wp_travel_engine_payment_mode]').length > 0) {

        $(document).on('change', 'input[name=wp_travel_engine_payment_mode]', function () {

            var payment_mode = $('input[name=wp_travel_engine_payment_mode]:checked').val();
            var partial_payment_table = $('.wpte-bf-book-summary .wpte-bf-extra-info-table');

            var amount = 'partial' == payment_mode ? wte.payments.total_partial : wte.payments.total;
            var amount = wteGetFormatedPriceWithCurrencyCodeSymbol(amount);

            'partial' == payment_mode ? partial_payment_table.show() : partial_payment_table.hide();

            $('.wpte-bf-book-summary .wpte-bf-total-price .wpte-price').html(amount);

        });

    }

    $('#wte-send-enquiry-message').on('click',function (e) {
        e.preventDefault();
        let enquirySection = document.getElementById('wte_enquiry_contact_form');
        enquirySection.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    });

    $('#wte_enquiry_contact_form').parsley();
    // Orderby
    $('.wte-ordering').on('change', 'select.orderby', function () {
        $(this).closest('form').submit();
    });

    $(window).on('load', function () {
        if ($(window).width() < 1025) {
            $('.single-trip .wte_enquiry_contact_form-wrap').insertAfter('.single-trip .widget-area .wpte-bf-outer');
        } else {
            $('.single-trip .widget-area .wte_enquiry_contact_form-wrap').insertAfter('.single-trip .site-main');
        }
    });

    // Select2 initialization
    var wteSelec2Selectors = document.querySelectorAll('.wpte-enhanced-select')
    wteSelec2Selectors && wteSelec2Selectors.forEach(function (el) {
        $(el).select2({
            closeOnSelect: false,
            allowClear: false,
        })
    })

});

function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + WPTE_Price_Separator + '$2');
    }
    return x1 + x2;
}

// Trip page view detail section.
(function () {
    function fadeOut(element, cb) {
        var opacity = 1;
        function decrease() {
            opacity -= 0.05;
            if (opacity <= 0) {
                // complete
                element.style.removeProperty('opacity');
                element.style.display = 'none'

                cb()
                return
            }
            element.style.opacity = opacity;
            requestAnimationFrame(decrease);
        }
        decrease();
    }

    var toggleContainers = document.querySelectorAll('.wpte-bf-toggle-wrap')

    function hideTogggleContainer(element, cb) {
        return function eventHandler(event) {
            var contentEl = element.querySelector('.wpte-bf-toggle-content')

            if (element.className.indexOf('wpte-bf-active') > -1) {
                fadeOut(contentEl, function () {
                    document.removeEventListener('click', eventHandler)
                    element.classList.remove('wpte-bf-active')
                    cb()
                })
                return
            }
            element.className.indexOf('wpte-bf-active') < 0 ? element.classList.add('wpte-bf-active') : element.classList.remove('wpte-bf-active')
            cb()
        }
    }

    toggleContainers && toggleContainers.forEach(function (tc) {
        var toggler = tc.querySelector('.wpte-bf-toggle-title')
        var contentEl = tc.querySelector('.wpte-bf-toggle-content')
        var closeBtn = tc.querySelector('.wpte-bf-toggle-close')
        closeBtn && closeBtn.addEventListener('click', function (event) {
            event.preventDefault()
            hideTogggleContainer(tc)
        })
        toggler && toggler.addEventListener('click', function (event) {
            document.addEventListener('click', hideTogggleContainer(tc, function () {
                var span = toggler.querySelector('.wtebf-toggle-title')
                var activeSpan = toggler.querySelector('.wtebf-toggle-title-active')
                if (activeSpan)
                    activeSpan.style.display = tc.className.indexOf('wpte-bf-active') > -1 ? 'block' : 'none'
                if (span)
                    span.style.display = tc.className.indexOf('wpte-bf-active') < 0 ? 'block' : 'none'
            }))
        })
    })
})()
