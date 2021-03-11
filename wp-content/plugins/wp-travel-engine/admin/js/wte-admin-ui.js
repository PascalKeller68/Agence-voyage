/**
 * JS based add_action.
 * @param {Name of the action} action_name
 * @param {Callback function} callback
 * @param {callback priority} priority
 */
function wpte_add_action(action_name, callback, priority) {
    if (!priority) {
        priority = 10;
    }

    if (priority > 100) {
        priority = 100;
    }

    if (priority < 0) {
        priority = 0;
    }
    if (typeof actions == "undefined") {
        actions = {};
    }
    if (typeof actions[action_name] == "undefined") {
        actions[action_name] = [];
    }

    if (typeof actions[action_name][priority] == "undefined") {
        actions[action_name][priority] = [];
    }
    actions[action_name][priority].push(callback);
}

/**
 * JS do_action hook
 */
function wpte_do_action() {
    if (arguments.length == 0) {
        return;
    }
    var args_accepted = Array.prototype.slice.call(arguments),
        action_name = args_accepted.shift(),
        _this = this,
        i,
        ilen,
        j,
        jlen;

    if ("undefined" === typeof actions) {
        actions = {};
    }

    if (typeof actions[action_name] == "undefined") {
        return;
    }

    for (i = 0, ilen = 100; i <= ilen; i++) {
        if (actions[action_name][i]) {
            for (j = 0, jlen = actions[action_name][i].length; j < jlen; j++) {
                var fxn = actions[action_name][i][j];
                window[fxn](args_accepted);
                // fxn.apply( _this, args_accepted );
            }
        }
    }
}
function wpte_core_ui_fxn($) {
    //toggle item js
    $ = jQuery;
    $(".wpte-toggle-item:not(.active) .wpte-toggle-content").hide();
    $(document).on("click", ".wpte-toggle-title a", function () {
        $(this).parents(".wpte-toggle-item").toggleClass("active");
        $(this)
            .parents(".wpte-toggle-title")
            .siblings(".wpte-toggle-content")
            .stop(true, false, true)
            .slideToggle();
    });

    //toggle onoff block popup
    $(document).on(
        "click",
        ".wpte-onoff-block:not(.wpte-floated) .wpte-onoff-toggle",
        function () {
            $(this).toggleClass("active");
            $(this)
                .siblings(".wpte-onoff-popup")
                .stop(true, false, true)
                .slideToggle();
        }
    );

    //toggle inlined onoff block popup
    $(document).on(
        "click",
        ".wpte-onoff-block.wpte-floated .wpte-onoff-toggle",
        function () {
            $(this).toggleClass("active");
            $(this).siblings(".wpte-onoff-popup").fadeToggle();
        }
    );

    //toggle disable class in repeater block
    $(document).on(
        "click",
        ".wpte-settings .wpte-repeater-block .wpte-system-btns .wpte-toggle-btn",
        function () {
            $(this)
                .parents(".wpte-repeater-block")
                .toggleClass("wpte-disabled");
        }
    );

    // Save and continue link action.
    $(document).on("click", ".wpte_save_continue_link", function (e) {
        e.preventDefault();
        // Get Data.
        var parent = ".wpte-tab-content.content_loaded";

        var invalid = false;

        $(
            parent + " input, " + parent + " select, " + parent + " textarea"
        ).each(function (index) {
            $(this).parsley().validate();
            if (!$(this).parsley().isValid()) {
                invalid = true;
                var Trigger = $(this)
                    .parents(".wpte-tab-content")
                    .data("trigger");
                $(".wpte-tab-wrap a." + Trigger).click();
                $(this).focus();
                return false;
            }
        });

        if (invalid) {
            toastr.error(WTE_UI.validation_error);
            return;
        }

        var form_data = {};
        $(
            parent + " input, " + parent + " select, " + parent + " textarea"
        ).each(function (index) {
            filterby = $(this).attr("name");
            filterby_val = $(this).val();
            if ("undefined" == typeof form_data[filterby]) {
                form_data[filterby] = [];
            }
            if (
                $(this).attr("type") == "checkbox" ||
                $(this).attr("type") == "radio"
            ) {
                if ($(this).data("multiple") == true) {
                    if ($(this).is(":checked")) {
                        form_data[filterby].push(filterby_val);
                    }
                } else {
                    if ($(this).is(":checked")) {
                        form_data[filterby] = filterby_val;
                    }
                }
            } else if (
                $(this).is("textarea") &&
                $(this).closest(".tmce-active").size() > 0
            ) {
                id = $(this).attr("id");
                var content = tinymce.get(id).getContent();
                form_data[filterby] = content;
            } else {
                form_data[filterby] = filterby_val;
            }
        });

        form_data["next_tab"] = $(this).data("next-tab");
        form_data["tab"] = $(this).data("tab");
        form_data["action"] = "wpte_tab_trip_save_and_continue";
        form_data["nonce"] = $(this).data("nonce");
        form_data["post_id"] = $(this).data("post-id");

        // Get overview content.
        // if ( 'overview' === form_data.tab ) {
        //     if ( 'undefined' != typeof(tinymce.editors.WTE_Trip_Overview) ) {
        //         form_data['overview_editor_content'] = tinymce.editors.WTE_Trip_Overview.getContent();
        //     }
        // }

        var selector = $('a[data-callback="' + form_data.next_tab + '"]');
        $.ajax({
            url: ajaxurl,
            data: form_data,
            type: "post",
            dataType: "json",
            beforeSend: function (xhr) {
                // $('.wpte-tab-content').css( 'opacity', '0.3' );
                $(".wpte-loading-anim").show();
            },
            success: function (data) {
                // $('.wpte-tab-content').css( 'opacity', '1' );
                $(".wpte-loading-anim").hide();
                if (data.success) {
                    // Navigate to next tab.
                    selector.trigger("click");
                    toastr.success(data.data.message);
                }
            },
        });
    });

    //Global Settings JS.
    $(".wte-global-tabs-holder").sortable();
    $(document).on("click", ".wpte-add-glb-tab", function (e) {
        e.preventDefault();

        var template = wp.template("wpte-glb-tabs-row");

        var index =
            0 < $(".wpte-glb-tab-row").length
                ? $(".wpte-glb-tab-row").length + 1
                : 1;

        jQuery(".wte-global-tabs-holder").append(template({ key: index }));
        ++index;
    });

    $(document).on("click", ".wpte-remove-glb-tab", function (e) {
        e.preventDefault();
        $(this).parents(".wpte-glb-tab-row").remove();
    });

    $(".wpte-glb-trp-infos-holdr").sortable();
    $(document).on("click", ".wpte-add-glb-trp-info", function (e) {
        e.preventDefault();

        var template = wp.template("wpte-add-trip-info-block");

        var index = Math.floor(Math.random() * (+99999999 - +1)) + +1;

        jQuery(".wpte-glb-trp-infos-holdr").append(template({ key: index }));
        ++index;
    });

    $(document).on("click", ".wpte-remove-glb-ti", function (e) {
        e.preventDefault();
        $(this).parents(".wpte-glb-trp-infos-row").remove();
    });

    // Save and continue link action.
    $(document).on("click", ".wpte-save-global-settings", function (e) {
        e.preventDefault();
        // Get Data.
        var parent = ".wpte-global-settngstab.content_loaded";

        var form_data = {};
        $(
            parent + " input, " + parent + " select, " + parent + " textarea"
        ).each(function (index) {
            filterby = $(this).attr("name");
            filterby_val = $(this).val();
            if ("undefined" == typeof form_data[filterby]) {
                form_data[filterby] = [];
            }
            if (
                $(this).attr("type") == "checkbox" ||
                $(this).attr("type") == "radio"
            ) {
                if ($(this).data("multiple") == true) {
                    if ($(this).is(":checked")) {
                        form_data[filterby].push(filterby_val);
                    }
                } else {
                    if ($(this).is(":checked")) {
                        form_data[filterby] = filterby_val;
                    }
                }
            } else if (
                $(this).is("textarea") &&
                $(this).closest(".tmce-active").size() > 0
            ) {
                id = $(this).attr("id");
                var content = tinymce.get(id).getContent();
                form_data[filterby] = content;
            } else if (
                $(this).is("textarea") &&
                $(this).closest(".tmce-active").size() == 0
            ) {
                var content = decodeEntities(filterby_val);
                form_data[filterby] = content;
            } else {
                form_data[filterby] = filterby_val;
            }
        });

        form_data["action"] = "wpte_global_tabs_save_data";
        form_data["nonce"] = $(this).data("nonce");
        form_data["tab"] = $(this).data("tab");

        $.ajax({
            url: ajaxurl,
            data: form_data,
            type: "post",
            dataType: "json",
            beforeSend: function (xhr) {
                // $('.wpte-main-wrap.wpte-settings').css( 'opacity', '0.3' );
                $(".wpte-loading-anim").show();
            },
            success: function (data) {
                // $('.wpte-main-wrap.wpte-settings').css( 'opacity', '1' );
                $(".wpte-loading-anim").hide();
                if (data.success) {
                    toastr.success(data.data.message);
                }
            },
        });
    });
}
/**
 * Scroll to div metabox.
 */
