<?php
/**
 * WP Travel Engine Template Hooks
 *
 * @package WP_Travel_Engine
 */
class WP_Travel_Engine_Template_Hooks {

	private static $_instance = null;

	private function __construct() {
		$this->init_hooks();

	}

	public static function get_instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Initialization hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {

		add_action( 'wte_bf_travellers_input_fields', array( $this, 'booking_form_traveller_inputs' ) );
		add_action( 'wte_after_price_info_list', array( $this, 'display_multi_pricing_info' ) );
		add_action( 'wp_travel_engine_trip_itinerary_template', array( $this, 'display_itinerary_content' ) );

		add_action( 'wp_travel_engine_checkout_header_steps', array( $this, 'checkout_header_steps' ) );

		$this->init_single_trip_hooks();

	}

	private function init_single_trip_hooks() {
		add_action( 'wp_travel_engine_before_trip_content', array( $this, 'trip_content_wrapper_start' ), 5 );
		add_action( 'wte_single_trip_content', array( $this, 'display_single_trip_title' ), 5 );
		add_action( 'wte_single_trip_content', array( $this, 'display_single_trip_gallery' ), 10 );
		add_action( 'wte_single_trip_content', array( $this, 'display_single_trip_content' ), 15 );
		add_action( 'wte_single_trip_content', array( $this, 'display_single_trip_tabs_nav' ), 20 );
		add_action( 'wte_single_trip_content', array( $this, 'display_single_trip_tabs_content' ), 25 );
		add_action( 'wte_single_trip_footer', array( $this, 'display_single_trip_footer' ), 5 );
		add_action( 'wp_travel_engine_after_trip_content', array( $this, 'trip_content_wrapper_end' ), 5 );
		add_action( 'wp_travel_engine_trip_sidebar', array( $this, 'trip_content_sidebar' ), 5 );
		add_action( 'wp_travel_engine_primary_wrap_close', array( $this, 'trip_wrappers_end' ), 5 );

		add_action( 'wte_single_trip_tab_content_wp_editor', array( $this, 'display_wp_editor_content' ), 10, 4 );
		add_action( 'wte_single_trip_tab_content_itinerary', array( $this, 'display_itinerary_content' ), 10, 4 );
		add_action( 'wte_single_trip_tab_content_cost', array( $this, 'display_cost_content' ), 10, 4 );
		add_action( 'wte_single_trip_tab_content_faqs', array( $this, 'display_faqs_content' ), 10, 4 );
		add_action( 'wte_single_trip_tab_content_map', array( $this, 'display_map_content' ), 10, 4 );
		add_action( 'wte_single_trip_tab_content_review', array( $this, 'display_review_content' ), 10, 4 );

		add_action( 'wp_travel_engine_trip_secondary_wrap', array( $this, 'trip_secondary_wrap_start' ), 5 );
		add_action( 'wp_travel_engine_trip_price', array( $this, 'display_trip_price' ), 5 );
		add_action( 'wp_travel_engine_trip_facts', array( $this, 'display_trip_facts' ), 5 );
		add_action( 'wp_travel_engine_trip_secondary_wrap_close', array( $this, 'trip_secondary_wrap_close' ), 5 );

		add_action( 'wte_after_overview_content', array( $this, 'display_overview_trip_highlights' ), 999 );

		// Tab Titles.
		add_action( 'wte_overview_tab_title', array( $this, 'show_overview_title' ), 999 );
		add_action( 'wte_cost_tab_title', array( $this, 'show_cost_tab_title' ), 999 );
		add_action( 'wte_custom_t_tab_title', array( $this, 'show_custom_tab_title' ), 999 );
		add_action( 'wte_faqs_tab_title', array( $this, 'show_faqs_tab_title' ), 999 );
		add_action( 'wte_map_tab_title', array( $this, 'show_map_tab_title' ), 999 );
		add_action( 'wte_itinerary_tab_title', array( $this, 'show_itinerary_tab_title' ), 999 );

	}

	/**
	 * Secondary wrap start.
	 */
	public function trip_secondary_wrap_start() {
		do_action( 'wp_travel_engine_before_secondary' );
		?>
			<div id="secondary" class="widget-area">
		<?php
	}

