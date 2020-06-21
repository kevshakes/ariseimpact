<?php
/**
 * Main Custom admin functions area
 *
 * @since Sparkle Themes
 *
 * @param Construction Light
 *
 */

/**
 * Load Custom Themes functions that act independently of the theme functions.
 */
require get_theme_file_path('inc/themes-functions.php');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Customizer Sanitization.
 */
require get_template_directory() . '/inc/customizer/sanitize.php';

/**
 * Customizer Custom Controller.
 */
require get_template_directory() . '/inc/customizer/custom-controller.php';

/**
 * Customizer Active Call Back .
 */
require get_template_directory() . '/inc/customizer/callback.php';

/**
 * Breadcrumbs.
 */
require get_template_directory() . '/inc/breadcrumbs/breadcrumbs.php';


require get_template_directory() . '/inc/widgets/siteorigin-panels.php';

/**
 * Custom Widget About Us.
 */
require get_template_directory() . '/inc/widgets/widget-about.php';

/**
 * Custom Widget Service.
 */
require get_template_directory() . '/inc/widgets/widget-service.php';

/**
 * Custom Widget Video Call To Action.
 */
require get_template_directory() . '/inc/widgets/widget-video.php';

/**
 * Custom Widget Call To Action.
 */
require get_template_directory() . '/inc/widgets/widget_calltoaction.php';

/**
 * Custom Widget Counter.
 */
require get_template_directory() . '/inc/widgets/widget-counter.php';

/**
 * Custom Widget Featured Service.
 */
require get_template_directory() . '/inc/widgets/widget-featured-service.php';

/**
 * Custom Widget Team.
 */
require get_template_directory() . '/inc/widgets/widget-team.php';

/**
 * Custom Widget Testimonial.
 */
require get_template_directory() . '/inc/widgets/widget-testimonial.php';

/**
 * Custom Widget Portfolio.
 */
require get_template_directory() . '/inc/widgets/widget-portfolio.php';

/**
 * Custom Widget Blog.
 */
require get_template_directory() . '/inc/widgets/widget-blog.php';

/**
 * Custom Widget Blog.
 */
require get_template_directory() . '/inc/widgets/widget-clients.php';

/**
 * Dynamic Color.
 */
require get_template_directory() . '/inc/dynamic.php';

/**
 * Elementor.
 */
require get_template_directory() . '/inc/elementor/elementor-function.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {

	require get_template_directory() . '/inc/jetpack.php';
	
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {

	require get_template_directory() . '/inc/woocommerce.php';
	
}