function wpte_trip_edit_scrolltop() {
    var target = jQuery("#trip_pricing_id");
    if (target.length) {
        jQuery("html,body").animate(
            {
                scrollTop: target.offset().top,
            },
            500,
            "swing"
        );
        return false;
    }
}
/**
 * Pricing tab JS.
 */
function wpte_tab_wpte_pricing() {
    jQuery(".wpte-multi-pricing-wrap").sortable();
    jQuery("#wpte-adult-price-pertype-sel").on('change', function (e) {
        e.preventDefault();
        jQuery("#wpte-trip-default-pper").val(jQuery(this).val());
    });
}
/**
 * Overview tab JS.
 */
function wpte_tab_wpte_overview() {
    jQuery(".wte-add-trip-highlight").on("click", function (e) {
        e.preventDefault();

        var index = Math.floor(Math.random() * (+99999999 - +1)) + +1;

        var template = wp.template("tour-highlight-row");

        jQuery(".wpte-trip-highlights-hldr").append(template({ key: index }));
        jQuery(
            'input[name="wp_travel_engine_setting[trip_highlights][' +
            index +
            '][highlight_text]"]'
        ).focus();
        ++index;

        jQuery(".wte-delete-highlight:last").on("click", function (e) {
            e.preventDefault();

            var confirmation = confirm(WTE_UI.suretodel);
            if (!confirmation) {
                return false;
            }

            jQuery(this).parents(".wpte-trp-highlight").remove();
        });
    });

    jQuery(".wte-delete-highlight").on("click", function (e) {
        e.preventDefault();

        var confirmation = confirm(WTE_UI.suretodel);
        if (!confirmation) {
            return false;
        }

        jQuery(this).parents(".wpte-trp-highlight").remove();
    });

    jQuery(".wpte-trip-highlights-hldr").sortable();
}
/**
 * Itinerary tab js.
 */
