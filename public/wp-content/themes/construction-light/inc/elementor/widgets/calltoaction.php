<?php
/**
 * Construction Light Call To Action.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Calltoaction extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Calltoaction widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-calltoaction';
	}

	/**
	 * Retrieve Construction_Light_Calltoaction widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Call To Action', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Calltoaction widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-parallax';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Calltoaction widget belongs to.
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
				'label' => __( 'Call To Action', 'construction-light' ),
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

		//calltoaction section title.
		$this->add_control(
			'calltoaction_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//calltoaction section subtitle.
		$this->add_control(
			'calltoaction_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		//calltoaction button one text.
		$this->add_control(
			'calltoaction_button_one',
			[
				'label'     => esc_html__( 'Enter Button One Text :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//calltoaction button one link.
		$this->add_control(
			'calltoaction_button_one_url',
			[
				'label'     => esc_html__( 'Enter Button One Link :', 'construction-light' ),
				'type'      => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'construction-light' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
			]
		);

		//calltoaction button two text.
		$this->add_control(
			'calltoaction_button_two',
			[
				'label'     => esc_html__( 'Enter Button Two Text :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//calltoaction button two link.
		$this->add_control(
			'calltoaction_button_two_url',
			[
				'label'     => esc_html__( 'Enter Button Two Link :', 'construction-light' ),
				'type'      => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'construction-light' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
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

		$settings      = $this->get_settings_for_display();
        $cta_title     = $settings['calltoaction_title'];
        $cta_sub_title = $settings['calltoaction_subtitle'];
		
		$button_one    = $settings['calltoaction_button_one'];
        $button_one_url= $settings['calltoaction_button_one_url']['url'];

        $target_one = $settings['calltoaction_button_one_url']['is_external'] ? ' target="_blank"' : '';
		$nofollow_one = $settings['calltoaction_button_one_url']['nofollow'] ? ' rel="nofollow"' : '';

        $button_two    = $settings['calltoaction_button_two'];    
        $button_two_url= $settings['calltoaction_button_two_url']['url'];

        $target_two = $settings['calltoaction_button_two_url']['is_external'] ? ' target="_blank"' : '';
		$nofollow_two = $settings['calltoaction_button_two_url']['nofollow'] ? ' rel="nofollow"' : '';

        ?>
        <div class="calltoaction_promo_wrapper">
                <div class="container">
                    <div class="calltoaction_full_widget_content">

                        <h2 class="wow zoomIn"><?php echo esc_html( $cta_title ); ?></h2>

                        <div class="calltoaction_subtitle wow zoomIn">
                            <p><?php echo esc_html( $cta_sub_title ); ?></p>
                        </div>
                    </div>

                    <div class="calltoaction_button_wrap">

                    	<?php 
                    	if (!empty($button_one)) {
                    		echo '<a href="'.esc_url($button_one_url).'" '. $target_one . $nofollow_one.' class="'.esc_attr("btn btn-primary wow fadeInLeft").'">'.esc_html( $button_one ).'<i class="'.esc_attr("fas fa-arrow-right").'"></i></a>';
                    	}

                    	if (!empty($button_two)) {

                    		echo '<a href="'.esc_url($button_two_url).'" '. $target_two . $nofollow_two .' class="'.esc_attr("btn btn-border wow fadeInRight").'">'.esc_html( $button_two ).'<i class="'.esc_attr("fas fa-arrow-right").'"></i></a>';                 		
                    	}

                    	?>
                    </div>
                </div>
            </div>
        <?php

	}

}