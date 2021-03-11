jQuery(function($){
    $( '.wte-preview-enquiry' ).on('click', function(e) {
        e.preventDefault();
        var enquiry_id = $(this).data('enquiryid');
        var data = {
            enquiry_id: enquiry_id,
            action: 'wte_get_enquiry_preview'
        }
        var YO = $(this);
        $.ajax({
            url: ajaxurl,
            data: data,
            type: "post",
            dataType: "json",
            beforeSend: function() {
                YO.removeClass( 'dashicons-welcome-view-site' ).addClass('dashicons-update-alt');
            },
            success: function (data) {
                if ( data.success ) {
                    // Open directly via API
                    $.magnificPopup.open({
                        items: {
                            src: data.data.html,
                            type: 'inline'
                        }
                    });

                    YO.addClass( 'dashicons-welcome-view-site' ).removeClass('dashicons-update-alt');
                }
            }
        });
    });
});