function wpte_tab_wpte_itinerary() {
    jQuery(".wpte-add-itinerary").on("click", function (e) {
        e.preventDefault();

        // Add itinerary.
        var iti_index =
            0 < jQuery(".wpte-itinerary-repeter").length
                ? jQuery(".wpte-itinerary-repeter").length + 1
                : 1;
        var template = wp.template("wpte-add-iti-row");

        jQuery(".wpte-remove-iti").remove();

        jQuery("#wpte-itinerary-repeter-holder").append(
            template({ key: iti_index })
        );
        ++iti_index;
    });

    jQuery(document).on("click", ".wpte-remove-iti:last", function (e) {
        e.preventDefault();

        var confirmation = confirm(WTE_UI.suretodel);
        if (!confirmation) {
            return false;
        }

        jQuery(this).parents(".wpte-itinerary-repeter").remove();

        jQuery(".wpte-itinerary-repeter:last").append(
            '<button class="wpte-delete wpte-remove-iti"></button>'
        );
    });
}
/**
 * Trip facts JS.
 */
function wpte_tab_wpte_trip_facts() {
    jQuery("body").on("click", ".add-info", function (e) {
        e.preventDefault();
        var val = jQuery("#wte_global_trip_facts").find(":selected").val();
        if (val == "") {
            jQuery("#wte_global_trip_facts").css(
                "-webkit-box-shadow",
                "inset 0px 0px 1px 1px red"
            );
            jQuery("#wte_global_trip_facts").css(
                "-moz-box-shadow",
                "inset 0px 0px 1px 1px red"
            );
            jQuery("#wte_global_trip_facts").css(
                "box-shadow",
                "inset 0px 0px 1px 1px red"
            );
            return;
        } else {
            jQuery("#wte_global_trip_facts").css(
                "-webkit-box-shadow",
                "inset 0px 0px 0px 0px red"
            );
            jQuery("#wte_global_trip_facts").css(
                "-moz-box-shadow",
                "inset 0px 0px 0px 0px red"
            );
            jQuery("#wte_global_trip_facts").css(
                "box-shadow",
                "inset 0px 0px 0px 0px red"
            );
        }
        nonce = jQuery("#wte_global_trip_facts").attr("data-nonce");
        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: { action: "wp_add_trip_info", val: val, nonce: nonce },
            beforeSend: function () { },
            success: function (response) {
                jQuery(".wpte-trip-facts-hldr").append(response);
                jQuery(".wpte-remove-trp-fact:last").on("click", function (e) {
                    e.preventDefault();

                    var confirmation = confirm(WTE_UI.suretodel);
                    if (!confirmation) {
                        return false;
                    }

                    jQuery(this).parents(".wpte-trip-fact-row").remove();
                });
            },
        });
    });
    jQuery(".wpte-remove-trp-fact").on("click", function (e) {
        e.preventDefault();

        var confirmation = confirm(WTE_UI.suretodel);
        if (!confirmation) {
            return false;
        }

        jQuery(this).parents(".wpte-trip-fact-row").remove();
    });

    jQuery(".wpte-trip-facts-hldr").sortable();
}
/**
 * Gallery tab function.
 */
