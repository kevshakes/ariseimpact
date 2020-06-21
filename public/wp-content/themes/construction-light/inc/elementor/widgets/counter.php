<?php
/**
 * Construction Light Counter.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Counter extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Counter widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-counter';
	}

	/**
	 * Retrieve Construction_Light_Counter widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Counter', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Counter widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-counter';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Counter widget belongs to.
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
				'label' => __( 'Counter', 'construction-light' ),
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

		//counter section title.
		$this->add_control(
			'counter_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//counter section subtitle.
		$this->add_control(
			'counter_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

        //Elementery List
        $repeater = new \Elementor\Repeater();

		//Elementery icons
		$repeater->add_control(
			'counter_icon',
			[
				'label'       => esc_html__( 'Select Icon :', 'construction-light' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'default'     => 'fas fa-sitemap',
			]
		);

		//counter title.
		$repeater->add_control(
			'counter_title',
			[
				'label'     => esc_html__( 'Enter Counter Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//counter number.
		$repeater->add_control(
			'counter_number',
			[
				'label'     => esc_html__( 'Enter Counter Number :', 'construction-light' ),
				'type'      => Controls_Manager::NUMBER,
				'label_block' => true,
			]
		);

        $this->add_control(
			'list',
			[
				'label'  => esc_html__( 'Counter Setting :', 'construction-light' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				
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

        $counter_title    = $settings['counter_title'];
        $counter_subtitle = $settings['counter_subtitle'];

        //($settings );
        $list      = $settings['list'];

        ?>
        <section class="cons_light_counter_wrap">

            <div class="container">

                <?php construction_light_section_title( $counter_title, $counter_subtitle ); ?>

                <div class="row cons_light_team-counter-wrap">
                    <?php

                        if (!empty($list)):

                        $i = 1;
                        foreach ( $list as $item ):

	                    	?>
	                        <div class="col-lg-3 col-md-3 col-sm-6">
	                            <div class="cons_light_counter">
	                                <div class="cons_light_counter-icon">
	                                    <i class="<?php echo esc_attr($item['counter_icon']); ?>"></i>
	                                </div>
	                                <div class="cons_light_counter-count odometer odometer<?php echo esc_attr($i); ?>" data-count="<?php echo absint($item['counter_number']); ?>">
	                                    99
	                                </div>
	                                <h6 class="cons_light_counter-title">
	                                    <?php echo esc_html($item['counter_title']); ?>
	                                </h6>
	                            </div>
	                        </div>
	                    <?php  $i++; endforeach; 
	                endif; ?>
                </div>
            </div>
        </section>
        <?php

	}	

}