	/**
	 * Checkout page header steps.
	 *
	 * @return void
	 */
	public function checkout_header_steps() {
		// Get template for header crumbs.
		return wte_get_template( 'checkout/header-steps.php' );
	}

	/**
	 * Secondary content such as pricing for single trip.
	 */
	public function display_trip_price() {
		global $post;

		// Functions
		$functions     = new Wp_Travel_Engine_Functions();
		$currency_code = 'USD';
		$currency_code = $functions->trip_currency_code( $post );

		// Get global and post settings.
		$post_meta    = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
		$wte_settings = get_option( 'wp_travel_engine_settings', true );

		$price_per_text = isset( $post_meta['trip_price_per'] ) && ! empty( $post_meta['trip_price_per'] ) ? $post_meta['trip_price_per'] : 'per-person';

		// Get trip price.
		$is_sale_price_enabled = wp_travel_engine_is_trip_on_sale( $post->ID );
		$sale_price            = wp_travel_engine_get_sale_price( $post->ID );
		$regular_price         = wp_travel_engine_get_prev_price( $post->ID );
		$price                 = wp_travel_engine_get_actual_trip_price( $post->ID );
		// Don't load the trip price template, if the booking form hidden option is set.
		if ( isset( $wte_settings['booking'] ) ) {
			return;
		}

		// Don't load the template, if the regular price is not set.
		if ( '' === trim( $regular_price ) ) {
			return;
		}

		// Get booking steps.
		/**
		 * Converted into Associative array.
		 *
		 * @change 4.1.7 To keepup tab/step uniqueness.
		 */
		$booking_steps = array(
			'date'       => __( 'Select a Date', 'wp-travel-engine' ),
			'travellers' => __( 'Travellers', 'wp-travel-engine' ),
		);
		$booking_steps = apply_filters( 'wte_trip_booking_steps', $booking_steps );

		// Get placeholder.
		$wte_placeholder = isset( $wte_settings['pages']['wp_travel_engine_place_order'] ) ? $wte_settings['pages']['wp_travel_engine_place_order'] : '';

		do_action( 'wp_travel_engine_before_trip_price' );
		if ( defined( 'WTE_USE_OLD_BOOKING_PROCESS' ) && WTE_USE_OLD_BOOKING_PROCESS ) :
			require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/frontend/trip-meta/trip-meta-parts/trip-price-bak.php';
			else :
				require WP_TRAVEL_ENGINE_BASE_PATH . '/includes/frontend/trip-meta/trip-meta-parts/trip-price.php';
			endif;
			do_action( 'wp_travel_engine_after_trip_price' );
	}

	/**
	 * Secondary content such as trip facts for single trip.
	 */
	public function display_trip_facts() {
		do_action( 'wp_travel_engine_before_trip_facts' );
		require_once WP_TRAVEL_ENGINE_BASE_PATH . '/includes/frontend/trip-meta/trip-meta-parts/trip-facts.php';
		do_action( 'wp_travel_engine_after_trip_facts' );
	}

	/**
	 * Secondary wrap close.
	 */
	public function trip_secondary_wrap_close() {
		?>
			</div>
			<!-- #secondary -->
		<?php
	}

	/**
	 * Trip Footer
	 */
	public function display_single_trip_footer() {
		wte_get_template( 'single-trip/trip-footer.php' );
	}

	/**
	 * Cost Includes/Excludes tab content
	 */
	public function display_cost_content( $id, $field, $name, $icon ) {
		global $post;

		$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

		$data = array(
			'cost' => $post_meta['cost'],
		);

		wte_get_template( 'single-trip/trip-tabs/cost.php', $data );
	}

	/**
	 * Faqs tab content
	 */
	public function display_faqs_content( $id, $field, $name, $icon ) {
		global $post;

		$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

		$data = array(
			'faq' => $post_meta['faq'],
		);

		wte_get_template( 'single-trip/trip-tabs/faqs.php', $data );
	}

	/**
	 * Map Tab content.
	 */
	public function display_map_content( $id, $field, $name, $icon ) {
		global $post;
		// $post_meta = get_post_meta($post->ID, 'wp_travel_engine_setting', true);

		$data = array(
			'post_id' => $post->ID,
		);

		wte_get_template( 'single-trip/trip-tabs/map.php', $data );
	}