function wpte_tab_wpte_gallery() {
    $ = jQuery;
    var file_frame;
    var allowed_filetype = ["image/jpeg", "image/png"];
    wte_gal_sortable();
    $(document).on("click", ".wpte-add-gallery-img", function (e) {
        e.preventDefault();
        var $this = $(this);
        if (file_frame) file_frame.close();

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data("uploader-title"),
            button: {
                text: $(this).data("uploader-button-text"),
            },
            library: {
                type: allowed_filetype,
            },
            multiple: true,
        });

        var index_max_count = Math.floor(Math.random() * (+99999999 - +1)) + +1;

        file_frame.on("select", function () {
            var selection = file_frame.state().get("selection");

            selection.map(function (attachment, i) {
                var attachment = attachment.toJSON(),
                    index = index_max_count + i;

                // Prepare HTML.
                var file_html_content =
                    '<div class="wpte-gal-img"><input type="hidden" readonly name="wpte_gallery_id[' +
                    index +
                    ']" value="' +
                    attachment.id +
                    '"><img src="' +
                    attachment.sizes.thumbnail.url +
                    '" alt=""><div class="wpte-gal-btns"><button class="wpte-change wpte-change-gal-img"></button><button class="wpte-delete wpte-delete-gal-img"></button></div></div>';

                $(file_html_content).insertBefore("#wpte-gal-img-upldr-btn");
            });
        });
        wte_gal_sortable();
        file_frame.open();
    });

    $(document).on("click", ".wpte-change-gal-img", function (e) {
        e.preventDefault();
        var that = $(this);
        if (file_frame) file_frame.close();
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data("uploader-title"),
            button: {
                text: $(this).data("uploader-button-text"),
            },
            library: {
                type: allowed_filetype,
            },
            multiple: false,
        });
        file_frame.on("select", function () {
            attachment = file_frame.state().get("selection").first().toJSON();

            that.parents(".wpte-gal-img")
                .children('input[type="hidden"]')
                .val(attachment.id);
            that.parents(".wpte-gal-img")
                .children("img")
                .attr("src", attachment.sizes.thumbnail.url);
        });
        file_frame.open();
    });

    /**
     * sortable for file adder field
     */
    function wte_gal_sortable() {
        if ($(".wpte-gallery").length) {
            $(".wpte-gallery").sortable({
                opacity: 0.9,
                revert: true,
            });
        }
    }

    /**
     * Remove file element on file field remove click
     */
    $(document).on("click", ".wpte-delete-gal-img", function (e) {
        e.preventDefault();
        $(this).parents(".wpte-gal-img").remove();
    });

    /**
     * Parse video URL
     * @param {Parse video URL} url
     */
    function parseVideo(url) {
        // - Supported YouTube URL formats:
        //   - http://www.youtube.com/watch?v=My2FRPA3Gf8
        //   - http://youtu.be/My2FRPA3Gf8
        //   - https://youtube.googleapis.com/v/My2FRPA3Gf8
        // - Supported Vimeo URL formats:
        //   - http://vimeo.com/25451551
        //   - http://player.vimeo.com/video/25451551
        // - Also supports relative URLs:
        //   - //player.vimeo.com/video/25451551

        url.match(
            /(http:\/\/|https:\/\/|)(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/
        );
        var type = null;
        if (RegExp.$3.indexOf("youtu") > -1) {
            type = "youtube";
        } else if (RegExp.$3.indexOf("vimeo") > -1) {
            type = "vimeo";
        }

        return {
            type: type,
            id: RegExp.$6,
        };
    }

    function getVideoThumbnail(videoObj) {
        // Obtains the video's thumbnail and passed it back to a callback function.
        if (videoObj.type == "youtube") {
            appendtemplate(
                videoObj,
                "//img.youtube.com/vi/" + videoObj.id + "/hqdefault.jpg"
            );
        } else if (videoObj.type == "vimeo") {
            // Requires jQuery
            $.get(
                "http://vimeo.com/api/v2/video/" + videoObj.id + ".json",
                function (data) {
                    appendtemplate(videoObj, data[0].thumbnail_medium);
                }
            );
        }
    }

    $(".wp-travel-engine-trip-video-gallery-add-video").on('click', function (e) {
        e.preventDefault();
        var video_url = $("input#wte-trip-vid-url").val();

        if ("" == video_url) {
            toastr.error(WTE_UI.novid);
            return;
        }

        var ParsedURL = parseVideo(video_url);
        if (null == ParsedURL.type || "" == ParsedURL.id) {
            toastr.error(WTE_UI.invalid_url);
            return;
        }

        getVideoThumbnail(ParsedURL);
    });

    function appendtemplate(ParsedURL, thumb) {
        var template = wp.template("wpte-trip-videogallery-row");
        var rand = Math.floor(Math.random() * (999 - 10 + 1)) + 10;
        var vidthumb = thumb;

        $(".wp-travel-engine-trip-video-gallery").append(
            template({ index: rand, video_data: ParsedURL, thumb: vidthumb })
        );
        $("input#wte-trip-vid-url").val("");
        $(".wte-video-list-srtable").sortable();
    }

    $(document).on(
        "click",
        ".wp-travel-engine-trip-video-gallery .remove-video",
        function (e) {
            e.preventDefault();
            var confirmation = confirm(WTE_UI.suretodel);
            if (!confirmation) {
                return false;
            }
            $(this).parent("small").parent("li").remove();
        }
    );

    $(".wte-video-list-srtable").sortable();
}
/**
 * Map tab function.
 */
