jQuery(document).ready(function($) {
    $(".disabled_______book-submit").on('click',function(event) {
        event.preventDefault();

        // Validate all input fields.
        var parent = "#" + $(this).data("formid");

        var cart_fields = {};
        $(parent + " input, " + parent + " select").each(function(index) {
            filterby = $(this).attr("name");
            filterby_val = $(this).val();

            if ($(this).data("multiple") == true) {
                if ("undefined" == typeof cart_fields[filterby]) {
                    cart_fields[filterby] = [];
                }
                if ($(this).attr("type") == "checkbox") {
                    if ($(this).is(":checked")) {
                        cart_fields[filterby].push(filterby_val);
                    }
                }
                if ($(this).data("dependent") == true) {
                    var pare = $(this).data("parent");
                    if ($("#" + pare).is(":checked")) {
                        cart_fields[filterby].push(filterby_val);
                    }
                }
            } else {
                cart_fields[filterby] = filterby_val;
            }
        });

        cart_fields["action"] = "wte_add_trip_to_cart";
        cart_fields["tid"] = cart_fields["trip-id"];

        // console.log(cart_fields);

        $.ajax({
            type: "POST",
            url: wp_travel_engine.ajaxurl,
            data: cart_fields,
            beforeSend: function() {
                $("#price-loading").fadeIn(500);
            },
            success: function(data) {
                $("#price-loading").fadeOut(500);
                location.href = wp_travel_engine.CheckoutURL;
            }
        });
    });
});