	/**
	 * Review Tab content
	 */
	public function display_review_content( $id, $field, $name, $icon ) {
		global $post;

		$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

		$title = isset( $post_meta['review']['review_title'] ) && '' != $post_meta['review']['review_title']
		? $post_meta['review']['review_title'] : '';

		$data = array(
			'id'    => $post->ID,
			'title' => $title,
		);

		wte_get_template( 'single-trip/trip-tabs/review.php', $data );
	}

	/**
	 * Itinerary Tab Content
	 */
	public function display_itinerary_content( $id, $field, $name, $icon ) {
		wte_get_template( 'single-trip/trip-tabs/itinerary-tab.php' );
	}

	/**
	 * Overview/WPeditor Tab
	 */
	public function display_wp_editor_content( $id, $field, $name, $icon ) {
		global $post;
		$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

		$key = "{$id}_wpeditor";

		if ( '1' == $id ) {
			$data = array(
				'overview' => $post_meta['tab_content'][ $key ],
			);
			wte_get_template( 'single-trip/trip-tabs/overview.php', $data );
		} else {
			$data = array(
				'editor' => $post_meta['tab_content'][ $key ],
				'name'   => sanitize_title( $name ),
				'id'     => $id,
			);
			wte_get_template( 'single-trip/trip-tabs/editor.php', $data );
		}
	}

	/**
	 * Trip tabs content
	 */
	public function display_single_trip_tabs_content() {
		$settings = wte_get_active_single_trip_tabs();

		if ( false === $settings ) {
			return;
		}

		$data = array(
			'tabs' => $settings['trip_tabs'],
		);

		wte_get_template( 'single-trip/tabs-content.php', $data );
	}

	/**
	 * Trip tabs nav
	 */
	public function display_single_trip_tabs_nav() {
		$settings = wte_get_active_single_trip_tabs();

		if ( false === $settings ) {
			return;
		}

		$data = array(
			'tabs' => $settings['trip_tabs'],
		);

		wte_get_template( 'single-trip/tabs-nav.php', $data );
	}

	/**
	 * Single Trip title.
	 */
	public function display_single_trip_title() {
		global $post;

		$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

		$duration = isset( $post_meta['trip_duration'] ) && '' != $post_meta['trip_duration']
		? $post_meta['trip_duration'] : '';

		$data = array(
			'duration' => $duration,
		);

		wte_get_template( 'single-trip/title.php', $data );
	}

	/**
	 * Single Trip Feat Image or Gallery.
	 */
	public function display_single_trip_gallery() {

		do_action( 'wp_travel_engine_feat_img_trip_galleries' );

		if ( ! has_action( 'wp_travel_engine_feat_img_trip_galleries' ) ) {
			wte_get_template( 'single-trip/gallery.php' );
		}
	}

	/**
	 * Single Trip content
	 */
	public function display_single_trip_content() {
		global $post;

		$settings  = get_option( 'wp_travel_engine_settings', true );
		$post_meta = get_post_meta( $post->ID, 'WTE_Fixed_Starting_Dates_setting', true );

		$data = array(
			'settings'  => $settings,
			'post_meta' => $post_meta,
		);

		wte_get_template( 'single-trip/trip-content.php', $data );
	}

	/**
	 * Main wrap of the single trip.
	 */
	public function trip_content_wrapper_start() {
		wte_get_template( 'single-trip/trip-content-wrapper-start.php' );
	}

	/**
	 * Main wrap end of the single trip.
	 */
	public function trip_content_wrapper_end() {
		wte_get_template( 'single-trip/trip-content-wrapper-end.php' );
	}

	/**
	 * Trip Wrapper close.
	 */
	public function trip_wrappers_end() {
		?>
			</div>
			<!-- #primary -->
		</div>
		<!-- .row -->
		<?php
			do_action( 'wp_travel_engine_before_related_posts' );
			do_action( 'wp_travel_engine_related_posts' );
			do_action( 'wp_travel_engine_after_related_posts' );
		?>
	</div>
	<!-- .trip-content-area  -->
		<?php
	}

	/**
	 * Sidebar of the single trip.
	 */
	public function trip_content_sidebar() {
		wte_get_template( 'single-trip/trip-sidebar.php' );
	}

