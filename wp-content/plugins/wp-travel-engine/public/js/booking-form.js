"use strict";
jQuery(document).ready(function ($) {
    var currentTab = $(".wpte-bf-step.active");
    var currentTabContent = $(".wpte-bf-step-content.active");
    var isDateSelected = false;

    window.wteCartFields = {
        action: "wte_add_trip_to_cart",
        nonce: $("#nonce").val(),
        "trip-id": wte.trip.id,
        travelers: 1,
        "trip-cost": wte.trip.price,
    };

    // populateHTML();

    toastr.options.positionClass = "toast-bottom-full-width";
    toastr.options.timeOut = "10000";

    function applyFixStratingDatePrice(date, cost) {
        if ("" == window.wte_fix_date.enabled) {
            return cost;
        }

        try {
            var costCount = wte_fix_date.cost.length;
            for (var i = 0; i < costCount; i++) {
                if (wte_fix_date.use_multi_prices) {
                    return cost;
                }

                var dateKey = date.split('-').map(function (d) {
                    return +d;
                }).join('-')

                var tempCost = wte_fix_date.cost[i][dateKey];
                if (undefined !== tempCost) {
                    return tempCost;
                }
            }
        } catch (err) {
            return cost;
        }

        return cost;
    }

    // Toggle detail.
    $(".wpte-bf-toggle-wrap .wpte-bf-toggle-title").on('click',function (event) {
        event.preventDefault();
        $(this).parents(".wpte-bf-toggle-wrap").toggleClass("active");
        $(this)
            .siblings(".wpte-bf-toggle-content")
            .fadeToggle("slow", function () {
                if ($(this).is(":visible")) {
                    $(this)
                        .siblings(".wpte-bf-toggle-title")
                        .find(".wtebf-toggle-title")
                        .hide();
                    $(this)
                        .siblings(".wpte-bf-toggle-title")
                        .find(".wtebf-toggle-title-active")
                        .show();
                } else {
                    $(this)
                        .siblings(".wpte-bf-toggle-title")
                        .find(".wtebf-toggle-title")
                        .show();
                    $(this)
                        .siblings(".wpte-bf-toggle-title")
                        .find(".wtebf-toggle-title-active")
                        .hide();
                }
            });
    });

    /**
     * Handle increment and decrement of travellers in the travellers section.
     */
    jQuery(".wpte-bf-content-travellers .wpte-bf-number-field").each(
        function () {
            var spinner = jQuery(this),
                input = spinner.find('input[type="text"]'),
                btnUp = spinner.find(".wpte-bf-plus"),
                btnDown = spinner.find(".wpte-bf-minus"),
                min = input.attr("min"),
                max = input.attr("max");

            btnUp.on('click',function (event) {
                event.preventDefault();
                var input = $(this).parent().find("input");
                var max = $(input).attr("max");

                var value = parseFloat(input.val());
                ++value;

                if (value >= max) {
                    value = max;
                }

                spinner.find("input").val(value);
                spinner.find("input").trigger("change");

                // Get traveller type.
                var type = $(this)
                    .parents(".wpte-bf-number-field")
                    .find('input[type="text"]')
                    .data("cartField");

                // Add data to the cart fields.
                window.wteCartFields[type] = value;

                populateHTML();
            });

            btnDown.on('click',function (event) {
                event.preventDefault();

                var input = $(this).parent().find("input");
                var min = $(input).attr("min");

                var value = parseFloat(input.val());
                --value;

                if (value <= min) {
                    value = min;
                }

                spinner.find("input").val(value);
                spinner.find("input").trigger("change");

                // Get traveller type.
                var type = $(this)
                    .parents(".wpte-bf-number-field")
                    .find('input[type="text"]')
                    .data("cartField");

                // Add data to the cart fields.
                window.wteCartFields[type] = value;

                populateHTML();
            });
        }
    );

    /**
     * Populate the html fields.
     */

    function populateHTML() {
        if (!$("body").hasClass("single-trip")) {
            return;
        }

        // Calculate total.
        var travellersCost = calculateTravellersTotalCost();
        window.wte.trip.travellersCost = travellersCost;
        var grandTotal = calculateGrandTotal();
        var formattedTravellersCost = wteGetFormatedPriceWithCurrencyCodeSymbol(
            travellersCost,
            wte.currency.code,
            wte.currency.symbol
        );
        var formattedGrandTotal = wteGetFormatedPrice(grandTotal);
        var formattedGrandTotalWithCodeSymbol = wteGetFormatedPriceWithCurrencyCodeSymbol(
            grandTotal,
            wte.currency.code,
            wte.currency.symbol
        );

        var tripPrice = wteGetFormatedPriceWithCurrencyCodeSymbol(
            wte.trip.price,
            wte.currency.code
        );

        // Add data to the cart fields.
        window.wteCartFields["trip-cost"] = grandTotal;

        // Update the total cost.
        $(".wpte-bf-total-price > .wpte-price").html(formattedGrandTotal);

        var numberFields = $(
            '.wpte-bf-content-travellers .wpte-bf-number-field > input[type="text"]'
        );
        var html = "";
        $.each(numberFields, function (index, numberField) {
            var count = $(numberField).val();
            var cartField = $(numberField).data("cartField");
            var type = $(numberField).data("type");
            var costField = $(numberField).data("costField");
            var cost = calculateSingleTravellerTypeCost(numberField);
            var formattedCost = wteGetFormatedPriceWithCurrencyCodeSymbol(
                cost,
                wte.currency.code,
                wte.currency.symbol
            );
            var pricing_type =
                $(numberField).data("pricing-type") || "per-person";

            window.wteCartFields[cartField] = count;
            window.wteCartFields[costField] = cost;

            if (
                count > 1 &&
                ("group" == type || "per-group" === pricing_type)
            ) {
                count = 1;
            }

            if ($.inArray(type, Object.keys(wte.pax_labels)) !== -1) {
                type = wte.pax_labels[type];
            }

            var capitalizeType =
                type.charAt(0).toUpperCase() + type.substring(1);

            if (count > 1 && "traveler" == type) {
                capitalizeType = "Travelers";
            } else if (count > 1 && "child" == type) {
                capitalizeType = "Children";
            } else if ((count > 1 && "Adult" == type) || "adult" == type) {
                capitalizeType = "Adults";
            } else if (
                count > 1 &&
                ("group" == type || "per-group" === pricing_type)
            ) {
                count = 1;
            }

            // Calculate new price from the cost.
            var price = cost / count;
            if (!isFinite(price)) {
                price = $(numberField).data("cost");
                if ("" == price) price = 0;
                try {
                    price = applyFixStratingDatePrice(
                        window.wteCartFields["trip-date"],
                        price
                    );
                } catch (err) { }
            }
            price = parseFloat(price);
            price = price.toFixed(2);
            var formattedPriceWithSymbol = wteGetFormatedPriceWithCurrencySymbol(
                price,
                wte.currency.symbol
            );
            var formattedPrice = wteGetFormatedPrice(price);
            var priceHtml = wteGetFormatedPriceWithCurrencyCodeSymbol(
                price,
                wte.currency.code,
                wte.currency.symbol
            );
            jQuery(this)
                .parents(".wpte-bf-traveler-block")
                .find(".wpte-bf-price ins")
                .html(priceHtml);

            if (0 == cost) {
                return;
            }

            html =
                html +
                `
                <tr>
                    <td>${count} x ${capitalizeType} <span class="wpte-bf-info">(${formattedPriceWithSymbol}/${type})<span></span></td>
                    <td>${formattedCost}</td>
                </tr>
            `;
        });

        $(".wpte-bf-travellers-price-table tbody").html(html);

        // Update the grand total.
        $(".wte-bf-price-detail .wpte-bf-total").html(`
            ${wte.totaltxt} <b>${formattedGrandTotalWithCodeSymbol}</b>
        `);
    }

    /**
     * Calculate travellers total cost.
     */
    function calculateTravellersTotalCost() {
        // Get all the number fields.
        var numberFields = $(
            '.wpte-bf-content-travellers .wpte-bf-number-field > input[type="text"]'
        );
        var total = 0.0;

        // Calculate total.
        $.each(numberFields, function (index, numberField) {
            var cost = calculateSingleTravellerTypeCost(numberField);
            total = total + cost;
        });
        return total;
    }

    function calculateSingleTravellerTypeCost(numberField) {
        var count = $(numberField).val();
        var price = $(numberField).data("cost");

        if (isNaN(price) || "" == price) {
            price = 0;
        }

        try {
            price = parseFloat(
                applyFixStratingDatePrice(
                    window.wteCartFields["trip-date"],
                    price
                )
            );
        } catch (err) { }

        var type = $(numberField).data("type");
        var pricing_type = $(numberField).data("pricing-type") || "per-person";

        if (("group" == type || "per-group" == pricing_type) && count > 0) {
            cost = parseFloat(price);
        } else {
            var cost = parseInt(count) * parseFloat(price);
        }

        try {
            cost = parseFloat(applyGroupDiscount(count, type, cost));
        } catch (err) { }

        return cost;
    }

    var availableDates = [];
    try {
        var availableDatesCount = wte_fix_date.cost.length;
        for (var i = 0; i < availableDatesCount; i++) {
            availableDates.push(Object.keys(wte_fix_date.cost[i])[0]);
        }
    } catch (err) { }

    function ApplyCutOffDays(date) {
        if (
            wte.booking_cutoff.enable &&
            parseInt(wte.booking_cutoff.cutoff) > 0
        ) {
            var today = new Date();

            var calendarDate = new Date(date.getTime() + 24 * 60 * 60 * 1000)
            var cutoffTime = parseInt(wte.booking_cutoff.cutoff) * 60 * 60 * 1000;
            cutoffTime = wte.booking_cutoff.unit === 'days' ? cutoffTime * 24 : cutoffTime;
            var bookableDate = new Date(today.getTime() + cutoffTime);

            if (bookableDate.getTime() > calendarDate.getTime()) {
                return [false, "", "Unavailable"];
            }
        }

        return [true, "", "Available"];
    }

    function convertToUTC(date) {
        return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0))
    }

    var allrruleDates = {}
    function getDates(date) {
        if (
            wte.booking_cutoff.enable &&
            parseInt(wte.booking_cutoff.cutoff) > 0
        ) {
            var today = convertToUTC(new Date());
            var cutoffTime = parseInt(wte.booking_cutoff.cutoff) * 60 * 60 * 1000;
            cutoffTime = wte.booking_cutoff.unit === 'days' ? cutoffTime * 24 : cutoffTime;
            bookableDate = new Date(today.getTime() + cutoffTime);
            if (convertToUTC(date).getTime() < convertToUTC(bookableDate).getTime()) {
                return [];
            }
        }
        if (!wte_fix_date.departureDates) {
            return availableDates.map(function (ad) {
                return convertToUTC(new Date(ad))
            });
        }

        var departureDates = wte_fix_date.departureDates;
        var indexes = departureDates.sdate ? Object.keys(departureDates.sdate) : [];
        var rruleset = new rrule.RRuleSet();
        indexes.forEach(function (i) {
            var _rruleset = new rrule.RRuleSet();
            var recurring = typeof departureDates !== 'undefined' && typeof departureDates[i] !== 'undefined' ? departureDates[i].recurring : null;
            if (!!recurring && recurring.enable == 'true') {
                var rule = {}
                var until = new Date(recurring.until);
                if (new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0)).getTime() > new Date(Date.UTC(until.getFullYear(), until.getMonth(), until.getDate(), 0, 0, 0, 0)).getTime()) {
                    return;
                }
                rule.freq = rrule.RRule[recurring.type];
                var startDate = convertToUTC(new Date(departureDates['sdate'][i]));
                rule.dtstart = startDate;

                if (recurring.months && recurring.type == 'MONTHLY') {
                    rule['bymonth'] = Object.values(recurring.months).map(function (m) {
                        return +m;
                    })
                    rule.dtstart.setDate(startDate.getDate())
                }
                if (recurring.week_days && recurring.type == 'WEEKLY') {
                    rule['byweekday'] = Object.values(recurring.week_days).map(function (wd) {
                        return rrule.RRule[wd]
                    })
                }
                rule.count = +recurring.limit || 10;

                rruleset.rrule(new rrule.RRule(rule))
                _rruleset.rrule(new rrule.RRule(rule))

                _rruleset.all().forEach(function (rs) {
                    // wte_fix_date['cost'] = []
                    if (allrruleDates[rs.getTime()]) return
                    allrruleDates[rs.getTime()] = true
                    var dateStr = rs.getFullYear() + '-' + (+rs.getMonth() + 1) + '-' + rs.getDate();
                    wte_fix_date.cost.push({ [dateStr]: departureDates.cost[i] })
                    var bookedSeats = wte_fix_date.bookedSeats[rs.getTime()] ? wte_fix_date.bookedSeats[rs.getTime()].booked || 0 : 0
                    var seatsLeft = +departureDates.seats_available[i] - +bookedSeats
                    // wte_fix_date.
                    wte_fix_date.seats_available.push({ [dateStr]: seatsLeft })
                    wte_fix_date.fdd_ids.push({ [dateStr]: rs.getTime() })
                })

            } else {
                var rs = new Date(departureDates.sdate[i]);

                if (
                    wte.booking_cutoff.enable &&
                    parseInt(wte.booking_cutoff.cutoff) > 0
                ) {
                    var today = new Date();
                    today = new Date(Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0, 0));
                    var cutoffTime = parseInt(wte.booking_cutoff.cutoff) * 60 * 60 * 1000;
                    cutoffTime = wte.booking_cutoff.unit === 'days' ? cutoffTime * 24 : cutoffTime;
                    bookableDate = new Date(today.getTime() + cutoffTime);
                    if (convertToUTC(rs).getTime() > convertToUTC(bookableDate).getTime()) {
                        rruleset.rdate(rs)
                    }
                } else {
                    if (convertToUTC(rs).getTime() > convertToUTC(convertToUTC(new Date())).getTime()) {
                        rruleset.rdate(rs)
                    }
                }
                if (allrruleDates[rs.getTime()]) return

                allrruleDates[rs.getTime()] = true
                var dateStr = rs.getFullYear() + '-' + (+rs.getMonth() + 1) + '-' + rs.getDate();
                wte_fix_date.cost.push({ [dateStr]: departureDates.cost[i] })
                var bookedSeats = wte_fix_date.bookedSeats[rs.getTime()] ? wte_fix_date.bookedSeats[rs.getTime()].booked || 0 : 0
                var seatsLeft = +departureDates.seats_available[i] - +bookedSeats
                // wte_fix_date.
                wte_fix_date.seats_available.push({ [dateStr]: seatsLeft })
                wte_fix_date.fdd_ids.push({ [dateStr]: rs.getTime() })
            }
        })
        return rruleset.all()
    }

    function checkAvailableDates(date) {
        var dmy = $.datepicker.formatDate($.datepicker.ISO_8601, date);

        var today = new Date();
        today = new Date(Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0, 0));
        var bookableDate = null;
        if (
            wte.booking_cutoff.enable &&
            parseInt(wte.booking_cutoff.cutoff) > 0
        ) {
            var cutoffTime = parseInt(wte.booking_cutoff.cutoff) * 60 * 60 * 1000;
            cutoffTime = wte.booking_cutoff.unit === 'days' ? cutoffTime * 24 : cutoffTime;
            bookableDate = new Date(today.getTime() + cutoffTime);
            bookableDate = new Date(Date.UTC(bookableDate.getFullYear(), bookableDate.getMonth(), bookableDate.getDate(), 0, 0, 0, 0))
        } else {
            bookableDate = new Date(Date.UTC(today.getFullYear(), today.getMonth(), today.getDate(), 0, 0, 0, 0))
        }

        if (!wte_fix_date || (wte_fix_date.departureDates && !wte_fix_date.departureDates.cost)) {
            if (convertToUTC(date) >= bookableDate) {
                return [true, "", "Available"];
            } else {
                return [false, "", "Unavailable"];
            }
        }

        var validDates = getDates(date).filter(function (vd) {
            return convertToUTC(vd) >= bookableDate;
        }).map(function (d) {
            return d.getTime();
        })

        if (validDates.length <= 0 && convertToUTC(date) >= bookableDate) {
            return [true, "", "Available"];
        }

        var fixDatesCount = wte_fix_date.seats_available.length;
        for (var index = 0; index < fixDatesCount; index++) {
            if (
                wte_fix_date.seats_available[index][dmy] == "0" ||
                "" == wte_fix_date.seats_available[index][dmy]
            ) {
                return [false, "", "Unavailable"];
            }
        }

        $recurredDate = validDates.find(function (d) {
            return new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0, 0)).getTime() === d
        });
        if ($recurredDate) {
            return [true, "", "Available"];
        } else {
            return [false, "", "Unavailable"];
        }

    }

    /**
     * Change to the next tab afeter selecting the date.
     */
    $(".wpte-bf-datepicker").datepicker({
        minDate: 0,
        beforeShowDay: (typeof wte_fix_date != 'undefined' && !!wte_fix_date.departureDates && wte_fix_date.enabled == '1') ? checkAvailableDates : ((0 == availableDates.length || (typeof wte_fix_date != 'undefined' && '' == window.wte_fix_date.enabled))
            ? ApplyCutOffDays
            : checkAvailableDates),
        // beforeShowDay: checkAvailableDates,
        dateFormat: "yy-mm-dd",
        onSelect: function (dateText, inst) {
            isDateSelected = true;

            // Get the next tab.
            var nextTab = getNextTab();
            if (nextTab) {
                // Deactive the current tab.
                $(".wpte-bf-step").removeClass("active");
                $(currentTab).removeClass("active");

                changeTab(nextTab);
            }

            if (window.wteCartFields["trip-date"] == dateText) {
                return;
            }
            window.wteCartFields["trip-date"] = dateText;

            try {
                if ("" == window.wte_fix_date.enabled) {
                    return;
                }
            } catch (err) { }

            try {
                var seatsAvailableLength = wte_fix_date.seats_available.length;
                for (var i = 0; i < seatsAvailableLength; i++) {
                    var seatsAvailable = wte_fix_date.seats_available[i][dateText];
                    var price = wte_fix_date.cost[i][dateText];

                    if (undefined !== seatsAvailable) {
                        var numberFields = $(
                            ".wpte-bf-content-travellers"
                        ).find('input[type="text"]');
                        $.each(numberFields, function (index, numberField) {
                            var cartField = $(numberField).data("cartField");
                            var defaultCount =
                                "travelers" == cartField ||
                                    "pricing_options[adult][pax]" == cartField
                                    ? 1
                                    : 0;
                            $(numberField).val(defaultCount);
                            $(numberField).attr("max", seatsAvailable);
                            $(".wpte-bf-content-travellers").data(
                                "maxtravellers",
                                seatsAvailable
                            );
                            // $(numberField).data('cost', price);
                            // var priceHtml = window.wte.currency.code + '<b> ' + wteGetFormatedPrice(price) + '</b>';
                            // $(numberField).parents('.wpte-bf-traveler-block').find('.wpte-bf-price ins').html(priceHtml);
                        });
                    }
                }
            } catch (err) { }
            populateHTML();
        },
    });

    // Change the tab.
    $('.wpte-bf-btn-wrap > input[type="button"]').on('click',function (event) {
        event.preventDefault();

        // Get the next tab.
        var nextTab = getNextTab(currentTab);
        if (nextTab) {
            if (currentTab.dataset.stepName == 'wpte-bf-step-travellers' || "travellers" == currentTab.innerText.toLowerCase()) {
                var total_pax = 0;
                $(".wpte-bf-content-travellers")
                    .find("input")
                    .each(function (i, n) {
                        total_pax += parseInt($(n).val(), 10);
                    });
                var MIN_PAX = parseInt(
                    $(".wpte-bf-content-travellers").data("mintravellers")
                );
                var MAX_PAX = parseInt(
                    $(".wpte-bf-content-travellers").data("maxtravellers")
                );
                if (total_pax >= MIN_PAX && total_pax <= MAX_PAX) {
                    // Deactive the current tab.
                    $(".wpte-bf-step").removeClass("active");
                    $(currentTab).removeClass("active");
                    changeTab(nextTab);
                } else {
                    var withmin = wte_strings.pax_validation.replace(
                        "%2$s",
                        MIN_PAX
                    );
                    var withmax = withmin.replace("%3$s", MAX_PAX);
                    var finalstr = withmax.replace("%1$s", total_pax);
                    toastr.error(finalstr);
                    return false;
                }
            }
        } else {
            // Add data to the cart.
            $.ajax({
                type: "POST",
                url: WTEAjaxData.ajaxurl,
                data: window.wteCartFields,
                success: function (data) {
                    if (data.success) {
                        $("#price-loading").fadeOut(500);
                        location.href = wp_travel_engine.CheckoutURL;
                    } else {
                        var i;
                        for (i = 0; i < data.data.length; i++) {
                            // Show Errors.
                            toastr.error(data.data[i]);
                        }
                    }
                },
            });
        }
    });

    /**
     * Change the tab and the tab content on click.
     */
    $("#wpte-booking-form").on("click", ".wpte-bf-step", function (event) {
        event.preventDefault();

        // Don't change the tab if date is not selected.
        if (!isDateSelected) {
            return false;
        }

        // Deactive the current tab.
        $(".wpte-bf-step").removeClass("active");
        $(this).removeClass("active");

        changeTab(this);
    });

    /**
     * Change the tabs to the supplied tab.
     */
    function changeTab(tab) {
        if (!isDateSelected) {
            return false;
        }

        // Set the current tab to next tab.
        currentTab = tab;

        // Get the index of the old tab.
        var tabs = $(".wpte-bf-step");
        var index = $(tabs).index(tab);

        // Change the tab content according to the tab.
        $(currentTabContent).fadeOut("slow", function () {
            // Active next tab.
            $(currentTab).addClass("active");

            $(currentTabContent).removeClass("active");
            currentTabContent = $(".wpte-bf-step-content")[index];
            $(currentTabContent).css("display", "");
            $(currentTabContent).css("opacity", "");
            $(currentTabContent).addClass("active");

            // Show price details except in calender.
            if (index === 0) {
                $(".wte-bf-price-detail").css("display", "none");
            } else {
                $(".wte-bf-price-detail").css("display", "");
            }

            // If it is the last tab, change the continue to checkout.
            if (index + 1 >= tabs.length) {
                $(
                    '.wte-bf-price-detail .wpte-bf-btn-wrap input[type="button"]'
                ).val(wte.bookNow);
            } else {
                $(
                    '.wte-bf-price-detail .wpte-bf-btn-wrap input[type="button"]'
                ).val(wte_strings.bookingContinue || 'Continue');
            }
        });
    }

    /**
     * Get next tab in the selection.
     */
    function getNextTab(tab) {
        // Get the index of the old tab.
        var tabs = $(".wpte-bf-step");
        var index = $(tabs).index(tab);

        // Return false if there is no next tab.
        if (index + 1 >= tabs.length) {
            return false;
        }

        return tabs[index + 1];
    }

    populateHTML()
});

