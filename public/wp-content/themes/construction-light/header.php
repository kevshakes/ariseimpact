<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Construction Light
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="page" class="site">

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'construction-light' ); ?></a>

<?php

    /**
     * Header Type
    */
    $headerlayout = get_theme_mod('construction_light_header_layout','layout_one');
    
    if($headerlayout == 'layout_one'){

        get_template_part('header/header', 'one');

    }else if($headerlayout == 'layout_two'){

        get_template_part('header/header', 'two');

    }else{ 

        get_template_part('header/header', 'one');

    }


	if( is_front_page() ){ 

        $bannerslider = get_theme_mod('construction_light_banner_slider_section', 'enable');

        if ($bannerslider == 'enable') {
    		/**
    	     * Hook -  construction_light_action_banner_slider
    	     *
    	     * @hooked construction_light_banner_slider - 25
    	     */

    	    do_action('construction_light_action_banner_slider');
        }
	}
?>


<?php
    $breadcrumbs_enable = get_theme_mod('construction_light_enable_breadcrumbs', 'enable');

    if ($breadcrumbs_enable == 'enable') {

        if (!is_front_page() && !is_home()) {
            /**
             * @hook construction_light_breadcrumbs.
             *
             * @hooked construction_light_breadcrumbs.
             *
             */
            do_action('construction_light_breadcrumbs');
        }
    }
?>

	<div id="content" class="site-content">
