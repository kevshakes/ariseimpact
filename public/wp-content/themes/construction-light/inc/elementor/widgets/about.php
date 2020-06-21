<?php
/**
 * Construction Light
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_About extends Widget_Base{

	/**
	 * Retrieve Construction_Light_About widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construct-light-about';
	}

	/**
	 * Retrieve Construction_Light_About widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'About Us', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_About widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-accordion';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_About widget belongs to.
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
				'label' => __( 'About Us', 'construction-light' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// about us page.
        $this->add_control(
        	'about_us_page',
        	[
				'label'       => esc_html__( 'Select Page :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => construction_light_pages(),
			]
		);

		//about us image.
		$this->add_control(
			'about_image',
			[
				'label' 		=> esc_html__( 'About Us Image :', 'construction-light' ),
				'type' 			=> \Elementor\Controls_Manager::MEDIA,
			]
		);

		//about content.
		$this->add_control(
			'about_content_display_type',
			[
				'label'       => esc_html__( 'Display the posts from:', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'excerpt',
				'options'     => array(
					'excerpt'      => esc_html__( 'Content Excerpt', 'construction-light' ),
					'full_content' => esc_html__( 'Full Content', 'construction-light' ),
				),
			]
		);

		//about excerpt button.
		$this->add_control(
			'about_button',
			[
				'label'       => esc_html__( 'Enter Button Text :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => array(
					'about_content_display_type' => 'excerpt',
				),
			]
		);

		//about email.
		$this->add_control(
			'about_email',
			[
				'label'       => esc_html__( 'Enter Email Address :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//about contact number.
		$this->add_control(
			'about_number',
			[
				'label'       => esc_html__( 'Enter Contct Number :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'award_achivement',
			[
				'label' => __( 'Award Achivement Settings', 'construction-light' ),
			]
		);

		$this->add_control(
			'show_achivement_award',
			[
				'label' => esc_html__( 'Show Achivement Award Counter', 'construction-light' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'construction-light' ),
				'label_off' => esc_html__( 'Hide', 'construction-light' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		//Elementery List
        $repeater = new \Elementor\Repeater();

        // Award Title
		$repeater->add_control(
			'award_title',
			[
				'label'       => esc_html__( 'Achivement Award Title :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//  Award Number
		$repeater->add_control(
			'award_number',
			[
				'label'       => esc_html__( 'Achivement Award Number :', 'construction-light' ),
				'type'        => Controls_Manager::NUMBER,
				'label_block' => true,
			]
		);

		$this->add_control(
			'list',
			[
				'label'  => esc_html__( 'Achivement Award Settings :', 'construction-light' ),
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
					'{{WRAPPER}} h3' => 'text-transform: {{VALUE}};',
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
					'{{WRAPPER}} h3' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} h3' => 'text-align: {{VALUE}};',
				],

			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'construction-light' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'layout1',
				'options' => [
					'layout1' => __( 'Layout 1', 'construction-light' ),
					'layout2' => __( 'Layout 2', 'construction-light' ),
					'layout3' => __( 'Layout 3', 'construction-light' )
				]
				
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

        //($settings );
		$about_page = $settings['about_us_page'];
		$style  = $settings['layout'];

        ?>
        <section class="about_us_front <?php echo esc_attr($style); ?>">
            <div class="container">
                <div class="row">
                    <?php 
                    if( !empty( $about_page ) ) :

                    	if (!empty($about_page)):

                    		$aboutus_args = array(
	                            'posts_per_page' => 1,
	                            'post_type'      => 'page',
	                            'page_id'        => $about_page,
	                            'post_status'    => 'publish',
	                        );

	                        $aboutus_query = new \WP_Query($aboutus_args);

	                        if ( $aboutus_query->have_posts()):

	                        	while ( $aboutus_query->have_posts()):

                        	    	$aboutus_query->the_post();

                        	    	$about_image = $settings['about_image']['url'];

                        	    	$about_col = '';

			                        if( !empty( $about_image ) && ($style != 'layout3') ){

			                            $about_col = 7;

			                        }else{

			                            $about_col = 12;

			                        }

			                        if (!empty($about_image) && $style == 'layout1'):
			                        	?>
			                            <div class="col-lg-5 col-md-5 col-sm-12">
			                                <img src="<?php echo esc_url($about_image); ?>"/>
			                            </div>
			                        <?php endif; ?>

			                        <div class="col-lg-<?php echo intval( $about_col ); ?> col-md-<?php echo intval( $about_col ); ?> col-sm-12">

			                        	<h3><?php the_title(); ?></h3>

			                        	<?php

			                        		$aboutus_info = $settings['about_content_display_type'];

										    if ( !empty( $aboutus_info ) && $aboutus_info == 'excerpt') {

										        the_excerpt();

										    } else {

										        the_content();
										    } 

										    $about_email = $settings['about_email'];

										    $about_phone = $settings['about_number'];

										    $phone_number = preg_replace("/[^0-9]/","",$about_phone);

										    if( !empty( $about_email ) || !empty( $about_phone )):
										    	?>

										    	<div class="address-info">
											        <ul>
											            <?php 

											            if( !empty( $about_email )):

											             ?>
											                <li><?php esc_html_e('Email Us :','construction-light'); ?>
											                    <a href="mailto:'<?php echo esc_attr( antispambot( $about_email ) ); ?>">
											                        <?php echo esc_html( antispambot( $about_email ) ); ?>
											                    </a>
											                </li>

											            <?php endif;

											            if( !empty( $about_email )):

												            ?>
											                <li><?php esc_html_e('Contact Us :','construction-light'); ?>
											                    <a href="tel:'<?php echo esc_attr( $phone_number ); ?>">
											                        <?php echo esc_html( $about_phone ); ?>
											                    </a>
											                </li>

												        <?php endif; ?>
											        </ul>
											    </div>
											<?php endif;

											if ( !empty( $aboutus_info ) && $aboutus_info == 'excerpt'):
												?>
												<a href="<?php the_permalink(); ?>" class="btn btn-primary">
											        <?php echo esc_html($settings['about_button']); ?><i class="fas fa-arrow-right"></i>
											    </a>
											<?php endif;

											$list = $settings['list'];

											$show_award = $settings['show_achivement_award'];

											if( !empty( $list ) && $show_award == 'yes'):

												echo '<div class="'. esc_attr('achivement-items').'"><ul>';

												foreach (  $list as $item ):

													$award_title =  $item['award_title'];

													$award_number =  $item['award_number'];

													?>
													<li>
									                    <div class="timer achivement"><?php echo intval( $award_number ); ?></div>
									                    <span class="medium"><?php echo esc_html( $award_title ); ?></span>
									                </li>

												<?php endforeach;

												echo '</ul></div>';

											endif;
										?>
									</div>

									<?php if (!empty($about_image) && $style == 'layout2'):
			                        	?>
			                            <div class="col-lg-5 col-md-5 col-sm-12">
			                                <img src="<?php echo esc_url($about_image); ?>"/>
			                            </div>
									<?php endif; ?>
									
									<?php if (!empty($about_image) && $style == 'layout3'):
			                        	?>
			                            <div class="col-lg-12 col-md-12 col-sm-12">
			                                <img src="<?php echo esc_url($about_image); ?>"/>
			                            </div>
			                        <?php endif; ?>

                        	    <?php endwhile;
	                        endif;

                    	endif;

                    endif;
                    ?>
                </div>
            </div>
        </section>
        <?php

	}
	

}