/**
 *  Format the price. (e.g. 1200 -> 1,200)
 *
 * @param {float} price          Price to be formatted.
 * @param {string} code          Currency code.
 * @param {boolean} format        Whether to format the price or not. (default = true)
 *
 * @return {string} Formatted price.
 */
function wteGetFormatedPrice(price, format, numberOfDecimals) {
    // Set default values.
    price = price || 0.0;
    format = format || true;
    numberOfDecimals = numberOfDecimals || 0;

    if ("undefined" !== typeof WTE_CC_convData && WTE_CC_convData.rate) {
        price = price * parseFloat(WTE_CC_convData.rate);
    }

    // Bail early if the format is false.
    if (false == format) {
        return price;
    }

    price = parseFloat(price);
    price = price.toFixed(numberOfDecimals);
    price = price.replace(".00", "");

    price = addCommas(price);

    return price;
}

/**
 *  Format price with currency code. (e.g. USD 1,200)
 *
 * @param {float} price          Price to be formatted.
 * @param {string} code          Currency code.
 * @param {boolean} format        Whether to format the price or not. (default = true)
 * @param {int} numberOfDecimals Number of numbers after decimal point.
 *
 * @return {string} Formatted price with currency code.
 */
function wteGetFormatedPriceWithCurrencyCode(
    price,
    code,
    format,
    numberOfDecimals
) {
    // Set default values
    code = code || wte.currency.code;

    var formattedPrice =
        code + " " + wteGetFormatedPrice(price, format, numberOfDecimals);

    return formattedPrice;
}

