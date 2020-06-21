<?php
/**
 * Construction Light Testimonial.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Testimonial extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Testimonial widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-testimonial';
	}

	/**
	 * Retrieve Construction_Light_Testimonial widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Testimonial', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Testimonial widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Testimonial widget belongs to.
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
			'section_content',
			[
				'label' => __( 'Testimonial', 'construction-light' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'background',
				'label' => __( 'Background', 'construction-light' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}}',
			]
		);
		//section title.
		$this->add_control(
			'testimonial_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//section subtitle.
		$this->add_control(
			'testimonial_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

        //Elementery List
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
        	'testimonial_page',
        	[
				'label'       => esc_html__( 'Select Page :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => construction_light_pages(),
			]
		);

		//Elementery icons
		$repeater->add_control(
			'testimonial_designation',
			[
				'label'       => esc_html__( 'Enter Designation :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

        $this->add_control(
			'list',
			[
				'label'  => esc_html__( 'Testimonial :', 'construction-light' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				
				
			]
		);


		// columns
		$this->add_control(
			'columns',
			[
				'label'       => esc_html__( 'Columns', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 2,

				'options' => 
				[
					'1'    => esc_html__( 'Single Column', 'construction-light' ),
					'2'    => esc_html__( 'Two Column', 'construction-light' ),
					'3'  => esc_html__( 'Three Column', 'construction-light' ),
					'4'   => esc_html__( 'Four Column', 'construction-light' ),
				],
			]
		);

		$this->add_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'construction-light' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'construction-light' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'construction-light' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'construction-light' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} ' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} p' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} h3' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .client-text' => 'text-align: {{VALUE}};',
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

        $testimonial_title   = $settings['testimonial_title'];
        $testimonial_subtitle = $settings['testimonial_subtitle'];

        //($settings );
        $list      = $settings['list'];

        ?>
        <section class="cons_light_testimonial">
            <div class="container">

                <?php construction_light_section_title( $testimonial_title, $testimonial_subtitle ); ?>

                <div class="row">
                    <div class="owl-carousel owl-theme testimonial_slider_ele" data-columns="<?php echo intval($settings['columns']); ?>" >
	                    <?php

	                    if( !empty( $list ) ):

		                    foreach (  $list as $item ):

		                    	$testimonial_page =  $item['testimonial_page'];

	                            $designation      =  $item['testimonial_designation'];

		                        if (!empty($testimonial_page)):

		                            $testimonial_query = new \WP_Query('page_id=' . $testimonial_page);

		                            if ($testimonial_query->have_posts()):

			                        	while ($testimonial_query->have_posts()): 

			                        		$testimonial_query->the_post();

					                    	?>
					                        <div class="item">
				                                <div class="col-lg-12 col-md-12 col-sm-12">

				                                    <div class="client-img">
				                                        <?php the_post_thumbnail('thumbnail'); ?>
				                                    </div>

				                                    <?php the_excerpt(); ?>

				                                    <div class="client-text">
				                                        <h3>
				                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				                                        </h3>
				                                        <h4><?php echo esc_html($designation); ?></h4>
				                                    </div>
				                                </div>
				                            </div>
					                    <?php endwhile;
					                endif;
			                    endif;
		                    endforeach; 
		                endif; ?>
	                </div>
                </div>
            </div>
        </section>
        <?php

	}	

}