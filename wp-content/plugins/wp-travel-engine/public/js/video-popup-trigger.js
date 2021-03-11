jQuery(document).ready(function($) {
    $('.wte-trip-vidgal-popup-trigger').on('click', function(event) {
        event.preventDefault();
        var gallery = $(this).data('galtarget');
        $(gallery).magnificPopup({
            delegate: 'a',
            type:'iframe',
            gallery: {
                enabled: true
            }
        }).magnificPopup('open');
    }); 
});
