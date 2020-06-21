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
if (!function_exists('construction_light_widget_counter')) :

    function construction_light_widget_counter(){

        register_widget('construction_light_widget_counter');

    }

endif;
add_action('widgets_init', 'construction_light_widget_counter');

if (!class_exists( 'construction_light_widget_counter' ) ):

    class construction_light_widget_counter extends WP_Widget{

        private $defaults = array(
            'title'         => '',
            'subtitle'      => '',
            'bg_image'      => '',            
            'all_page_items'=> '',
            'icon_class'    => '',
            'counter_title' => '',
            'counter_num'   => ''

        );

        function __construct(){

            parent::__construct(
                'construction_light_counter', // Base ID
                esc_html__('&nbsp;CL : Counter', 'construction-light'), // Name
                array('description' => esc_html__('Widgeet Displays Success Business Awards Count', 'construction-light')) // Args
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

            $counter_title    = $instance['title'];
            $counter_subtitle = $instance['subtitle'];
            $counter_bg       = $instance['bg_image'];
            $all_page_items   = $instance['all_page_items'];

            echo $args['before_widget']; ?>
            
                <section class="cons_light_counter_wrap" style="background-image:url(<?php echo esc_url( $counter_bg ); ?>);">
                    <div class="container">

                        <?php construction_light_section_title( $counter_title, $counter_subtitle ); ?>

                        <div class="row cons_light_team-counter-wrap">
                            <?php
                                if (is_array($all_page_items) && count($all_page_items) > 0) :
                                $i = 1;
                                foreach ($all_page_items as $counter):
                            ?>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="cons_light_counter">
                                        <div class="cons_light_counter-icon">
                                            <i class="<?php echo esc_attr($counter['icon_class']); ?>"></i>
                                        </div>

                                        <div class="cons_light_counter-count odometer odometer<?php echo esc_attr($i); ?>" data-count="<?php echo absint($counter['counter_num']); ?>">
                                            
                                            99
                                        </div>
                                        <h6 class="cons_light_counter-title">
                                            <?php echo esc_html($counter['counter_title']); ?>
                                        </h6>
                                    </div>
                                </div>
                            <?php $i++; endforeach; endif; ?>                        
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
            $title         = $instance['title'];
            $subtitle      = $instance['subtitle'];
            $all_page_items= $instance['all_page_items'];
            $icon_class    = $instance['icon_class'];
            $counter_title = $instance['counter_title'];
            $counter_num   = $instance['counter_num'];

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

            <!-- Title -->

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                    <?php esc_html_e('Enter Counter Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <!-- Subtitle -->

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                    <?php esc_html_e('Enter Counter Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>

            <!-- Counter Repeater -->

            <div class="cl-repeater">

                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                    <?php esc_html_e('Manage Counter Settings', 'construction-light'); ?> :
                </label>

                <?php
                $repeater = 0;

                if (is_array($all_page_items) && count($all_page_items) > 0) {

                    foreach ($all_page_items as $counter) {

                        ?>
                        <div class="repeater-table">

                            <div class="cl-repeater-top">
                                <div class="cl-repeater-title-action">
                                    <button type="button" class="cl-repeater-action">
                                        <span class="cl-toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="cl-repeater-title">
                                    <h3><?php esc_html_e('Counter Settings', 'construction-light') ?><span class="in-cl-repeater-title"></span></h3>
                                </div>
                            </div>

                            <div class='cl-repeater-inside hidden'>

                                <!-- Counter Icon Class -->

                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('icon_class')); ?>">
                                        <?php esc_html_e('Enter Fontawesome Icon Class (Ex :- fas fa-truck-monster )', 'construction-light'); ?> :
                                        <a class="button button-link sp-postid" target="_blank" href="<?php echo esc_url('https://fontawesome.com/icons?d=gallery&m=free'); ?>"><?php esc_html_e('More Icon Class','construction-light'); ?></a>
                                    </label>

                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') .$repeater.'icon_class'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items'). '[' .$repeater. '][' . 'icon_class' . ']'); ?>" type="text" value="<?php echo esc_attr($counter['icon_class']); ?>"/>
                                </p>

                                <!-- Counter Title -->

                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('counter_title')); ?>">
                                        <?php esc_html_e('Enter Counter Title', 'construction-light'); ?> :
                                    </label>

                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') .$repeater.'counter_title'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items'). '[' .$repeater. '][' . 'counter_title' . ']'); ?>" type="text" value="<?php echo esc_attr($counter['counter_title']); ?>"/>
                                </p>

                                <!-- Counter Number -->

                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('counter_num')); ?>">
                                        <?php esc_html_e('Enter Counter Number', 'construction-light'); ?> :
                                    </label>

                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') .$repeater.'counter_num'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items'). '[' .$repeater. '][' . 'counter_num' . ']'); ?>" type="text" value="<?php echo esc_attr($counter['counter_num']); ?>"/>
                                </p>

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
                                    <?php esc_html_e('Counter Settings', 'construction-light') ?>
                                    <span class="in-cl-repeater-title"></span>
                                </h3>
                            </div>
                        </div>
                        <div class='cl-repeater-inside hidden'>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('icon_class')); ?>">
                                    <?php esc_html_e('Enter Fontawesome Icon Class (Ex :- fas fa-truck-monster )', 'construction-light'); ?> :
                                    <a class="button button-link sp-postid" target="_blank" href="<?php echo esc_url('https://fontawesome.com/icons?d=gallery&m=free'); ?>"><?php esc_html_e('More Icon Class','construction-light'); ?></a>
                                </label>
                                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'icon_class'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '['. $coder_repeater_depth .'][' . 'icon_class' . ']'); ?>" type="text" value=""/>
                            </p>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('counter_title')); ?>">
                                    <?php esc_html_e('Enter Counter Title', 'construction-light'); ?> :
                                </label>

                                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'counter_title'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '['. $coder_repeater_depth .'][' . 'counter_title' . ']'); ?>" type="text" value=""/>
                            </p>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('counter_num')); ?>">
                                    <?php esc_html_e('Enter Counter Number', 'construction-light'); ?> :
                                </label>

                                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'counter_num'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '['. $coder_repeater_depth .'][' . 'counter_num' . ']'); ?>" type="text" value=""/>
                            </p>


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

                </script>
                <?php
                echo '<input class="cl-total-repeater" type="hidden" value="' . wp_kses_post($repeater) . '">';
                $add_field = esc_html__('Add Counter', 'construction-light');
                echo '<span class="button-primary cl-add-repeater" id="' . wp_kses_post($coder_repeater_depth) . '">' . wp_kses_post($add_field) . '</span><br/>';
                ?>
            </div>

            <?php
        }

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

            $instance['bg_image'] = ! empty( $new_instance['bg_image'] ) ? esc_url_raw( $new_instance['bg_image'] ) : '';

            $instance['all_page_items'] = ! empty( $new_instance['all_page_items'] ) ?  $new_instance['all_page_items'] : '';

            $counters = array();

            foreach ($new_instance['all_page_items'] as $key => $counter) {

                $counters[$key]['icon_class'] = !empty( $counter['icon_class'] ) ? sanitize_text_field( $counter['icon_class'] ) : '';

                $counters[$key]['counter_title'] = !empty( $counter['counter_title'] ) ? sanitize_text_field( $counter['counter_title'] ) : '';

                $counters[$key]['counter_num'] = !empty( $counter['counter_num'] ) ? intval( $counter['counter_num'] ) : '';

            }

            $instance['all_page_items'] = $counters;

            return $instance;


         }

    }

endif;