function wpte_tab_wpte_map() {
    $ = jQuery;

    var file_frame;
    var allowed_filetype = ["image/jpeg", "image/png"];

    $(document).on("click", "#wpte-upload-map-img", function (e) {
        e.preventDefault();
        var $this = $(this);
        if (file_frame) file_frame.close();

        file_frame = wp.media.frames.file_frame = wp.media({
            title: "Upload Map image",
            button: {
                text: "Upload image",
            },
            library: {
                type: allowed_filetype,
            },
            multiple: false,
        });

        file_frame.on("select", function () {
            var selection = file_frame.state().get("selection");

            selection.map(function (attachment, i) {
                var attachment = attachment.toJSON();

                $("#map-image-prev-hldr").attr(
                    "src",
                    attachment.sizes.medium.url
                );
                $('input[name="wp_travel_engine_setting[map][image_url]"]').val(
                    attachment.id
                );
                $(".wpte-delete-map-img").show();
            });
        });
        file_frame.open();
    });

    $(document).on("click", ".wpte-delete-map-img", function (e) {
        e.preventDefault();
        var confirmation = confirm(WTE_UI.suretodel);
        var fallback_img = $(this).data("fallback");
        if (!confirmation) {
            return false;
        }
        $("#map-image-prev-hldr").attr("src", fallback_img);
        $('input[name="wp_travel_engine_setting[map][image_url]"]').val("");
        $(this).hide();
    });
}
/**
 * FAQs function.
 */
function wpte_tab_wpte_faq() {
    //faq toggle
    jQuery(document).on(
        "click",
        ".wpte-faq-block .wpte-faq-title",
        function () {
            jQuery(this).parents(".wpte-faq-block").toggleClass("wpte-active");
            jQuery(this)
                .parents(".wpte-faq-title-wrap")
                .siblings(".wpte-faq-content")
                .stop(true, false, true)
                .slideToggle();
        }
    );

    jQuery(document).on("click", ".wpte-add-faq-blck", function (e) {
        e.preventDefault();

        var index =
            0 < jQuery(".wpte-faq-block-row").length
                ? jQuery(".wpte-faq-block-row").length + 1
                : 1;

        var template = wp.template("wpte-faq-block-tmp");

        jQuery(".wpte-faq-block-hldr").append(template({ key: index }));
        ++index;
    });

    jQuery(document).on("click", ".wpte-del-faq", function (e) {
        e.preventDefault();

        var confirmation = confirm(WTE_UI.suretodel);
        if (!confirmation) {
            return false;
        }

        jQuery(this).parents(".wpte-faq-block-row").remove();
    });

    jQuery(".wpte-faq-block-hldr").sortable();
}