/**
 *  Format price with currency code and symbol. (e.g. USD $1,200)
 *
 * @param {float} price        Price to be formatted.
 * @param {string} code        Currency code.
 * @param {string} symbol      Currency symbol.
 * @param {boolean} format     Whether to format the price or not. (default = true)
 * @param {int} numberOfDecimals Number of number after decimal point.
 *
 * @return {string} Formatted price with currency code and symbol.
 */
function wteGetFormatedPriceWithCurrencyCodeSymbol(
    price,
    code,
    symbol,
    format,
    numberOfDecimals
) {
    // Set default values.
    code = code || wte.currency.code;
    symbol = symbol || wte.currency.symbol;

    var currency_option = wte_currency_vars.code_or_symbol;
    var currency_symbol_display = "code" === currency_option ? code : symbol;

    var formattedPrice =
        '<span class="wpte-currency-code">' +
        currency_symbol_display +
        '</span> <span class="wpte-price">' +
        wteGetFormatedPrice(price, format, numberOfDecimals) +
        "</span>";

    return formattedPrice;
}

function wteGetFormatedPriceWithCurrencySymbol(
    price,
    symbol,
    format,
    numberOfDecimals
) {
    // Set default values.
    symbol = symbol || wte.currency.symbol;

    var formattedPrice =
        symbol + wteGetFormatedPrice(price, format, numberOfDecimals);

    return formattedPrice;
}

/**
 * Calculate grand total.
 */
function calculateGrandTotal() {
    var travellersCost = parseFloat(window.wte.trip.travellersCost);
    var extraServicesCost = parseFloat(window.wte.trip.extraServicesCost);

    return travellersCost + extraServicesCost;
}
