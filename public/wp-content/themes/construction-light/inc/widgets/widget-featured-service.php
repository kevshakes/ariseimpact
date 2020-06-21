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
if (!function_exists('construction_light_widget_featured_service')) :

    function construction_light_widget_featured_service(){
        register_widget('construction_light_widget_featured_service');

    }
endif;
add_action('widgets_init', 'construction_light_widget_featured_service');

/*
* Featured Service Widget.
*/
if (!class_exists( 'construction_light_widget_featured_service' ) ):

    class construction_light_widget_featured_service extends WP_Widget {

        private $defaults = array(
            'page_id' => 0,
            'all_page_items' => '',           
            'icon_class' => '',
        );

        function __construct(){

            parent:: __construct( 'construction_light_featured_service', //Base ID
            	esc_html__('&nbsp;CL : Featured Service', 'construction-light'), //Name
				array('description' => esc_html__('Widget Displays Featured Service Area', 'construction-light'),) //args
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
            $all_page_items = $instance['all_page_items'];
            $page_id = absint($instance['page_id']);

            echo $args['before_widget'];

            ?>
            <section class="cons_light_feature">
                <div class="container">
                    <div class="row">
                        <?php
                            $page_args = array();

                            $post_in = array();

                            if (count($all_page_items) > 0 && is_array($all_page_items)):
                                
                            foreach ($all_page_items as $page):

                            if (isset($page['page_id']) && !empty($page['page_id'])):

                                $page_args = array(
                                    'page_id' => $page['page_id'],
                                    'orderby' => 'page_id',
                                    'post_type' => 'page',
                                    'no_found_rows' => true,
                                    'post_status' => 'publish'
                                );

                                $service_query = new WP_Query($page_args);

                                if ($service_query->have_posts()): while ($service_query->have_posts()): $service_query->the_post();
                        ?>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 feature-list">
                                <div class="box">
                                    <figure>
                                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('construction-light-medium'); ?></a>
                                    </figure>

                                    <div class="bottom-content">
                                        <?php if (isset( $page['icon_class'] ) && !empty( $page['icon_class'] ) ): ?>
                                            <div class="icon-box">
                                                <i class="<?php echo esc_attr($page['icon_class']); ?>"></i>
                                            </div>
                                        <?php endif; ?>

                                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                        <?php the_excerpt(); ?>
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
        	
            $instance       = wp_parse_args((array)$instance, $this->defaults);
         
            $page_id        = $instance['page_id'] ;
            $all_page_items = $instance['all_page_items'];
            $icon_class     = $instance['icon_class'];

            ?>
            <div class="cl-repeater">
                <?php
                    $repeater = 0;

                    if (is_array($all_page_items) && count($all_page_items) > 0) {

                    foreach ($all_page_items as $service) {

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
                                    <h3><?php esc_html_e('Featured Service Page ', 'construction-light') ?>
	                                    <span class="in-cl-repeater-title"></span>
	                                </h3>
                                </div>
                            </div>

                            <div class='cl-repeater-inside hidden'>
                                <?php
                                    /* see more here https://codex.wordpress.org/Function_Reference/wp_dropdown_pages*/
                                    $args = array(
                                        'selected' 			=> $service['page_id'],
                                        'name' 				=> $repeater_name,
                                        'id' 				=> $repeater_id,
                                        'class' 			=> 'widefat cl-select',
                                        'show_option_none' 	=> esc_html__('Select Page', 'construction-light'),
                                        'option_none_value' => 0 // string
                                    );
                                    wp_dropdown_pages($args);
                                ?>
                                <div class="fb">
                                    <p>
                                        <label for="<?php echo esc_attr($this->get_field_id('icon_class')); ?>">
                                        	<?php esc_html_e('Enter Fontawesome Icon Class (Ex :- fas fa-truck-monster )', 'construction-light'); ?>
                                            <a class="button button-link sp-postid" target="_blank" href="<?php echo esc_url('https://fontawesome.com/icons?d=gallery&m=free'); ?>"><?php esc_html_e('More Icon Class','construction-light'); ?></a>
                                        </label>
                                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'icon_class'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'icon_class' . ']'); ?>" type="text" value="<?php echo esc_attr($service['icon_class']); ?>"/>
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
                                <h3><?php esc_html_e('Featured Service Page', 'construction-light') ?>
	                                <span class="in-cl-repeater-title"></span>
	                            </h3>
                            </div>
                        </div>
                        <div class='cl-repeater-inside hidden'>
                            <?php
                                /* see more here https://codex.wordpress.org/Function_Reference/wp_dropdown_pages*/
                                $args = array(
                                    'selected' => '',
                                    'name' => $repeater_name,
                                    'id' => $repeater_id,
                                    'class' => 'widefat cl-select',
                                    'show_option_none' => esc_html__('Select Page', 'construction-light'),
                                    'option_none_value' => 0 // string
                                );
                                wp_dropdown_pages($args);
                            ?>
                            <div class="fb">
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('icon_class')); ?>">
                                    	<?php esc_html_e('Enter Fontawesome Icon Class (Ex :- fas fa-truck-monster )', 'construction-light'); ?>
                                        <a class="button button-link sp-postid" target="_blank" href="<?php echo esc_url('https://fontawesome.com/icons?d=gallery&m=free'); ?>"><?php esc_html_e('More Icon Class','construction-light'); ?></a>
                                    </label>
                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'icon_class'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'icon_class' . ']'); ?>" type="text" value=""/>
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
                            </div>
                        </div>
                    </div>

                </script>
                <?php
                    echo '<input class="cl-total-repeater" type="hidden" value="' . wp_kses_post($repeater) . '">';
                    $add_field = esc_html__('Add Featured Service Page', 'construction-light');
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

            $instance['all_page_items']  = ! empty( $new_instance['all_page_items'] ) ?  $new_instance['all_page_items'] : '';


            $service_page = array();

            foreach ($new_instance['all_page_items'] as $key => $services) {

            	$service_page[$key]['page_id'] = !empty( $services['page_id'] ) ? absint( $services['page_id'] ) : '';

            	$service_page[$key]['icon_class'] = !empty( $services['icon_class'] ) ? sanitize_text_field( $services['icon_class'] ) : '';

            }

            $instance['all_page_items'] = $service_page;

            return $instance;
        }

    }
endif; 