(function ($) {
    //main tab js
    function show_admin_tab_content(selector) {
        var wpteUniqueClass = selector.attr("class").split(" ")[1];
        selector.siblings(".wpte-tab").removeClass("current");
        selector.addClass("current");
        selector
            .parents(".wpte-tab-wrap")
            .siblings(".wpte-tab-content-wrap")
            .children(".wpte-tab-content")
            .removeClass("current");

        selector
            .parents(".wpte-tab-wrap")
            .siblings(".wpte-tab-content-wrap")
            .children("." + wpteUniqueClass + "-content")
            .addClass("current content_loaded");

        wpte_trip_edit_scrolltop();
    }

    // Developer JS.
    jQuery(function ($) {
        // Load core UI JS.
        wpte_core_ui_fxn();
        wpte_tab_wpte_overview();

        $(".wpte-tab-nav").on('click', function (e) {
            // $( '.wpte_save_continue_link' ).unbind('click');
            e.preventDefault();
            var selector = $(this);
            if ($(this).hasClass("content_loaded")) {
                show_admin_tab_content(selector);
                return;
            }

            var tab_details = $(this).data("tab-details");
            var content_key = tab_details.content_key;
            var data = {
                action: "wpte_admin_load_tab_content",
                tab_details: tab_details,
                post_id: $(this).data("post-id"),
                next_tab: $(this).data("next-tab"),
            };

            $.ajax({
                url: ajaxurl,
                data: data,
                type: "post",
                dataType: "json",
                beforeSend: function (xhr) {
                    // $('.wpte-tab-main.wpte-vertical-tab').css( 'opacity', '0.3' );
                    $(".wpte-loading-anim").show();
                },
                success: function (data) {
                    // $('.wpte-tab-main.wpte-vertical-tab').css( 'opacity', '1' );
                    $(".wpte-loading-anim").hide();
                    if (data.success) {
                        var content = data.data.html;
                        $(".wpte-tab-content-wrap").append(content);
                        $(".wpte-tab-content-wrap textarea").each(function () {
                            $(this).val(decodeEntities($(this).val()));
                        });

                        show_admin_tab_content(selector);
                        wpte_do_action(
                            "wpte_after_admin_tab_shown",
                            content_key
                        );
                        switch (content_key) {
                            case "wpte-pricing":
                                wpte_tab_wpte_pricing();
                                break;
                            case "wpte-overview":
                                wpte_tab_wpte_overview();
                                break;
                            case "wpte-itinerary":
                                wpte_tab_wpte_itinerary();
                                break;
                            case "wpte-facts":
                                wpte_tab_wpte_trip_facts();
                                break;
                            case "wpte-gallery":
                                wpte_tab_wpte_gallery();
                                break;
                            case "wpte-map":
                                wpte_tab_wpte_map();
                                break;
                            case "wpte-faqs":
                                wpte_tab_wpte_faq();
                                break;
                        }
                        selector.addClass("content_loaded");
                    }
                    // wpte_core_ui_fxn();
                },
            });
        });

        $(document).on("click", ".wpte_load_global_settings_tab", function (e) {
            e.preventDefault();

            var selector = $(this);
            if ($(this).hasClass("content_loaded")) {
                show_admin_tab_content(selector);
                return;
            }

            var tab_details = $(this).data("tab-data");
            var content_key = $(this).data("content-key");
            var data = {
                action: "wpte_global_settings_load_tab_content",
                tab_details: tab_details,
                content_key: content_key,
            };

            $.ajax({
                url: ajaxurl,
                data: data,
                type: "post",
                dataType: "json",
                beforeSend: function (xhr) {
                    // $('.wpte-main-wrap.wpte-settings').css( 'opacity', '0.3' );
                    $(".wpte-loading-anim").show();
                },
                success: function (data) {
                    // $('.wpte-main-wrap.wpte-settings').css( 'opacity', '1' );
                    $(".wpte-loading-anim").hide();
                    if (data.success) {
                        var content = data.data.html;
                        $(".wpte-global-settings-tbswrp").append(content);
                        show_admin_tab_content(selector);
                        wpte_do_action(
                            "wpte_after_global_settings_tab_shown",
                            content_key
                        );
                        switch (content_key) {
                            case "wpte-miscellaneous":
                                $("select.wpte-enhanced-select").select2();
                                break;
                            case "wpte-extensions":
                                $("select.wpte-enhanced-select").select2();
                                break;
                            case "wpte-payment":
                                $("select.wpte-enhanced-select").select2();
                                break;
                        }
                        selector.addClass("content_loaded");
                        document.dispatchEvent(new CustomEvent('wteSettingsTabContentLoaded', { detail: { contentKey: content_key } }))
                    }

                },
            });
        });

        //Bind price
        $(document).on("change keyup", "*[bind]", function (e) {
            var to_bind = $(this).attr("bind");
            var value = "" != $(this).val() ? $(this).val() : "";
            $("*[bind='" + to_bind + "']").val(value);
        });

        $(document).on("change keyup", "*[bindSale]", function (e) {
            var to_bind = $(this).attr("bindSale");
            var value = "" != $(this).val() ? $(this).val() : "";
            $("*[bindSale='" + to_bind + "']").val(value);
        });

        $(document).on(
            "change",
            'input[name="wp_travel_engine_setting[multiple_pricing][adult][enable_sale]"]',
            function (e) {
                $("#wpte-trip-enb-sale-price").prop("checked", this.checked);
            }
        );

        $(document).on("click", ".wpte-copy-btn", function (e) {
            e.preventDefault();
            var ID = $(this).data("copyid");
            var copyText = document.getElementById(ID);
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            toastr.success(WTE_UI.copied);
        });

        /**
         * Display the font awesome icon list.
         */
        $(document).on("click", ".wpte-add-icon", function (e) {
            e.preventDefault();
            // $('.wpte-ico-search').hide();
            $(".wpte-font-awesome-list").hide();
            if ($(this).siblings(".wpte-font-awesome-list").length < 1) {
                var $iconlist = $(
                    ".wp-travel-engine-font-awesome-list-template"
                ).clone();
                $(this).after($iconlist.html());
                $(this).siblings(".wpte-font-awesome-list").fadeIn("slow");
            } else {
                $(this).siblings(".wpte-ico-search").remove();
                $(this).siblings(".wpte-font-awesome-list").remove();
            }
        });

        /**
         * Select icon from the font awesome icon list.
         */
        $(document).on("click", ".wpte-font-awesome-list li", function (event) {
            event.preventDefault();
            var svg_data = $(this).children("svg");
            var prefix = $(this).children("svg").attr("data-prefix");
            var icon = $(this).children("svg").attr("data-icon");
            var val = prefix + " fa-" + icon;
            // var val = $(this).children().attr('class');
            $(this)
                .parent()
                .parent()
                .siblings(".trip-tabs-icon")
                .attr("value", val);
            $(this)
                .parent()
                .parent()
                .siblings(".trip-tabs-icon")
                .siblings("span.wpte-icon-preview")
                .children(".wpte-icon-holdr")
                .html(svg_data);
            $(this)
                .parent()
                .parent()
                .fadeOut("slow", function () { });
            $(this)
                .parent()
                .parent()
                .siblings(".trip-tabs-icon")
                .siblings(".wpte-font-awesome-list")
                .remove();
            $(this)
                .parents(".wpte-icons-holder")
                .find(".wpte-ico-search")
                .remove();
        });

        $(document).on("click", ".wpte-remove-icn-btn", function (e) {
            e.preventDefault();
            $(this).siblings(".wpte-icon-holdr").html("");
            $(this)
                .parents(".wpte-icon-preview")
                .siblings(".trip-tabs-icon")
                .val("");
        });

        $(document).on("keyup", ".wpte-ico-search", function () {
            var value = $(this).val();
            var matcher = new RegExp(value, "gi");
            $(this)
                .parent(".wpte-font-awesome-list")
                .find("li")
                .show()
                .not(function () {
                    return matcher.test($(this).find("svg").attr("data-icon"));
                })
                .hide();
        });

        $(".wpte-enhanced-select").select2({
            allowClear: true,
            closeOnSelect: false,
        });

        $(".wp-travel-engine-datetime").datepicker({ dateFormat: "yy-mm-dd" });

        $(document).on("click", ".wpte-edit-bkng", function (e) {
            e.preventDefault();
            $(this)
                .parents(".wpte-block")
                .find(".wpte-block-content")
                .fadeOut("slow")
                .css({
                    height: 0,
                    "padding-top": 0,
                    "padding-bottom": 0,
                    overflow: "hidden",
                });
            $(this)
                .parents(".wpte-block")
                .find(".wpte-block-content-edit")
                .fadeIn("slow")
                .css("height", "auto");
        });

        $(document).on("click", ".wpte-edit-prsnl-details", function (e) {
            e.preventDefault();
            $(this)
                .parents(".wpte-prsnl-dtl-blk")
                .find(".wpte-prsnl-dtl-blk-content")
                .fadeOut("slow")
                .css({ height: 0, "margin-top": 0, overflow: "hidden" });
            $(this)
                .parents(".wpte-prsnl-dtl-blk")
                .find(".wpte-prsnl-dtl-blk-content-edit")
                .fadeIn("slow")
                .css("height", "auto");
        });

        $(".wpte-glb-trp-infos-row").each(function () {
            // if ($(this).is(':visible')) {
            if ($(this).find("select option:selected").val() == "select") {
                $(this).find(".select-options").show();
                $(this).find(".input-placeholder").hide();
            } else {
                $(this).find(".select-options").hide();
                $(this).find(".input-placeholder").show();
            }
            // }
        });

        $("body").on("change", ".wpte-trp-inf-fieldtyp", function (e) {
            if ($(this).find("select option:selected").val() == "select") {
                $(this)
                    .siblings(".wpte-field")
                    .find(".select-options")
                    .fadeIn("slow");
            } else {
                $(this).siblings(".wpte-field").find(".select-options").hide();
            }
            if (
                $(this).find("select option:selected").val() == "text" ||
                $(this).find("select option:selected").val() == "number" ||
                $(this).find("select option:selected").val() == "textarea"
            ) {
                $(this)
                    .siblings(".wpte-field")
                    .find(".input-placeholder")
                    .fadeIn("slow");
            } else {
                $(this)
                    .siblings(".wpte-field")
                    .find(".input-placeholder")
                    .hide();
            }
        });
    });

    jQuery(function ($) {
        $(document).on("click", ".wpte-rich-textarea", function (e) {
            var current_item = $(this);
            var this_id = $(this)
                .find("textarea.wte-editor-area")
                .attr("id");
            if (current_item.hasClass("delay")) {
                current_item.find(".wte-editor-notice").remove();
                current_item.removeClass("delay");
                wte_init_editor(this_id);
            }
        });

        toastr.options.positionClass = "toast-bottom-full-width";

        $(document).on(
            "click",
            ".wp-travel-engine-featured-trip",
            function (e) {
                e.preventDefault();
                var featuredIcon = $(this);
                var post_id = $(this).attr("data-post-id");
                var nonce = $(this).attr("data-nonce");
                var data = {
                    action: "wp_travel_engine_featured_trip",
                    post_id: post_id,
                    nonce: nonce,
                };
                $.ajax({
                    url: ajaxurl,
                    data: data,
                    type: "post",
                    dataType: "json",
                    success: function (data) {
                        if (data != "invalid") {
                            featuredIcon
                                .removeClass("dashicons-star-filled")
                                .removeClass("dashicons-star-empty");
                            if (data.new_status == "yes") {
                                featuredIcon.addClass(
                                    "dashicons-star-filled"
                                );
                            } else {
                                featuredIcon.addClass(
                                    "dashicons-star-empty"
                                );
                            }
                        }
                    },
                });
            }
        );

        $(document).on(
            "click",
            ".wp-travel-engine-featured-term",
            function (e) {
                e.preventDefault();
                var featuredIcon = $(this);
                var post_id = $(this).attr("data-term-id");
                var nonce = $(this).attr("data-nonce");
                var data = {
                    action: "wp_travel_engine_featured_term",
                    post_id: post_id,
                    nonce: nonce,
                };
                $.ajax({
                    url: ajaxurl,
                    data: data,
                    type: "post",
                    dataType: "json",
                    success: function (data) {
                        if (data != "invalid") {
                            featuredIcon
                                .removeClass("dashicons-star-filled")
                                .removeClass("dashicons-star-empty");
                            if (data.new_status == "yes") {
                                featuredIcon.addClass(
                                    "dashicons-star-filled"
                                );
                            } else {
                                featuredIcon.addClass(
                                    "dashicons-star-empty"
                                );
                            }
                        }
                    },
                });
            }
        );

        //main tab js
        $(document).on("click", ".wpte-tab-wrap .wpte-tab", function () {
            var wpteUniqueClass = $(this).attr("class").split(" ")[1];
            $(this).siblings(".wpte-tab").removeClass("current");
            $(this).addClass("current");
            $(this)
                .parents(".wpte-tab-wrap")
                .siblings(".wpte-tab-content-wrap")
                .children(".wpte-tab-content")
                .removeClass("current");
            $(this)
                .parents(".wpte-tab-wrap")
                .siblings(".wpte-tab-content-wrap")
                .children("." + wpteUniqueClass + "-content")
                .addClass("current content_loaded");
        });
        function wte_init_editor(this_id) {
            wp.editor.initialize(this_id, {
                tinymce: {
                    wpautop: true,
                    plugins:
                        "charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview",
                    toolbar1:
                        "bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv",
                    toolbar2:
                        "formatselect alignjustify forecolor | pastetext removeformat charmap | outdent indent | undo redo | wp_help",
                },
                quicktags: true,
                mediaButtons: true,
            });
        }
    })
})(jQuery);

