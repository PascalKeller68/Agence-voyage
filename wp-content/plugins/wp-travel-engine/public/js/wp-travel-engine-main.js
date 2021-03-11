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

(function () {
  var allInfos = document.querySelectorAll('.wpte-checkout-payment-info');
  var paymentMethodsRadio = document.querySelectorAll('[name=wpte_checkout_paymnet_method]');
  paymentMethodsRadio && paymentMethodsRadio.forEach(function (el) {
    el.checked && el.parentElement.classList.add('wpte-active-payment-method') && el.parentElement.querySelector('.wpte-checkout-payment-info').removeAttribute('style');
    el.addEventListener('change', function (e) {
      if (!!allInfos) {
        allInfos.forEach(function (el) {
          el.style.display = 'none';
          el.parentElement.classList.remove('wpte-active-payment-method');
        });
      }

      var parentEl = e.target.parentElement;
      parentEl.classList.add('wpte-active-payment-method');
      var infoEl = e.target.parentElement.querySelector('.wpte-checkout-payment-info');
      infoEl && infoEl.removeAttribute('style');
    });
  });
})();

jQuery(document).ready(function($){
        // // $.fn.isOnScreen = function(){
        // //     var win = $(window);
            
        // //     var viewport = {
        // //         top : win.scrollTop(),
        // //         left : win.scrollLeft()
        // //     };
        // //     viewport.right = viewport.left + win.width();
        // //     viewport.bottom = viewport.top + win.height();
            
        // //     var bounds = this.offset();
        // //     bounds.right = bounds.left + this.outerWidth();
        // //     bounds.bottom = bounds.top + this.outerHeight();
            
        // //     return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
            
        // // };

        // $.fn.isVisible = function() {
        //     // Am I visible?
        //     // Height and Width are not explicitly necessary in visibility detection, the bottom, right, top and left are the
        //     // essential checks. If an image is 0x0, it is technically not visible, so it should not be marked as such.
        //     // That is why either width or height have to be > 0.
        //     var rect = this[0].getBoundingClientRect();
        //     return (
        //         (rect.height > 0 || rect.width > 0) &&
        //         rect.bottom >= 0 &&
        //         rect.right >= 0 &&
        //         rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
        //         rect.left <= (window.innerWidth || document.documentElement.clientWidth)
        //     );
        // };
        // $(window).scroll(function() {
        //    // if ( $('.wp-travel-engine-sidebar').isOnScreen() == false ) {
        //   if ($('.wp-travel-engine-sidebar').isVisible()) {
        //     $('.trip-price').prependTo('#trip-secondary').removeClass('price-fixed');  
        //    }
        //   else
        //   {
        //     $('.trip-price').appendTo('#trip-secondary').addClass('price-fixed');
        //   }
        // });
        $(".trip-price").stick_in_parent();
    });
jQuery(document).ready(function($){
   $('.wp-travel-engine-datetime').datepicker({ maxDate: 0, changeMonth: true,
		changeYear: true, dateFormat: 'yy-mm-dd', yearRange: "-100:+0" });

 //   $(".wp-travel-engine-price-datetime").datepicker({
 //        dateFormat: "yy-mm-dd",
 //        minDate: 0,
 //        changeMonth: true,
	// 	changeYear: false,
 //        onSelect: function(){
 //            $(".check-availability").hide();
 //            $(".book-submit").fadeIn("slow");
 //        }
	// });
   
});
(function (window, document, $, undefined) {
	jQuery(document).ready(function($){
		
		$("body").on("click", ".btn-loadmore", function (e) {
			e.preventDefault();

			var button = $(this),
				current_page = button.attr('data-current-page'),
				max_page     = button.attr('data-max-page'),
				// mode         = $(".wte-view-mode-selection.active").attr('data-mode'),
				data         = {
					'action': 'wpte_ajax_load_more',
					'query' : button.attr('data-query-vars'),
					'page'  : current_page,
					'nonce' : beloadmore.nonce,
					// 'mode'  : mode
				};

			$.ajax({ // you can also use $.post here
				url: beloadmore.url, // AJAX handler
				data: data,
				type: 'POST',
				beforeSend: function (xhr) {
					$("#loader").fadeIn(500); // change the button text, you can also add a preloader image
				},
				success: function (response) {
					button.before(response);
					current_page++;
					button.attr('data-current-page', current_page);
					if (current_page == max_page)
						button.remove();
				},
				complete: function () {               
					$("#loader").fadeOut(500);
					wte_rating_star_initializer_for_templates();
				}
			});
			
		});
		
		$("body").on("click", ".load-destination", function (e) {
			e.preventDefault();

			var button = $(this),
				current_page = button.attr('data-current-page'),
				max_page     = button.attr('data-max-page'),
				// mode         = $(".wte-view-mode-selection.active").attr('data-mode'),
				data         = {
					'action': 'wpte_ajax_load_more_destination',
					'query' : button.attr('data-query-vars'),
					'page'  : current_page,
					'nonce' : beloadmore.nonce,
					// 'mode'  : mode
				};

			$.ajax({ // you can also use $.post here
				url: beloadmore.url, // AJAX handler
				data: data,
				type: 'POST',
				beforeSend: function (xhr) {
					$("#loader").fadeIn(500); // change the button text, you can also add a preloader image
				},
				success: function (response) {
					button.before(response);
					current_page++;
					button.attr('data-current-page', current_page);
					if (current_page == max_page)
						button.remove();
				},
				complete: function () {               
					$("#loader").fadeOut(500);
					wte_rating_star_initializer_for_templates();
				}
			});
			
		});
	});

	function wte_rating_star_initializer_for_templates() {
		if ($(document).find('.trip-review-stars').length) {
			$(document).find('.trip-review-stars').each(function () {
				var rating_value = $(this).data('rating-value');
				starSvgIcon = $(this).data('icon-type');
				var starSvgIcon = (starSvgIcon !== '') ? starSvgIcon : '';
				$(this).rateYo({
					rating: rating_value,
					starSvg: starSvgIcon,
				});
			});
		}
	}

})(window, document, jQuery);
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

jQuery(document).ready(function($){
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
     
    $('body').on('change', '.travelers-no', function (e){
        $("#price-loading").fadeIn(500);
        $val = $(this).val();
        $new_val = $('.hidden-price').first().text().replace(/,/g, '');
        $total = $val*$new_val;
        $('#trip-cost').val($total);
        $total = addCommas( $total );
        $('.total').text(addCommas($total));
        $("#price-loading").fadeOut(500);
    });
});
jQuery(document).ready(function($){
    var rtlenable = false;
    if(rtl.enable == '1')
    {
        rtlenable = true;
    }
    $(".wpte-trip-feat-img-gallery").owlCarousel({
        nav     : true,
        navigationText: ['&lsaquo;','&rsaquo;'],
        items   : 1,
        autoplay: true,
        slideSpeed: 300,
        paginationSpeed: 400,
        center  : true,
        loop    : true,
        rtl     : rtlenable,
        dots    : false
    });
});