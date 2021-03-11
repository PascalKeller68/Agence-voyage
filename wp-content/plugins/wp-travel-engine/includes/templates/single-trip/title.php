<?php
/**
 * Single Trip header
 * 
 * This template can be overridden by copying it to yourtheme/wp-travel-engine/single-trip/title.php.
 * 
 * @package Wp_Travel_Engine
 * @subpackage Wp_Travel_Engine/includes/templates
 * @since @release-version //TODO: change after travel muni is live
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<header class="entry-header">
    <h1 class="entry-title" itemprop="name">
    <?php the_title(); ?>

    <!-- Display duration -->
    <?php if ( ! empty( $duration ) ): ?>
        <span class="wte-title-duration">
        <?php 
            printf(
                _nx( ' - %s Day', ' - %s Days', $duration, 'single-trip-title', 'wp-travel-engine' ),
                number_format_i18n( $duration ) 
            ); 
        ?>
        </span>
    <?php endif; ?>
    <!-- ./ Display duraiton -->
    </h1>
    <?php do_action('wp_travel_engine_header_hook'); ?>
</header>
<!-- ./entry-header -->

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
