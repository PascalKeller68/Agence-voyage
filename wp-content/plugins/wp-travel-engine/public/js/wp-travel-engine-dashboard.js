jQuery(document).ready(function($){
    $( 'form.wpte-lrf' ).parsley();

    $( 'a#wpte-show-login-form' ).on('click', function(e) {
        e.preventDefault();
        $( '.wpte-lrf-wrap.wpte-register' ).slideUp('slow');
        $( '.wpte-lrf-wrap.wpte-login' ).slideDown('slow');
    } );

    $( 'a#wpte-show-register-form' ).on('click', function(e) {
        e.preventDefault();
        $( '.wpte-lrf-wrap.wpte-register' ).slideDown('slow');
        $( '.wpte-lrf-wrap.wpte-login' ).slideUp('slow');
    } );
});

jQuery(document).ready(function($) {

    //toggle user logout
    $('.wpte-dashboard .wpte-lrf-userprogile > a').on('click', function(e) {
        e.stopPropagation();
        $(this).parent('.wpte-lrf-userprogile').toggleClass('active');
        $(this).siblings('.lrf-userprofile-popup').stop(true, false, true).slideToggle();
    });

    $('.wpte-dashboard .wpte-lrf-userprogile .lrf-userprofile-popup').on('click', function(e) {
        e.stopPropagation();
    });

    $('body, html').on('click', function() {
        $('.wpte-lrf-userprogile').removeClass('active');
        $('.lrf-userprofile-popup').slideUp();
    });

    //tab js
    $('.wpte-dashboard .wpte-lrf-sidebar .wpte-lrf-tab').on('click', function() {
        var lrfTabClass = $(this).attr('class').split(' ')[1];
        $('.wpte-lrf-tab').removeClass('active');
        $(this).addClass('active');
        $('.wpte-dashboard .wpte-lrf-content-area .wpte-lrf-tab-content').removeClass('active');
        $('.' + lrfTabClass + '-content').addClass('active');
    });

    //toggle change password popup
    $('.lrf-toggle .lrf-toggle-box').on('click', function() {
        $(this).toggleClass('active');
        $(this).parents('.lrf-toggle').siblings('.wpte-lrf-popup').stop(true, false, true).slideToggle();
    });

    //for accessibility
    $(window).on('keyup', function(e) {
        if(e.key == 'Escape') {
            $('.wpte-lrf-userprogile').removeClass('active');
            $('.lrf-userprofile-popup').slideUp();
            $(this).removeClass('active');
        $('.wpte-lrf-popup').slideUp();
        }
    });

    $( '.wte-dbrd-tab' ).on('click', function(e) {
        e.preventDefault();
        var tab_name = $(this).data('tab');
        $( '.wpte-lrf-sidebar .wpte-lrf-tab' ).removeClass('active');
        $( '.wpte-lrf-sidebar .lrf-' + tab_name ).addClass('active');

        $( '.wpte-lrf-main .wpte-lrf-tab-content' ).removeClass('active');
        $( '.wpte-lrf-main .lrf-' + tab_name ).addClass('active');
        
    } )

    $('.wpte-magnific-popup').magnificPopup({
        type:'inline',
        midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });

});//document end
