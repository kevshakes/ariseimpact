<?php 
/**
 * Function to Register and load the widget About Us
 *
 * @since 1.0.0
 *
 * @param null
 *
 * @return null
 *
 */
if (!function_exists('construction_light_widget_team')) :

    function construction_light_widget_team(){

        register_widget('construction_light_widget_team');

    }

endif;
add_action('widgets_init', 'construction_light_widget_team');

/*
* Team Widget.
*/
if (!class_exists('construction_light_widget_team')):
    class construction_light_widget_team extends WP_Widget{

        private $defaults = array(

            'page_id' => 0,
            'all_page_items' => '',
            'title' => '',
            'subtitle' => '',            
            'designation' => '',
            'facebook' => '',
            'twitter' => '',
            'instagram' => '',

        );

        function __construct(){
            parent:: __construct( 
            	'construction_light_team', //ID
				esc_html__('&nbsp;CL : Our Team Member', 'construction-light'), //Name
				array('description' => esc_html__('Widget Displays Our Team Members', 'construction-light'),) //args
            );
        }

        /**
         * Function to Creating widget front-end. This is where the action happens
         *
         * @access public
         * @since 1.0.0
         *
         * @param array $args widget setting
         * @param array $instance saved values
         *
         * @return void
         *
         */
        public function widget($args, $instance){

            $instance = wp_parse_args((array)$instance, $this->defaults);

            $team_title    = $instance['title'];
            $team_subtitle = $instance['subtitle'];
            $all_page_items= $instance['all_page_items'];

            echo $args['before_widget'];
            ?>
                <section class="cons_light_team_layout_two layout_one">
                    <div class="container">
                        <?php construction_light_section_title( $team_title, $team_subtitle ); ?>
                        <div class="row">
                            <?php
                                $page_args = array();

                                $post_in = array();

                                if ( is_array($all_page_items) && count($all_page_items) > 0 ):

                                foreach ($all_page_items as $page):

                                if (isset($page['page_id']) && !empty($page['page_id'])):

                                $page_args = array(
                                    'page_id' => $page['page_id'],
                                    'orderby' => 'page_id',
                                    'post_type' => 'page',
                                    'no_found_rows' => true,
                                    'post_status' => 'publish'
                                );

                                $testimonial_query = new WP_Query($page_args);

                                if ($testimonial_query->have_posts()): while ($testimonial_query->have_posts()): $testimonial_query->the_post();
                            ?>
                                <div class="col-lg-4 col-md-6 col-sm-12">
                                    <div class="box">
                                        <figure><?php the_post_thumbnail('construction-light-team'); ?></figure>
                                        <div class="team-wrap">
                                            <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                            <?php if (!empty($page['designation'])): ?>
                                                <span><?php echo esc_html( $page['designation'] ); ?></span>
                                            <?php endif; ?>
                                            <?php the_excerpt(); ?>
                                            <ul class="sp_socialicon">
                                                <?php if (!empty($page['facebook']) ) : ?>
                                                    <li><a href="<?php echo esc_url($page['facebook'] ); ?>"><i class="fab fa-facebook-f"></i></a></li>
                                                <?php endif; 

                                                if (!empty($page['twitter']) ) : ?>
                                                    <li><a href="<?php echo esc_url($page['twitter']); ?>"><i class="fab fa-twitter"></i></a></li>
                                                <?php endif;
                                                if (!empty($page['instagram']) ) : ?>
                                                    <li><a href="<?php echo esc_url($page['instagram']); ?>"><i class="fab fa-instagram"></i></a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; endif; endif; endforeach; endif; ?>
                        </div>
                    </div>
                </section>
            <?php
            echo $args['after_widget'];
        }

        /**
         * Widget Backend
         */
        public function form($instance){

            $instance = wp_parse_args((array)$instance, $this->defaults);

            $title 			= $instance['title'];
            $subtitle 		= $instance['subtitle'];            
            $page_id 		= absint($instance['page_id']);
            $all_page_items = $instance['all_page_items'];
            $Designation 	= $instance['designation'];
            $facebook 		= $instance['facebook'];
            $twitter 		= $instance['twitter'];
            $instagram 		= $instance['instagram'];

            ?>
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Enter Team Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                	<?php esc_html_e('Enter Team Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>


            <div class="cl-repeater">

            	<label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Our Team Member Settings', 'construction-light'); ?> :
                </label>
                <?php
                $repeater = 0;

                if (is_array($all_page_items) && count($all_page_items) > 0) {

                    foreach ($all_page_items as $features) {

                        $repeater_id = $this->get_field_id('all_page_items') . $repeater . 'page_id';

                        $repeater_name = $this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'page_id' . ']';
                        ?>
                        <div class="repeater-table">

                            <div class="cl-repeater-top">
                                <div class="cl-repeater-title-action">
                                    <button type="button" class="cl-repeater-action">
                                        <span class="cl-toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="cl-repeater-title">
                                    <h3>
                                    	<?php esc_html_e('Team Page ', 'construction-light') ?>
	                                    <span class="in-cl-repeater-title"></span>
	                                </h3>
                                </div>
                            </div>

                            <div class='cl-repeater-inside hidden'>
                                <?php
                                    /* see more here https://codex.wordpress.org/Function_Reference/wp_dropdown_pages*/
                                    $args = array(
                                        'selected'         => $features['page_id'],
                                        'name' 			   => $repeater_name,
                                        'id' 			   => $repeater_id,
                                        'class' 		   => 'widefat cl-select',
                                        'show_option_none' => esc_html__('Select Page', 'construction-light'),
                                        'option_none_value' => 0 // string
                                    );
                                    wp_dropdown_pages($args);
                                ?>
                                <div class="fb">

                                    <!-- Designation -->
                                    <p>
                                        <label for="<?php echo esc_attr($this->get_field_id('designation')); ?>">
                                        	<?php esc_html_e('Enter Designation', 'construction-light'); ?> :
                                        </label>

                                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'designation'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'designation' . ']'); ?>" type="text" value="<?php echo esc_attr($features['designation']); ?>"/>
                                    </p>

                                    <!-- Facebook -->
                                    <p>
                                        <label for="<?php echo esc_attr($this->get_field_id('facebook')); ?>">
                                        	<?php esc_html_e('Enter Facebook URL', 'construction-light'); ?> :
                                        </label>

                                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'facebook'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'facebook' . ']'); ?>" type="text" value="<?php echo esc_attr($features['facebook']); ?>"/>
                                    </p>

                                    <!-- Twitter -->
                                    <p>
                                        <label for="<?php echo esc_attr($this->get_field_id('twitter')); ?>">
                                        	<?php esc_html_e('Enter Twitter Url', 'construction-light'); ?> :
                                        </label>

                                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'twitter'); ?>"  name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'twitter' . ']'); ?>" type="text" value="<?php echo esc_attr($features['twitter']); ?>"/>
                                    </p>

                                    <!-- Instagram -->
                                    <p>
                                        <label for="<?php echo esc_attr($this->get_field_id('instagram')); ?>">
                                        	<?php esc_html_e('Enter Instagram Url', 'construction-light'); ?> :
                                        </label>

                                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'instagram'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'instagram' . ']'); ?>" type="text" value="<?php echo esc_attr($features['instagram']); ?>"/>
                                    </p>

                                </div>
                                <div class="cl-repeater-control-actions">

                                    <button type="button" class="button-link button-link-delete cl-repeater-remove">
                                    	<?php esc_html_e('Remove', 'construction-light'); ?>
                                    </button>
                                    |
                                    <button type="button" class="button-link cl-repeater-close">
                                    	<?php esc_html_e('Close', 'construction-light'); ?>
                                    </button>

                                    <a class="button button-link cl-postid alignright hidden" target="_blank" data-href="<?php echo esc_url(admin_url('post.php?post=POSTID&action=edit')); ?>" href=""></a>
                                </div>
                            </div>
                        </div>
                        <?php
                        $repeater = $repeater + 1;
                    }
                }
                $coder_repeater_depth = 'coderRepeaterDepth_' . '0';

                $repeater_id = $this->get_field_id('all_page_items') . $coder_repeater_depth . 'page_id';

                $repeater_name = $this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'page_id' . ']';

                ?>
                <script type="text/html" class="cl-code-for-repeater">
                    <div class="repeater-table">

                        <div class="cl-repeater-top">
                            <div class="cl-repeater-title-action">
                                <button type="button" class="cl-repeater-action">
                                    <span class="cl-toggle-indicator" aria-hidden="true"></span>
                                </button>
                            </div>

                            <div class="cl-repeater-title">
                                <h3>
                                	<?php esc_html_e('Team Page', 'construction-light') ?>
                                	<span class="in-cl-repeater-title"></span>
                                </h3>
                            </div>
                        </div>
                        <div class='cl-repeater-inside hidden'>
                            <?php
                                /* see more here https://codex.wordpress.org/Function_Reference/wp_dropdown_pages*/
                                $args = array(
                                    'selected'		 	=> '',
                                    'name' 				=> $repeater_name,
                                    'id' 				=> $repeater_id,
                                    'class'				=> 'widefat cl-select',
                                    'show_option_none'  => esc_html__('Select Page', 'construction-light'),
                                    'option_none_value' => 0 // string
                                );
                                wp_dropdown_pages($args);
                            ?>
                            <div class="fb">

                                <!-- Designation -->
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('designation')); ?>">
                                    	<?php esc_html_e('Enter Designation', 'construction-light'); ?> :
                                    </label>
                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'designation'); ?>"  name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'designation' . ']'); ?>" type="text" value=""/>
                                </p>

                                <!-- Facebook -->
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('facebook')); ?>">
                                    	<?php esc_html_e('Enter Facebook URL', 'construction-light'); ?>  :
                                    </label>
                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'facebook'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'facebook' . ']'); ?>" type="text" value=""/>
                                </p>

                                <!-- Twitter -->
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('twitter')); ?>">
                                    	<?php esc_html_e(' Enter Twitter Url', 'construction-light'); ?>  :
                                    </label>
                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'twitter'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'twitter' . ']'); ?>" type="text" value=""/>
                                </p>

                                <!-- Instagram -->
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('instagram')); ?>">
                                    	<?php esc_html_e(' Enter Instagram Url', 'construction-light'); ?> :
                                    </label>

                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'instagram'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'instagram' . ']'); ?>" type="text" value=""/>
                                </p>

                            </div>
                            <div class="cl-repeater-control-actions">

                                <button type="button" class="button-link button-link-delete cl-repeater-remove">
                                	<?php esc_html_e('Remove', 'construction-light'); ?>
                               </button>
                                |
                                <button type="button" class="button-link cl-repeater-close">
                                	<?php esc_html_e('Close', 'construction-light'); ?>
                                </button>

                                <a class="button button-link cl-postid alignright hidden" target="_blank"data-href="<?php echo esc_url(admin_url('post.php?post=POSTID&action=edit')); ?>" href=""></a>
                            </div>
                        </div>
                    </div>

                </script>
                <?php
                echo '<input class="cl-total-repeater" type="hidden" value="' . wp_kses_post($repeater) . '">';
                $add_field = esc_html__('Add Team', 'construction-light');
                echo '<span class="button-primary cl-add-repeater" id="' . wp_kses_post($coder_repeater_depth) . '">' . wp_kses_post($add_field) . '</span><br/>';
                ?>
            </div>

        <?php }

        /**
         * Function to Updating widget replacing old instances with new
         *
         * @access public
         * @since 1.0.0
         *
         * @param array $new_instance new arrays value
         * @param array $old_instance old arrays value
         *
         * @return array
         *
         */
        public function update($new_instance, $old_instance){

            $instance = $old_instance;

            $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

            $instance['subtitle'] = ! empty( $new_instance['subtitle'] ) ? sanitize_text_field( $new_instance['subtitle'] ) : '';

            $instance['all_page_items'] = $new_instance['all_page_items'];
            

            $page_ids = array();

            foreach ($new_instance['all_page_items'] as $key => $features) {

            	$page_ids[$key]['page_id'] = !empty( $features['page_id'] ) ? intval( $features['page_id'] ) : '';

            	$page_ids[$key]['designation'] = !empty( $features['designation'] ) ? sanitize_text_field( $features['designation'] ) : '';

            	$page_ids[$key]['facebook'] = !empty( $features['facebook'] ) ? esc_url_raw( $features['facebook'] ) : '';

            	$page_ids[$key]['twitter'] = !empty( $features['twitter'] ) ? esc_url_raw( $features['twitter'] ) : '';

            	$page_ids[$key]['instagram'] = !empty( $features['instagram'] ) ? esc_url_raw( $features['instagram'] ) : '';
               
            }


            $instance['all_page_items'] = $page_ids;

            return $instance;
        }

    }
endif; //construction_light_widget_team