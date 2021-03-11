<?php
/**
 * Trip Tabs Nav Template
 *
 * Closing "tabs-container" div is left out on purpose!.
 * 
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/single-trip/tabs-nav.php.
 * 
 * @package Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes/templates
 * @since @release-version //TODO: change after travel muni is live
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action('wp_travel_engine_before_trip_tabs');

$count = 0;

if ( ! empty( $tabs['id'] ) ) : ?>

	<div id="tabs-container" class="clearfix">
        <div class="nav-tab-wrapper">
            <div class="tab-inner-wrapper">
                <?php foreach ( $tabs['id'] as $key => $value ) : ?>
                    <div class="tab-anchor-wrapper">
                        <h2 class="wte-tab-title">
                            <a href="javascript:void(0);" 
                                class="nav-tab nb-tab-trigger<?php
                                if ($count == 0) {
                                    ?> nav-tab-active<?php } ?>" 
                                data-configuration="<?php echo esc_attr($tabs['id'][$value]); ?>">                            
                                <?php if (isset($tabs['icon'][$value]) && $tabs['icon'][$value] != '') {
                                    echo '<span class="tab-icon"><i class="' . esc_attr($tabs['icon'][$value]) . '"></i></span>';
                                } ?>
                                <?php echo esc_attr($tabs['name'][$value]); ?>
                            </a>
                        </h2>
                    </div>
                    <!-- ./tab-anchor-wrapper -->
                <?php $count++; endforeach; ?>
            </div>
            <!-- ./tab-inner-wrapper -->
        </div>
        <!-- ./nav-tab-wrapper -->        

<?php endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
