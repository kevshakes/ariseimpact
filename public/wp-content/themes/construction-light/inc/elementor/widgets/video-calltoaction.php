<?php
/**
 * Construction Light Video Call To Action.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Video_Calltoaction extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Video_Calltoaction widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-video-calltoaction';
	}

	/**
	 * Retrieve Construction_Light_Video_Calltoaction widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Video Call To Action', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Video_Calltoaction widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-youtube';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Video_Calltoaction widget belongs to.
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
				'label' => __( 'Video Call To Action', 'construction-light' ),
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

		//video calltoaction section title.
		$this->add_control(
			'video_calltoaction_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//video calltoaction section subtitle.
		$this->add_control(
			'video_calltoaction_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		//video calltoaction button one link.
		$this->add_control(
			'video_calltoaction_button_one_url',
			[
				'label'     => esc_html__( 'Enter Youtube Video Link :', 'construction-light' ),
				'type'      => Controls_Manager::URL,
				'label_block' => true,
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

		$video_url = $settings['video_calltoaction_button_one_url']['url'];

        ?>
        <div class="calltoaction_promo_wrapper video_calltoaction">
            <div class="container">
                
                <div class="video_calltoaction_wrap">
                    <a href="<?php echo esc_url($video_url); ?>" target="_blank" class="popup-youtube  box-shadow-ripples"><i class="fas fa-play "></i></a>
                </div>

                <div class="calltoaction_full_widget_content">

                    <h2 class="wow zoomIn"><?php echo esc_html($settings['video_calltoaction_title']); ?></h2>

                    <div class="calltoaction_subtitle wow zoomIn">
                        <p><?php echo esc_html($settings['video_calltoaction_subtitle']); ?></p>
                    </div>
                </div>

            </div>
        </div>
        <?php

	}

}