function decodeEntities(encodedString) {
    if (encodedString == null || encodedString == "") {
        return;
    }
    var textArea = document.createElement("textarea");
    var newencodedString = encodedString.replace(/&amp;/g, "&");
    textArea.innerHTML = newencodedString;
    return textArea.value;
}


(function () {
    var bookingDetailsEditBtns = document.querySelectorAll('.wpte-edit-booking-detail')
    bookingDetailsEditBtns && bookingDetailsEditBtns.forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault()
            e.target.closest('.wpte-block')
                .querySelectorAll('select, input')
                .forEach(function (input) {
                    if (input.dataset.attribName) input.name = input.dataset.attribName
                    if (input.dataset.attribValue) input.value = input.dataset.attribValue
                    if (input.dataset.attribType) input.type = input.dataset.attribType
                    if (input.disabled) input.disabled = false
                    if (input.type) input.readOnly = false
                })
        })
    })

    document.addEventListener('DOMContentLoaded', (event) => {
        var wteCodeBlocks = document.querySelectorAll('.wte-code');
        wteCodeBlocks && wteCodeBlocks.forEach(function (cb) {
            if (cb.dataset.height) cb.style.height = cb.dataset.height + 'px'
            hljs && hljs.highlightBlock(cb);
        })

        // Toggler
        var clickTogglers = document.querySelectorAll('.wte-onclick-toggler');
        clickTogglers && clickTogglers.forEach(function (ct) {
            ct.addEventListener('click', function (e) {
                e.preventDefault();
                if (e.target.dataset.target) {
                    var target = document.querySelector(e.target.dataset.target)
                    if (target) {
                        if (Array.from(target.classList).includes('visible')) {
                            target.classList.remove('visible')
                            target.style.display = 'none'
                        } else {
                            target.style.removeProperty('display')
                            target.classList.add('visible')
                        }
                    }
                }
            })
        })
    })

    function initUpdateNotifier(e){
        var suffix = WTE_UI.version
        var updateEl = document.querySelectorAll(`[class*=_wte_update_notice_], [data-wte-update]`);
        console.info(updateEl)
        if (updateEl) {
            for (var index in updateEl) {
                var el = updateEl[index]
    
                if (!el.classList) continue;
                if (el.classList.contains(`_wte_update_notice_${suffix}`)) {
                    el.classList.remove(`_wte_update_notice_${suffix}`)
                    continue;
                }
    
                var regexr = new RegExp(`^(wte_(new|updated|note)_${suffix})$`)
                if (!el.dataset.wteUpdate.match(regexr) ) {
                    continue;
                }
    
                var classToAdd = el.dataset.wteUpdate
    
                el.classList.add(classToAdd)
                var i = document.createElement('b');
                i.classList.add('_wte_notify')
                el.appendChild(i)
                el.removeAttribute('data-wte-update')
            }
        }
    }

    document.addEventListener('wteSettingsTabContentLoaded', initUpdateNotifier)


    // TODO: Remove from here
    document.addEventListener('click', function(e){
        if( e.target.classList.contains('wte-email-template-updater') ) {
            e.preventDefault();
            var targetForm = document.getElementById( e.target.dataset.target )
            targetForm && targetForm.submit();
        }
    })

    initUpdateNotifier()


})()
