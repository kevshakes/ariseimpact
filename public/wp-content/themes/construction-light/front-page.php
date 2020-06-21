<?php 
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Construction Light
 */
get_header();

/**
 * Enable Front Page
 */
do_action( 'construction_light_enable_front_page' );

$enable_front_page = get_theme_mod( 'construction_light_enable_frontpage' ,false);

    if ($enable_front_page == 1):

        $construction_light_home_sections = construction_light_homepage_section();

        foreach ($construction_light_home_sections as $construction_light_homepage_section) {

            $construction_light_homepage_section = str_replace('construction_light_', '', $construction_light_homepage_section);
            $construction_light_homepage_section = str_replace('_section', '', $construction_light_homepage_section);

            get_template_part( 'section/section', $construction_light_homepage_section );
        }
        
    endif;

get_footer();