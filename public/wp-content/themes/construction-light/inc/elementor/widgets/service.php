<?php
/**
 * Construction Light Service.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Service extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Service widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-service';
	}

	/**
	 * Retrieve Construction_Light_Service widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Our Service', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Service widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-library-download';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Service widget belongs to.
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
				'label' => __( 'Our Servcie', 'construction-light' ),
			]
		);

		//service section title.
		$this->add_control(
			'service_title',
			[
				'label'     => esc_html__( 'Enter Service Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//service section subtitle.
		$this->add_control(
			'service_subtitle',
			[
				'label'     => esc_html__( 'Enter Service Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		//layout.
		$this->add_control(
			'service_layout',
			[
				'label'       => esc_html__( 'Service Layout :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'layout_one',

				'options' => 
				[
					'layout_one'  => esc_html__( 'Layout One', 'construction-light' ),
					'layout_two'  => esc_html__( 'Layout Two', 'construction-light' ),
				],
			]
		);

		//Elementery List
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
        	'service_page',
        	[
				'label'       => esc_html__( 'Select Page :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => construction_light_pages(),
			]
		);

		//Elementery icons
		$repeater->add_control(
			'service_icon',
			[
				'label'       => esc_html__( 'Select Icon :', 'construction-light' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'default'     => 'fas fa-sitemap',
			]
		);

		$this->add_control(
			'list',
			[
				'label'  => esc_html__( 'Our Service :', 'construction-light' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				
				
			]
		);

		//service button text.
		$this->add_control(
			'service_button',
			[
				'label'     => esc_html__( 'Enter Button Text :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
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

		// column.
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
					'{{WRAPPER}} .cons_light_feature .bottom-content h3' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .cons_light_feature .bottom-content' => 'text-align: {{VALUE}};'
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

		$settings         = $this->get_settings_for_display();
        $service_title    = $settings['service_title'];
        $service_subtitle = $settings['service_subtitle'];
        $service_layout   = $settings['service_layout'];
		$list             = $settings['list'];
		$columns		  = $settings['columns'];
		$box_column = 12 / intval($columns);

        ?>
        <section class="cons_light_feature <?php echo esc_attr( $service_layout ); ?>">
            <div class="container">
                
                <?php construction_light_section_title( $service_title, $service_subtitle ); ?>

                <div class="row">
                    <?php
                    if (!empty($list)):

                        foreach ($list as $item):

                            $service_pages =  $item['service_page'];

                            if (!empty($service_pages)):

	                            $service_query = new \WP_Query('page_id=' . $service_pages);

	                            if ( $service_query->have_posts() ): 

	                            	while ( $service_query->have_posts() ): 
	                            		$service_query->the_post();

				                    	?>
				                        <div class="col-lg-<?php echo esc_attr($box_column); ?> col-md-<?php echo esc_attr($box_column); ?> col-sm-6 col-xs-12 feature-list">
				                            <div class="box">
				                                <?php if( !empty( $service_layout ) && $service_layout == 'layout_one' ){ ?>
				                                    <figure>
				                                        <a href="<?php the_permalink(); ?>">
				                                            <?php the_post_thumbnail('construction-light-medium'); ?>
				                                        </a>
				                                    </figure>
				                                <?php } ?>

				                                <div class="bottom-content">

				                                    <?php if( !empty( $service_layout ) && $service_layout == 'layout_two' ){ ?>
				                                        <div class="icon-box">
				                                            <i class="<?php echo esc_attr($item['service_icon']); ?>"></i>
				                                        </div>
				                                    <?php } ?>

				                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

				                                    <?php the_excerpt(); ?>

													<?php if( !empty($settings['service_button'])): ?>
				                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary">
				                                        <?php echo esc_html($settings['service_button']); ?>
				                                        <i class="fas fa-arrow-right"></i>
				                                    </a>
													<?php endif; ?>
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
        </section>
        <?php
	}

}