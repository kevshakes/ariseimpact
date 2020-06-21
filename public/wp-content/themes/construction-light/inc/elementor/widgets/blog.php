<?php
/**
 * Construction Light Blog.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Blog extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Blog widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-blog';
	}

	/**
	 * Retrieve Construction_Light_Blog widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Blog', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Blog widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-masonry';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Blog widget belongs to.
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
			'blog_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//Blog section subtitle.
		$this->add_control(
			'blog_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		//blog category.
		$this->add_control(
			'blog_category',
			[
				'label' => __( 'Select Multiple Category :', 'construction-light' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => construction_light_categories(),
			]
		);

		//button text.
		$this->add_control(
			'blog_button',
			[
				'label' => __( 'Enter Button Text :', 'construction-light' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//blog layout.
		$this->add_control(
			'blog_layout',
			[
				'label'       => esc_html__( 'Number Of Blog Post To Display :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'three',
				'options' => 
				[
					'three'  => esc_html__( 'Three Blog Posts', 'construction-light' ),
					'six'   => esc_html__( 'Six Blog Posts', 'construction-light' ),
				],
			]
		);

		$this->end_controls_section();

		//section title style.
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


		// columns
		$this->add_control(
			'columns',
			[
				'label'       => esc_html__( 'Columns', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 3,

				'options' => 
				[
					'1'    => esc_html__( 'Single Column', 'construction-light' ),
					'2'    => esc_html__( 'Two Column', 'construction-light' ),
					'3'  => esc_html__( 'Three Column', 'construction-light' ),
					'4'   => esc_html__( 'Four Column', 'construction-light' ),
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

		$settings       = $this->get_settings_for_display();
		$blog_title     = $settings['blog_title'];
		$blog_subtitle  = $settings['blog_subtitle'];
		$blog_category  = $settings['blog_category'];
		$blog_button    = $settings['blog_button'];
		$blog_columns    = $settings['columns'];
		$blog_columns = 12 / intval($blog_columns);

		?>
		<section class="cons_light_blog-list-area">
            <div class="container">

                <?php construction_light_section_title( $blog_title, $blog_subtitle ); ?>

                <div class="row">
                    <?php
                    
                        $blog_layout = $settings['blog_layout'];

                        if ($blog_layout == 'three') {

                            $post_num = 3;

                        } else {

                            $post_num = 6;

                        }

                        $args = array(
                            'posts_per_page' => $post_num,
                            'post_type' => 'post',
                            'category__in'=> $blog_category
                        );

                        $blog_query = new \WP_Query($args);

                        if ( $blog_query->have_posts() ): 

                        	while ( $blog_query->have_posts() ) :

                        		$blog_query->the_post();

			                    ?>
			                        <div class="col-lg-<?php echo esc_attr($blog_columns); ?> col-md-<?php echo esc_attr($blog_columns); ?> col-sm-12 articlesListing blog-grid">
			                            <article id="post-<?php the_ID(); ?>" <?php post_class('article'); ?>>
			                                <div class="blog-post-thumbnail">
			                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			                                        <?php the_post_thumbnail('construction-light-medium'); ?>
			                                    </a>
			                                </div>
			                                <div class="box">
			                                    <?php 

			                                        the_title( '<h3 class="title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); 

			                                        if ( 'post' === get_post_type() ){ do_action( 'construction_light_post_meta', 10 ); } 
			                                    ?>
			                                    
			                                    <div class="entry-content">
			                                        <?php the_excerpt(); ?>
			                                    </div>

			                                    <?php 
			                                    if (!empty($blog_button)):
				                                    ?>
				                                    <div class="btns text-center">
				                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">
				                                            <span><?php echo esc_html($blog_button); ?><i class="fas fa-arrow-right"></i></span>
				                                        </a>
				                                    </div>
			                                    <?php endif; ?>
			                                </div>

			                            </article><!-- #post-<?php the_ID(); ?> -->
			                        </div>
				           <?php endwhile; 
				        endif; ?>
                </div>
            </div>
        </section>
		<?php
	}



}