<?php
/**
 * Construction Light Client.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Client extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Client widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-client';
	}

	/**
	 * Retrieve Construction_Light_Client widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Client', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Client widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Client widget belongs to.
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
				'label' => __( 'Client', 'construction-light' ),
			]
		);

		//section title
		$this->add_control(
			'client_title',
			[
				'label'       => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//section subtitle
		$this->add_control(
			'client_subtitle',
			[
				'label'       => esc_html__( 'Enter Section Subtitle :', 'construction-light' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

        //Elementery List client image.
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
        	'clinet_image',
        	[
				'label'       => esc_html__( 'Upload Client Image :', 'construction-light' ),
				'type'        => Controls_Manager::MEDIA,
				'label_block' => true,
			]
		);

		//Elementery Client link
		$repeater->add_control(
			'client_url',
			[
				'label'       => esc_html__( 'Enter Client Link :', 'construction-light' ),
				'type'        => Controls_Manager::URL,
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

        $this->add_control(
			'list',
			[
				'label'  => esc_html__( 'Client :', 'construction-light' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				
				
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

        $client_title    = $settings['client_title'];
        $client_subtitle = $settings['client_subtitle'];

        //($settings );
        $list      = $settings['list'];

        ?>
        <section class="cons_light_client_logo_layout_two">
            <div class="container">

                <?php construction_light_section_title( $client_title, $client_subtitle ); ?>

                <div class="row">
                    <div class="owl-carousel owl-theme client_logo">
                        <?php

                        if (!empty($list)) :

                            foreach ($list as $item):

                            	$client_image =  $item['clinet_image'];

                            	$client_link =  $item['client_url']['url'];
                            	$target = $item['client_url']['is_external'] ? ' target="_blank"' : '';
                            	$nofollow = $item['client_url']['nofollow'] ? ' rel="nofollow"' : '';
	                        	?>
	                            <div class="item">
	                                <div class="box">
	                                	<?php 
	                                	echo '<a href="' . esc_url($client_link) . '"' . $target . $nofollow . '><img src="'.esc_url($item['clinet_image']['url']).'" class="'.esc_attr("img-fluid").'"></a>';
	                                	?>
	                                    
	                                </div>
	                            </div>
	                        <?php endforeach;
	                    endif; ?>
                    </div>
                </div>

            </div>
        </section>
        <?php

	}	

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _content_template() {
		?>
        <section class="cons_light_client_logo_layout_two">
            <div class="container">

            	<div class="row">

	                <div class="col-lg-12 col-sm-12 col-xs-12">
                        <h2 class="section-title">{{{ settings.client_title }}}</h2>
                        <div class="section-tagline">{{{ settings.client_subtitle }}}</div>
	                </div>

	            </div>

                <div class="row">
                    <div class="owl-carousel owl-theme client_logo">
                       
                        <# if ( settings.list.length ) { #>
                            <# _.each( settings.list, function( item ) { #>

	                            <div class="item">
	                                <div class="box">
	                                	<a href="{{{ item.client_url.url }}}">
	                                		<img src="{{{ item.clinet_image.url }}}" class="img-fluid">
	                                	</a>
	                                    
	                                </div>
	                            </div>
	                        <# }); #>
	                    <# } #>
                    </div>
                </div>

            </div>
        </section>
		<?php
	}

	

}