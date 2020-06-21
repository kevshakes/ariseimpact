<?php
/**
 * Construction Light Team.
 *
 * @package    SparkleThemes
 * @subpackage ConstructionLight
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if it is accessed directly
}

class Construction_Light_Team extends Widget_Base{

	/**
	 * Retrieve Construction_Light_Team widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'construction-light-team';
	}

	/**
	 * Retrieve Construction_Light_Team widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Team', 'construction-light' );
	}

	/**
	 * Retrieve Construction_Light_Team widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-group';
	}

	/**
	 * Retrieve the list of categories the Construction_Light_Team widget belongs to.
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
				'label' => __( 'Team', 'construction-light' ),
			]
		);

		//Team section title.
		$this->add_control(
			'team_title',
			[
				'label'     => esc_html__( 'Enter Section Title :', 'construction-light' ),
				'type'      => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//Team section subtitle.
		$this->add_control(
			'team_subtitle',
			[
				'label'     => esc_html__( 'Enter Section Subitle :', 'construction-light' ),
				'type'      => Controls_Manager::TEXTAREA,
				'label_block' => true,
			]
		);

		$this->add_control(
			'team_layout',
			[
				'label'       => esc_html__( 'Team Layout', 'construction-light' ),
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
        	'team_page',
        	[
				'label'       => esc_html__( 'Select Page :', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => construction_light_pages(),
			]
		);

		//Elementery Designation
		$repeater->add_control(
			'team_designation',
			[
				'label'       => esc_html__( 'Enter Designation :', 'construction-light' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		//Elementery facebook
		$repeater->add_control(
			'team_facebook',
			[
				'label'       => esc_html__( 'Enter Facebook URL :', 'construction-light' ),
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

		//Elementery twitter
		$repeater->add_control(
			'team_twitter',
			[
				'label'       => esc_html__( 'Enter Twitter URL :', 'construction-light' ),
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

		//Elementery Instagram
		$repeater->add_control(
			'team_instagram',
			[
				'label'       => esc_html__( 'Enter Instagram URL :', 'construction-light' ),
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
				'label'  => esc_html__( 'Team :', 'construction-light' ),
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

		// columns
		$this->add_control(
			'columns',
			[
				'label'       => esc_html__( 'Columns', 'construction-light' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => 'three',

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

        $settings  = $this->get_settings_for_display();

        $team_title    = $settings['team_title'];
        $team_subtitle = $settings['team_subtitle'];
        $team_layout   = $settings['team_layout'];
		$columns   = $settings['columns'];
		$columns = 12 / intval($columns);


        //($settings );
        $list      = $settings['list'];

        ?>
        <section class="cons_light_team_layout_two <?php echo esc_attr( $team_layout ); ?>">
            <div class="container">
                
                <?php construction_light_section_title( $team_title, $team_subtitle ); ?>

                <div class="row">
                    <?php

                    if (!empty( $list ) ):

                        foreach ($list as $item):
                        
	                        $team_page =  $item['team_page'];

                            if (!empty( $team_page )):

	                            $team_query = new \WP_Query('page_id=' . $team_page);

	                            if ($team_query->have_posts()):
	                            	while ($team_query->have_posts()): 
	                            		$team_query->the_post();

				                    	?>
				                        <div class="col-lg-<?php echo esc_attr($columns); ?> col-md-6 col-sm-12">
				                            <div class="box">
				                                <figure>
				                                    <?php
				                                        if( !empty( $team_layout ) && $team_layout == 'layout_two') {

				                                            the_post_thumbnail('thumbnail');

				                                        } else {

				                                            the_post_thumbnail('construction-light-team');

				                                        }
				                                    ?>
				                                </figure>

				                                <div class="team-wrap">

				                                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

				                                    <?php if (!empty($item['team_designation']) ): ?>

				                                        <span><?php echo esc_html($item['team_designation']); ?></span>

				                                    <?php endif; ?>

				                                    <?php the_excerpt(); 

				                                    $facebook = $item['team_facebook']['url'];
				                                    $facebook_target = $item['team_facebook']['is_external'] ? ' target="_blank"' : '';
				                                    $facebook_nofollow = $item['team_facebook']['nofollow'] ? ' rel="nofollow"' : '';

													$twitter = $item['team_twitter']['url'];
				                                    $twitter_target = $item['team_twitter']['is_external'] ? ' target="_blank"' : '';
				                                    $twitter_nofollow = $item['team_twitter']['nofollow'] ? ' rel="nofollow"' : '';

													$instagram = $item['team_instagram']['url'];
				                                    $instagram_target = $item['team_instagram']['is_external'] ? ' target="_blank"' : '';
				                                    $instagram_nofollow = $item['team_instagram']['nofollow'] ? ' rel="nofollow"' : '';

				                                    ?>

				                                    <ul class="sp_socialicon">

				                                        <?php 
				                                        if (!empty($facebook) ) : 

				                                        	echo '<li><a href="'.esc_url($facebook).'" '.$facebook_target. $facebook_nofollow. '><i class="'.esc_attr("fab fa-facebook-f").'"></i></a></li>';
														endif; 

				                                        if (!empty( $twitter ) ) : 

				                                        	echo '<li><a href="'.esc_url($twitter).'" '.$twitter_target. $twitter_nofollow.'><i class="'.esc_attr("fab fa-twitter").'"></i></a></li>';

				                                        endif;

				                                        if (!empty( $instagram ) ) : 

				                                        	echo '<li><a href="'.esc_url($instagram).'" '.$instagram_target. $instagram_nofollow.'><i class="'.esc_attr("fab fa-instagram").'"></i></a></li>';

				                                        endif; ?>

				                                    </ul>

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