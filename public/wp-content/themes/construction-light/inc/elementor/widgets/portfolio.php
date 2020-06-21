<?php
/**
 * Construction Light Portfolio.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Portfolio extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Portfolio widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-portfolio';
	}

	/**
	 * Retrieve Construction_Light_Portfolio widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Portfolio', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Portfolio widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-tabs';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Portfolio widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories(){
		return array( 'construction-light-widget-blocks' );
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'construction-light' ),
				
			]
		);

		//Blog section title.
		$this->add_control(
			'portfolio_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//Blog section subtitle.
		$this->add_control(
			'portfolio_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		// post category.
		$this->add_control(
			'portfolio_category',
			[
				'label' => __( 'Select Multiple Category :', 'construction-light' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => construction_light_categories(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'construction-light' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_transform',
			[
				'label' => __( 'Title Text Transform', 'construction-light' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'construction-light' ),
					'uppercase' => __( 'UPPERCASE', 'construction-light' ),
					'lowercase' => __( 'lowercase', 'construction-light' ),
					'capitalize' => __( 'Capitalize', 'construction-light' ),
				],
				'selectors' => [
					'{{WRAPPER}} .section-title' => 'text-transform: {{VALUE}};',
				],
			]
		);

		//color section
		$this->add_control(
			'text_color',
			[
				'label' => __( 'Title Color', 'construction-light' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#404040',
				'selectors' => [
					'{{WRAPPER}} .section-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {

		$settings  = $this->get_settings_for_display();
		$portfolio_title     = $settings['portfolio_title'];
		$portfolio_subtitle  = $settings['portfolio_subtitle'];
		$portfolio_category  = $settings['portfolio_category'];

		?>
		<section class="cons_ligcons_light_portfolio-section clearfix">
            <div class="container">

                <?php construction_light_section_title( $portfolio_title, $portfolio_subtitle ); ?>

                <?php

                if($portfolio_category):
               
	            	?>  
	                <div class="cons_light_portfolio-cat-name-list">
	                    <div class="cons_light_portfolio-cat-name active" data-filter="*"><?php echo esc_html_e('All Works','construction-light'); ?></div>
	                    <?php 
	                        foreach ($portfolio_category as $cons_light_portfolio_cat_single) {

	                            $category_slug = "";
	                            $category_slug = get_category($cons_light_portfolio_cat_single);

	                            if( is_object($category_slug)){

	                            $category_slug = 'portfolio-'.$category_slug->term_id;
	                    ?>
	                            <div class="cons_light_portfolio-cat-name" data-filter=".<?php echo esc_attr($category_slug); ?>">
	                                <?php echo esc_html(get_cat_name($cons_light_portfolio_cat_single)); ?>
	                            </div>

	                    <?php } } ?>
	                </div>
                <?php endif; ?>

                <div class="cons_light_portfolio-post-wrap clearfix">
                    <div class="cons_light_portfolio-posts clearfix">
                        <?php 

                        if($portfolio_category){

                        $count = 1;

                        $args = array( 
                        	'cat' => $portfolio_category, 
                        	'posts_per_page' => -1 
                        );

                        $query = new \WP_Query($args);

                        if($query->have_posts()):

                        	while($query->have_posts()) :

                        		$query->the_post(); 

                                $categories = get_the_category();
                                $category_slug = "";
                                $cat_slug = array();

	                            foreach ($categories as $category) {
	                                $cat_slug[] = 'portfolio-'.$category->term_id;
	                            }

		                            $category_slug = implode(" ", $cat_slug);

		                            if(has_post_thumbnail()){

		                                $image_url = get_template_directory_uri().'/assets/images/portfolio-small-blank.png';

		                                $cons_light_image = wp_get_attachment_image_src(get_post_thumbnail_id(),'construction-light-portfolio');  

		                                $cons_light_image_large = wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
		                            }else{
		                            	
		                                $image_url = get_template_directory_uri().'/assets/images/portfolio-small.png';

		                                $cons_light_image = "";
		                            }

		                        ?>
		                            <div class="cons_light_portfolio <?php echo esc_attr($category_slug); ?>">
		                                <div class="cons_light_portfolio-outer-wrap">
		                                    <div class="cons_light_portfolio-wrap" style="background-image: url(<?php echo esc_url( $cons_light_image[0] ) ?>);">
		                                    
		                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php esc_attr(get_the_title()); ?>">

		                                        <div class="cons_light_portfolio-caption">

		                                            <h3><?php the_title(); ?></h3>

		                                            <a class="cons_light_portfolio-link" href="<?php echo esc_url(get_permalink()); ?>"><i class="fa fa-link"></i></a>
		                                            
		                                            <?php if(has_post_thumbnail()){ ?>
		                                                <a class="cons_light_portfolio-image"  href="<?php echo esc_url( $cons_light_image_large[0] ) ?>"><i class="fa fa-search"></i></a>
		                                            <?php } ?>
		                                        </div>

		                                    </div>
		                                </div>
		                            </div>
	                        <?php endwhile; 
	                    endif; wp_reset_postdata(); } ?>
                    </div>
                </div>

            </div>
        </section>
		<?php
	}
	
}