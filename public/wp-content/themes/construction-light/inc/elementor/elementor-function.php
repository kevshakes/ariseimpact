<?php 
/**
 * Load the Construction Light Elementor widgets file and registers it
 */
if ( ! function_exists( 'construction_light_widgets_registered' ) ) :

	/**
	 * Load and register the required Elementor widgets file
	 *
	 * @param $widgets_manager
	 *
	 * @since Construction Light
	 */
	function construction_light_widgets_registered( $widgets_manager ) {

		//  Load Elementor Featured Service
		require get_template_directory() . '/inc/elementor/widgets/featured-service.php';

		//  Load Elementor About Us 
		require get_template_directory() . '/inc/elementor/widgets/about.php';

		//  Load Elementor Our Service
		require get_template_directory() . '/inc/elementor/widgets/service.php';

		//  Load Elementor Call To Action
		require get_template_directory() . '/inc/elementor/widgets/calltoaction.php';

		//  Load Elementor Video Call To Action
		require get_template_directory() . '/inc/elementor/widgets/video-calltoaction.php';

		//  Load Elementor Counter
		require get_template_directory() . '/inc/elementor/widgets/counter.php';

		//  Load Elementor Portfolio
		require get_template_directory() . '/inc/elementor/widgets/portfolio.php';

		//  Load Elementor Blog
		require get_template_directory() . '/inc/elementor/widgets/blog.php';

		//  Load Elementor Testimonial
		require get_template_directory() . '/inc/elementor/widgets/testimonial.php';

		//  Load Elementor Team
		require get_template_directory() . '/inc/elementor/widgets/team.php';

		//  Load Elementor Client
		require get_template_directory() . '/inc/elementor/widgets/client.php';

		//  Register Featured Service Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Featured_Service() );

		//  Register About Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_About() );

		//  Register Our Service Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Service() );

		//  Register Call To Action Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Calltoaction() );

		//  Register Testimonial Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Testimonial() );

		//  Register Portfolio  Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Portfolio() );

		//  Register Blog  Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Blog() );

		//  Register Team Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Team() );

		//  Register Client Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Client() );

		//  Register Video Call To Action Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Video_Calltoaction() );

		//  Register Counter Widget
		$widgets_manager->register_widget_type( new \Elementor\Construction_Light_Counter() );

	}

endif;

add_action( 'elementor/widgets/widgets_registered', 'construction_light_widgets_registered' );

if ( ! function_exists( 'construction_light_elementor_category' ) ) :

	/**
	 * Add the Elementor category for use in Construction Light widgets as seperator
	 *
	 * @since Construction Light 1.0.6
	 */
	function construction_light_elementor_category() {

		// Register widget block category for Elementor section
		\Elementor\Plugin::instance()->elements_manager->add_category( 'construction-light-widget-blocks', array(
			'title' => esc_html__( 'Construction Light Widgets', 'construction-light' ),
		), 1 );
	}

endif;

add_action( 'elementor/init', 'construction_light_elementor_category' );

/**
 * Return the values of all the Pages
 * present in the site
 *
 * @since Construction Light
 */


function construction_light_pages() {

	$output     = array();

	$pages = get_pages();

	foreach ( $pages as $page ) {

		$output[ $page->ID ] = $page->post_title;
	}

	return $output;
}

/**
 * Return the values of all the categories of the posts
 * present in the site
 *
 * @return array of category ids and its respective names
 *
 * @since Construction Light
 */
function construction_light_categories() {
	$output     = array();
	$categories = get_categories();

	foreach ( $categories as $category ) {
		$output[ $category->term_id ] = $category->name;
	}

	return $output;
}


if ( ! function_exists( 'construction_light_elementor_scripts' ) ) {

    /**
     * Loads scripts on elementor editor
     *
     * @since Construction Light
     */
    function construction_light_elementor_scripts() {
        wp_enqueue_script('construction-light-custom-elementor', get_template_directory_uri() . '/inc/elementor/assets/custom-elementor.js', array( 'jquery' ) );
    }

}
add_action( 'elementor/frontend/after_enqueue_scripts', 'construction_light_elementor_scripts' );