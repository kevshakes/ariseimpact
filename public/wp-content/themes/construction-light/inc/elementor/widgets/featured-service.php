<?php
/**
 * Construction Light Featured Service.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Featured_Service extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Featured_Service widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-featured-service';
	}

	/**
	 * Retrieve Construction_Light_Featured_Service widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Featured Service', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Featured_Service widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-library-save';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Featured_Service widget belongs to.
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
				'label' => __( 'Featured Service', 'construction-light' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT
			]
		);

        //Elementery List
        $repeater = new \Elementor\Repeater();

        // featured service page.
        $repeater->add_control(
        	'featured_service_page',
        	[
				'label'       => esc_html__( 'Select Page :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => construction_light_pages(),
			]
		);

		//Elementery icons
		$repeater->add_control(
			'featured_service_icon',
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
				'label'  => esc_html__( 'Featured Service :', 'construction-light' ),
				'type'   => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls()
			]
		);


		

		$this->end_controls_section();
		$this->start_controls_section(
			'info_section',
			[
				'label' => __( 'Columns', 'construction-light' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		
        // column.
		$this->add_control(
			'featured_service_column',
			[
				'label'       => esc_html__( 'Columns', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'three',

				'options' => 
				[
					'two'    => esc_html__( 'Two Column', 'construction-light' ),
					'three'  => esc_html__( 'Three Column', 'construction-light' ),
					'four'   => esc_html__( 'Four Column', 'construction-light' ),
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

        //($settings );
        $list      		= $settings['list'];

        $column      	= $settings['featured_service_column'];

        if($column == 'two'){

        	$col = 6;

        }elseif($column == 'three'){ 

        	$col = 4;

        }else{

        	$col = 3;

        }


        ?>
        <section class="cons_light_feature">
            <div class="container">
                <div class="row">
                    <?php

                    if( !empty( $list ) ):

	                    foreach (  $list as $item ):

	                    	$featured_service_pages =  $item['featured_service_page'];

                            $featured_service_icon =  $item['featured_service_icon'];

	                        if (!empty($featured_service_pages)):

	                            $featured_service_query = new \WP_Query('page_id=' . $featured_service_pages);

	                            if ($featured_service_query->have_posts()):

		                        	while ($featured_service_query->have_posts()): 

		                        		$featured_service_query->the_post();

				                    	?>
				                        <div class="col-lg-<?php echo intval($col);?> col-md-<?php echo intval($col);?> col-sm-6 col-xs-12 feature-list">
				                            <div class="box">
				                                <figure>
				                                    <a href="<?php the_permalink(); ?>">
				                                        <?php
					                                        if($column == 'two'){

					                                         the_post_thumbnail('construction-light-post-format');

					                                        }else{
					                                        	the_post_thumbnail('construction-light-medium');
					                                    	}
				                                   		?>
				                                    </a>
				                                </figure>

				                                <div class="bottom-content">

				                                    <div class="icon-box">
				                                        <i class="<?php echo esc_attr($featured_service_icon); ?>"></i>
				                                    </div>

				                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

				                                    <?php the_excerpt(); ?>

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