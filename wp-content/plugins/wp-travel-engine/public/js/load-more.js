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