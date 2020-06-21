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
if (!function_exists('construction_light_widget_testimonial')) :

    function construction_light_widget_testimonial(){

        register_widget('construction_light_widget_testimonial');

    }

endif;
add_action('widgets_init', 'construction_light_widget_testimonial');

/*
* Testimonial Widget.
*/
if (!class_exists('construction_light_widget_testimonial')):
    class construction_light_widget_testimonial extends WP_Widget{

        private $defaults = array(

            'bg_image' => '',            
            'title' => '',
            'subtitle' => '',
            'page_id' => 0,
            'all_page_items' => '',            
            'designation' => '',
        );

        function __construct(){
            parent:: __construct( 
            	'construction_light_testimonial', //ID
				esc_html__('&nbsp;CL : Testimonial', 'construction-light'), //Name
				array('description' => esc_html__('Widget Displays Customer Reviews', 'construction-light'),) //args
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

            $testimonial_bg       = $instance['bg_image'];
            $testimonial_title    = $instance['title'];
            $testimonial_subtitle = $instance['subtitle'];
            $all_page_items   = $instance['all_page_items'];

            echo $args['before_widget']; ?>
            
                <section class="cons_light_testimonial" style="background-image:url(<?php echo esc_url( $testimonial_bg ); ?>);">
                    <div class="container">
                        <?php construction_light_section_title( $testimonial_title, $testimonial_subtitle ); ?>
                        <div class="row">
                            <div class="owl-carousel owl-theme testimonial_slider">
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
                                    <div class="item">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="client-img"><?php the_post_thumbnail('thumbnail'); ?></div>
                                            <?php the_excerpt(); ?>
                                            <div class="client-text">
                                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                                <h4><?php echo esc_html($page['designation']); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                <?php  endwhile; endif; endif; endforeach; endif; ?>
                            </div>
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

            $bg_image      = $instance['bg_image'];
            $title 			= $instance['title'];
            $subtitle 		= $instance['subtitle'];            
            $page_id 		= absint($instance['page_id']);
            $all_page_items = $instance['all_page_items'];
            $Designation 	= $instance['designation'];

            ?>

             <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('bg_image')); ?>">
                    <?php esc_html_e('Select Background Image', 'construction-light'); ?>:
                </label><br>

                <?php

                $construction_light_display_none = '';

                if (empty($bg_image)){ $construction_light_display_none = ' style="display:none;" '; }

                ?>
                <span class="img-preview-wrap" <?php echo wp_kses_post($construction_light_display_none); ?>>
                    <img class="widefat" src="<?php echo esc_url($bg_image); ?>" alt="<?php esc_html_e('Image preview', 'construction-light'); ?>"/>
                </span><!-- .img-preview-wrap -->

                <input type="hidden" class="widefat" name="<?php echo esc_attr($this->get_field_name('bg_image')); ?>" id="<?php echo esc_attr($this->get_field_id('bg_image')); ?>" value="<?php echo esc_url($bg_image); ?>"/>

                <input type="button" value="<?php esc_html_e('Upload Image', 'construction-light'); ?>" class="button media-image-upload" data-title="<?php esc_html_e('Select Background Image', 'construction-light'); ?>" data-button="<?php esc_html_e('Select Background Image', 'construction-light'); ?>"/>

                <input type="button" value="<?php esc_html_e('Remove Image', 'construction-light'); ?>"class="button media-image-remove"/>

            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Enter Testimonial Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                	<?php esc_html_e('Enter Testimonial Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>


            <div class="cl-repeater">

            	<label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Testimonial Settings', 'construction-light'); ?> :
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
                                    	<?php esc_html_e('Testimonial Page ', 'construction-light') ?>
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
                                	<?php esc_html_e('Testimonial Page', 'construction-light') ?>
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
                $add_field = esc_html__('Add Testimonial', 'construction-light');
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

            $instance['bg_image'] = isset($new_instance['bg_image']) ? esc_url_raw($new_instance['bg_image']) : '';
            
            $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

            $instance['subtitle'] = ! empty( $new_instance['subtitle'] ) ? sanitize_text_field( $new_instance['subtitle'] ) : '';

            $instance['all_page_items'] = $new_instance['all_page_items'];
            

            $page_ids = array();

            foreach ($new_instance['all_page_items'] as $key => $features) {

            	$page_ids[$key]['page_id'] = !empty( $features['page_id'] ) ? intval( $features['page_id'] ) : '';

            	$page_ids[$key]['designation'] = !empty( $features['designation'] ) ? sanitize_text_field( $features['designation'] ) : '';
               
            }


            $instance['all_page_items'] = $page_ids;

            return $instance;
        }
    }
endif; 