	/**
	 * Booking form traveller input fields.
	 *
	 * @return void
	 */
	public function booking_form_traveller_inputs() {

		global $post;

		$trip_id = $post->ID;

		$post_meta = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );

		// Get trip price.
		$is_sale_price_enabled = wp_travel_engine_is_trip_on_sale( $post->ID );
		$sale_price            = wp_travel_engine_get_sale_price( $post->ID );
		$regular_price         = wp_travel_engine_get_prev_price( $post->ID );
		$price                 = wp_travel_engine_get_actual_trip_price( $post->ID );

		$this->booking_form_multiple_pricing_inputs( $trip_id, $price );

	}

	public function display_multi_pricing_info() {
		$wte_options = get_option( 'wp_travel_engine_settings', true );

		// Bail if disabled.
		if ( ! isset( $wte_options['show_multiple_pricing_list_disp'] ) || '1' != $wte_options['show_multiple_pricing_list_disp'] ) {
			return;
		}

		global $post;
		// Don't show the child price info, if the multi pricing is for child is set.
		$trip_settings            = get_post_meta( $post->ID, 'wp_travel_engine_setting', true );
		$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;
		if ( $multiple_pricing_options ) :
			foreach ( $multiple_pricing_options as $price_key => $multiple_pricing ) :
				if ( '' === $multiple_pricing['price'] ) {
					continue;
				}

				$is_sale = false;
				if ( isset( $multiple_pricing['enable_sale'] ) && '1' === $multiple_pricing['enable_sale'] ) {
					$is_sale = true;
				}

				if ( isset( $multiple_pricing['sale_price'] ) ) {
					$sale_price = apply_filters( 'wte_multi_pricing', $multiple_pricing['sale_price'], $post->ID );
				}

				if ( isset( $multiple_pricing['price'] ) ) {
					$regular_price = apply_filters( 'wte_multi_pricing', $multiple_pricing['price'], $post->ID );
				}

				$price = $regular_price;
				if ( $is_sale ) {
					$price = $sale_price;
				}
				?>
				<?php $a = 1; ?>
			<div class="wpte-bf-price">
				<?php if ( $is_sale ) : ?>
					<del>
					<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $regular_price ); ?>
					</del>
				<?php endif; ?>
					<ins>
				<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $price ); ?></b>
			</ins>
			<span class="wpte-bf-pqty"><?php esc_html_e( 'Per', 'wp-travel-engine' ); ?> <?php echo esc_html( $multiple_pricing['label'] ); ?></span>
		</div>

				<?php
			endforeach;
		endif;
	}

	/**
	 * Load booking form input fields
	 *
	 * @return void
	 */
	public function booking_form_default_traveller_inputs( $price ) {
		?>
			<div class="wpte-bf-traveler-block">
				<div class="wpte-bf-traveler">
					<div class="wpte-bf-number-field">
						<input type="text" name="add-member" value="1" min="0" max="99999999999999"
							disabled
							data-cart-field = "travelers"
							data-cost-field = 'travelers-cost'
							data-type = '<?php echo apply_filters( 'wte_default_traveller_type', __( 'Person', 'wp-travel-engine' ) ); ?>'
							data-cost="<?php echo esc_attr( $price ); ?>" />
						<button class="wpte-bf-plus">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
						</button>
						<button class="wpte-bf-minus">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
						</button>
					</div>
					<span><?php echo apply_filters( 'wte_default_traveller_type', __( 'Person', 'wp-travel-engine' ) ); ?></span>
				</div>
				<div class="wpte-bf-price">
					<ins>
						<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $price ); ?></b>
					</ins>
					<span class="wpte-bf-pqty"><?php echo apply_filters( 'wte_default_traveller_unit', __( 'Per Person', 'wp-travel-engine' ) ); ?></span>
				</div>
			</div>
		<?php
		do_action( 'wpte_after_travellers_input' );

	}

	/**
	 * Multiple pricing input fields.
	 *
	 * @return void
	 */
	public function booking_form_multiple_pricing_inputs( $trip_id, $default_price ) {

		$trip_settings                             = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$multiple_pricing_options                  = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;
		$multiple_pricing_is_adult_price_available = $this->multiple_pricing_is_adult_price_available( $trip_id );
		if ( $multiple_pricing_options && $multiple_pricing_is_adult_price_available ) :
			foreach ( $multiple_pricing_options as $key => $pricing_option ) :
				$min_pax     = isset( $pricing_option['min_pax'] ) && ! empty( $pricing_option['min_pax'] ) ? $pricing_option['min_pax'] : 0;
				$max_pax     = isset( $pricing_option['max_pax'] ) && ! empty( $pricing_option['max_pax'] ) ? $pricing_option['max_pax'] : 999999999;
				$enable_sale = isset( $pricing_option['enable_sale'] ) && '1' == $pricing_option['enable_sale'] ? true : false;

				$price         = $enable_sale && isset( $pricing_option['sale_price'] ) && ! empty( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : $pricing_option['price'];
				$pricing_label = isset( $pricing_option['label'] ) ? $pricing_option['label'] : ucfirst( $key );
				$value         = 'adult' === $key ? '1' : 0;
				$min_pax       = 0;

				$pricing_type = isset( $pricing_option['price_type'] ) && ! empty( $pricing_option['price_type'] ) ? $pricing_option['price_type'] : 'per-person';

				if ( '' === $price ) {
					continue;
				}

				// $price = apply_filters( 'wte_multi_pricing', $price, $trip_id );

				?>
					<div class="wpte-bf-traveler-block">
						<div class="wpte-bf-traveler">
							<div class="wpte-bf-number-field">
								<input type="text" name="add-member" value="<?php echo esc_attr( $value ); ?>" min="<?php echo esc_attr( $min_pax ); ?>" max="<?php echo esc_attr( $max_pax ); ?>"
									disabled
									data-cart-field = "pricing_options[<?php echo esc_attr( $key ); ?>][pax]"
									data-cost-field = 'pricing_options[<?php echo esc_attr( $key ); ?>][cost]'
									data-type = '<?php echo esc_attr( $key ); ?>'
									data-cost="<?php echo esc_attr( $price ); ?>"
									data-pricing-type="<?php echo esc_attr( $pricing_type ); ?>"/>
								<button class="wpte-bf-plus">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
								</button>
								<button class="wpte-bf-minus">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M368 224H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"></path></svg>
								</button>
							</div>
							<span><?php echo esc_html( $pricing_label ); ?></span>
						</div>
						<div class="wpte-bf-price">
							<ins>
								<?php echo wp_travel_engine_get_formated_price_with_currency_code_symbol( $price ); ?></b>
							</ins>
							<span class="wpte-bf-pqty"><?php echo apply_filters( 'wte_default_pricing_option_unit_' . $key, sprintf( __( 'Per %1$s', 'wp-travel-engine' ), $pricing_label ) ); ?></span>
						</div>
					</div>
				<?php
			endforeach;
		else :
			$this->booking_form_default_traveller_inputs( $default_price );
		endif;

	}

	/**
	 * Check if adult price available in multiple pricing
	 *
	 * @return void
	 */
	public function multiple_pricing_is_adult_price_available( $trip_id ) {

		$trip_settings            = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$multiple_pricing_options = isset( $trip_settings['multiple_pricing'] ) && ! empty( $trip_settings['multiple_pricing'] ) ? $trip_settings['multiple_pricing'] : false;

		if ( ! $multiple_pricing_options ) {
			return false;
		}

		if ( isset( $multiple_pricing_options['adult'] ) ) {

			$pricing_option = $multiple_pricing_options['adult'];
			$enable_sale    = isset( $pricing_option['enable_sale'] ) && '1' == $pricing_option['enable_sale'] ? true : false;
			$price          = $enable_sale && isset( $pricing_option['sale_price'] ) && ! empty( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : $pricing_option['price'];

			return ! empty( $price );

		}
		return false;
	}

	/**
	 * Display Trip highlights section
	 *
	 * @return void
	 */
	public function display_overview_trip_highlights() {
		global $post;

		$trip_id       = $post->ID;
		$post_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );

		$trip_highlights_title   = isset( $post_settings['trip_highlights_title'] ) ? $post_settings['trip_highlights_title'] : '';
		$trip_highlights_content = isset( $post_settings['trip_highlights'] ) ? $post_settings['trip_highlights'] : array();

		if ( ! empty( $trip_highlights_content ) && is_array( $trip_highlights_content ) ) {
			if ( ! empty( $trip_highlights_title ) ) {
				echo "<h3 class='wpte-trip-highlights-title'>{$trip_highlights_title}</h3>";
				echo "<ul class='wpte-trip-highlights' >";
				foreach ( $trip_highlights_content as $key => $highlight ) {
					$highlight = isset( $highlight['highlight_text'] ) && ! empty( $highlight['highlight_text'] ) ? $highlight['highlight_text'] : false;

					if ( $highlight ) {
						echo "<li class='trip-highlight'>{$highlight}</li>";
					}
				}
				echo '</ul>';
			}
		}
	}

	// Tab section title hooks.
	public function show_overview_title() {

		$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

		if ( ! $show_tab_titles ) {
			return;
		}

		global $post;
		$trip_id = $post->ID;

		$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$tab_title     = isset( $trip_settings['overview_section_title'] ) && ! empty( $trip_settings['overview_section_title'] ) ? $trip_settings['overview_section_title'] : false;

		if ( $tab_title ) {
			echo "<h2 class='wpte-overview-title'>{$tab_title}</h2>";
		}
	}

	// Tab section title hooks.
	public function show_cost_tab_title() {

		$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

		if ( ! $show_tab_titles ) {
			return;
		}

		global $post;
		$trip_id = $post->ID;

		$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$tab_title     = isset( $trip_settings['cost_tab_sec_title'] ) && ! empty( $trip_settings['cost_tab_sec_title'] ) ? $trip_settings['cost_tab_sec_title'] : false;

		if ( $tab_title ) {
			echo "<h2 class='wpte-cost-tab-title'>{$tab_title}</h2>";
		}
	}

	// Tab section title hooks.
	public function show_itinerary_tab_title() {

		$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

		if ( ! $show_tab_titles ) {
			return;
		}

		global $post;
		$trip_id = $post->ID;

		$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$tab_title     = isset( $trip_settings['trip_itinerary_title'] ) && ! empty( $trip_settings['trip_itinerary_title'] ) ? $trip_settings['trip_itinerary_title'] : false;

		if ( $tab_title ) {
			echo "<h2 class='wpte-itinerary-title'>{$tab_title}</h2>";
		}
	}

	// Tab section title hooks.
	public function show_faqs_tab_title() {

		$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

		if ( ! $show_tab_titles ) {
			return;
		}

		global $post;
		$trip_id = $post->ID;

		$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$tab_title     = isset( $trip_settings['faq_section_title'] ) && ! empty( $trip_settings['faq_section_title'] ) ? $trip_settings['faq_section_title'] : false;

		if ( $tab_title ) {
			echo "<h2 class='wpte-faqs-title'>{$tab_title}</h2>";
		}
	}

	// Tab section title hooks.
	public function show_map_tab_title() {

		$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

		if ( ! $show_tab_titles ) {
			return;
		}

		global $post;
		$trip_id = $post->ID;

		$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$tab_title     = isset( $trip_settings['map_section_title'] ) && ! empty( $trip_settings['map_section_title'] ) ? $trip_settings['map_section_title'] : false;

		if ( $tab_title ) {
			echo "<h2 class='wpte-map-title'>{$tab_title}</h2>";
		}
	}

	// Tab section title hooks.
	public function show_custom_tab_title( $tab_key ) {

		$show_tab_titles = apply_filters( 'wpte_show_tab_titles_inside_tabs', true );

		if ( ! $show_tab_titles ) {
			return;
		}

		global $post;
		$trip_id = $post->ID;

		$trip_settings = get_post_meta( $trip_id, 'wp_travel_engine_setting', true );
		$tab_title     = isset( $trip_settings[ 'tab_' . $tab_key . '_title' ] ) && ! empty( $trip_settings[ 'tab_' . $tab_key . '_title' ] ) ? $trip_settings[ 'tab_' . $tab_key . '_title' ] : false;

		if ( $tab_title ) {
			echo "<h2 class='wpte-{$tab_key}-title'>{$tab_title}</h2>";
		}
	}

}

WP_Travel_Engine_Template_Hooks::get_instance();
