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
if (!function_exists('construction_light_widget_calltoaction')) :

    function construction_light_widget_calltoaction()
    {
        register_widget('construction_light_widget_calltoaction');

    }

endif;
add_action('widgets_init', 'construction_light_widget_calltoaction');

/*
* Call To Action Video Widget.
*/
if (!class_exists('construction_light_widget_calltoaction')):

    class construction_light_widget_calltoaction extends WP_Widget{

        private $defaults = array(
            'calltoaction_image'       => '',
            'calltoaction_title'       => '',
            'calltoaction_subtitle'    => '',
            'calltoaction_button1'     => '',
            'calltoaction_button1_url' => '',
            'calltoaction_button2'     => '',
            'calltoaction_button2_url' => '',

        );

        function __construct(){

            parent::__construct(
                'construction_light_calltoaction', //ID
				esc_html__('&nbsp;CL : Call To Action', 'construction-light'), //Name
			    array('description' => esc_html__('Widget Displays Business Promotion Area', 'construction-light')) //args
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
        public function widget($args, $instance)
        {

            $instance = wp_parse_args((array)$instance, $this->defaults);

            $calltoaction             = $instance['calltoaction_image'];
            $calltoaction_title       = $instance['calltoaction_title'];
            $calltoaction_subtitle    = $instance['calltoaction_subtitle'];
            $calltoaction_button1     = $instance['calltoaction_button1'];
            $calltoaction_button1_url = $instance['calltoaction_button1_url'];
            $calltoaction_button2     = $instance['calltoaction_button2'];
            $calltoaction_button2_url = $instance['calltoaction_button2_url'];

            echo $args['before_widget']; ?>
                <div class="calltoaction_promo_wrapper" style="background-image:url(<?php echo esc_url( $calltoaction ); ?>);background-repeat:no-repeat;background-size:cover;background-attachment:fixed;background-position: center;">
                    <div class="container">
                        <div class="calltoaction_full_widget_content">

                            <h2 class="wow zoomIn"><?php echo esc_html( $calltoaction_title ); ?></h2>

                            <div class="calltoaction_subtitle wow zoomIn">
                                <p><?php echo esc_html( $calltoaction_subtitle ); ?></p>
                            </div>
                        </div>

                        <div class="calltoaction_button_wrap">
                            <a href="<?php echo esc_url( $calltoaction_button1_url ); ?>" class="btn btn-primary wow fadeInLeft"><?php echo esc_html( $calltoaction_button1 ); ?> <i class="fas fa-arrow-right"></i></a> <a href="<?php echo esc_url( $calltoaction_button2_url ); ?>" class="btn btn-border wow fadeInRight"><?php echo esc_html( $calltoaction_button2 ); ?> <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php
            
            echo $args['after_widget'];
        }


        /**
         * Widget Backend
         */
        public function form($instance){

            $instance = wp_parse_args((array)$instance, $this->defaults);
            
            $calltoaction_image         = $instance['calltoaction_image'];
            $calltoaction_title         = $instance['calltoaction_title'];
            $calltoaction_subtitle      = $instance['calltoaction_subtitle'];
            $calltoaction_button1       = $instance['calltoaction_button1'];
            $calltoaction_button1_url   = $instance['calltoaction_button1_url'];
            $calltoaction_button2       = $instance['calltoaction_button2'];
            $calltoaction_button2_url   = $instance['calltoaction_button2_url'];

            ?>
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_image')); ?>">
                    <?php esc_html_e('Select Background Image', 'construction-light'); ?>:
                </label><br>

                <?php

                $construction_light_display_none = '';

                if (empty($calltoaction_image)){ $construction_light_display_none = ' style="display:none;" '; }

                ?>
                <span class="img-preview-wrap" <?php echo wp_kses_post($construction_light_display_none); ?>>
                    <img class="widefat" src="<?php echo esc_url($calltoaction_image); ?>" alt="<?php esc_html_e('Image preview', 'construction-light'); ?>"/>
                </span><!-- .img-preview-wrap -->

                <input type="hidden" class="widefat" name="<?php echo esc_attr($this->get_field_name('calltoaction_image')); ?>" id="<?php echo esc_attr($this->get_field_id('calltoaction_image')); ?>" value="<?php echo esc_url($calltoaction_image); ?>"/>

                <input type="button" value="<?php esc_html_e('Upload Image', 'construction-light'); ?>" class="button media-image-upload" data-title="<?php esc_html_e('Select Background Image', 'construction-light'); ?>" data-button="<?php esc_html_e('Select Background Image', 'construction-light'); ?>"/>

                <input type="button" value="<?php esc_html_e('Remove Image', 'construction-light'); ?>"class="button media-image-remove"/>

            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_title')); ?>">
                	<?php esc_html_e('Enter Call To Action Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calltoaction_title')); ?>" name="<?php echo esc_attr($this->get_field_name('calltoaction_title')); ?>" type="text" value="<?php echo esc_attr($calltoaction_title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_subtitle')); ?>">
                	<?php esc_html_e('Enter Call To Action Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calltoaction_subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('calltoaction_subtitle')); ?>" type="text" value="<?php echo esc_attr($calltoaction_subtitle); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_button1')); ?>">
                	<?php esc_html_e('Enter Button One Text', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calltoaction_button1')); ?>" name="<?php echo esc_attr($this->get_field_name('calltoaction_button1')); ?>" type="text" value="<?php echo esc_attr($calltoaction_button1); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_button1_url')); ?>">
                	<?php esc_html_e('Enter Button One Link', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calltoaction_button1_url')); ?>" name="<?php echo esc_attr($this->get_field_name('calltoaction_button1_url')); ?>" type="text" value="<?php echo esc_attr($calltoaction_button1_url); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_button2')); ?>">
                	<?php esc_html_e('Enter Button Two Text', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calltoaction_button2')); ?>" name="<?php echo esc_attr($this->get_field_name('calltoaction_button2')); ?>" type="text" value="<?php echo esc_attr($calltoaction_button2); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('calltoaction_button2_url')); ?>">
                	<?php esc_html_e('Enter Button Two Link', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('calltoaction_button2_url')); ?>" name="<?php echo esc_attr($this->get_field_name('calltoaction_button2_url')); ?>" type="text" value="<?php echo esc_attr($calltoaction_button2_url); ?>"/>
            </p>

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

            $instance['calltoaction_image'] = isset($new_instance['calltoaction_image']) ? esc_url_raw($new_instance['calltoaction_image']) : '';

            $instance['calltoaction_title'] = ! empty( $new_instance['calltoaction_title'] ) ? sanitize_text_field( $new_instance['calltoaction_title'] ) : '';

            $instance['calltoaction_subtitle'] = ! empty( $new_instance['calltoaction_subtitle'] ) ? sanitize_text_field( $new_instance['calltoaction_subtitle'] ) : '';

            $instance['calltoaction_button1'] = ! empty( $new_instance['calltoaction_button1'] ) ? sanitize_text_field( $new_instance['calltoaction_button1'] ) : '';

            $instance['calltoaction_button1_url'] = ! empty( $new_instance['calltoaction_button1_url'] ) ? esc_url_raw( $new_instance['calltoaction_button1_url'] ) : '';

            $instance['calltoaction_button2'] = ! empty( $new_instance['calltoaction_button2'] ) ? sanitize_text_field( $new_instance['calltoaction_button2'] ) : '';

            $instance['calltoaction_button2_url'] = ! empty( $new_instance['calltoaction_button2_url'] ) ? esc_url_raw( $new_instance['calltoaction_button2_url'] ) : '';

            return $instance;
        }

    }
endif; 