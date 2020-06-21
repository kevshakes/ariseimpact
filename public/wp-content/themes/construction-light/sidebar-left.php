<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Construction Light
 */

if ( ! is_active_sidebar( 'sidebar-2' )) {
	return;
}
?>
<aside id="secondary" class="widget-area col-lg-4 col-md-4 col-sm-12">
	<?php dynamic_sidebar( 'sidebar-2' ); ?>
</aside><!-- #secondary -->
