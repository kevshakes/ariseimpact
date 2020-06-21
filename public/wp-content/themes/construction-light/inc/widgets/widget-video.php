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
if (!function_exists('construction_light_widget_calltoaction_video')) :

    function construction_light_widget_calltoaction_video(){

        register_widget('construction_light_widget_calltoaction_video');

    }

endif;
add_action('widgets_init', 'construction_light_widget_calltoaction_video');

/*
* Call To Action Video Widget.
*/
if (!class_exists('construction_light_widget_calltoaction_video')):

    class construction_light_widget_calltoaction_video extends WP_Widget{

        private $defaults = array(
            'bg_image'    => '',
            'title'       => '',
            'subtitle'    => '',
            'video_link'  => '',

        );

        function __construct(){

            parent::__construct(
                'construction_light_calltoaction_video', //ID
				esc_html__('&nbsp;CL : Video Call To Action', 'construction-light'), //Name
			    array('description' => esc_html__('Widget Displays Video Business Video', 'construction-light')) //args
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

            $video_image = $instance['bg_image'];
            $video_title = $instance['title'];
            $video_subtitle = $instance['subtitle'];
            $video_link = $instance['video_link'];

            echo $args['before_widget'];
            ?>

            <div class="calltoaction_promo_wrapper video_calltoaction" style="background-image:url(<?php echo esc_url( $video_image ); ?>);background-repeat:no-repeat;background-size:cover;background-attachment:fixed;background-position: center;">
                <div class="container">
                    <div class="video_calltoaction_wrap">
                        <a href="<?php echo esc_url( $video_link ); ?>" target="_blank" class="popup-youtube  box-shadow-ripples"><i class="fas fa-play "></i></a>
                    </div>

                    <div class="calltoaction_full_widget_content">
                        <h2 class="wow zoomIn"><?php echo esc_html( $video_title ); ?></h2>

                        <div class="calltoaction_subtitle wow zoomIn">
                            <p><?php echo esc_html( $video_subtitle ); ?></p>
                        </div>
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
            $bg_image     = $instance['bg_image'];
            $title        = $instance['title'];
            $subtitle     = $instance['subtitle'];
            $video_link   = $instance['video_link'];

            ?>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('bg_image')); ?>">
                    <?php esc_html_e('Upload Background Image', 'construction-light'); ?>:
                </label><br>
                <?php
                    $construction_light_display_none = '';

                    if (empty($bg_image)){ $construction_light_display_none = ' style="display:none;" '; }
                ?>
                <span class="img-preview-wrap" <?php echo wp_kses_post($construction_light_display_none); ?>>
                    <img class="widefat" src="<?php echo esc_url($bg_image); ?>" alt="<?php esc_html_e('Image preview', 'construction-light'); ?>"/>
                </span><!-- .img-preview-wrap -->
                <input type="hidden" class="widefat" name="<?php echo esc_attr($this->get_field_name('bg_image')); ?>" id="<?php echo esc_attr($this->get_field_id('bg_image')); ?>" value="<?php echo esc_url($bg_image); ?>"/>
                <input type="button" value="Upload Image" class="button media-image-upload" data-title="<?php esc_html_e('Select Background Image', 'construction-light'); ?>" data-button="<?php esc_html_e('Select Background Image', 'construction-light'); ?>"/>
                <input type="button" value="Remove Image" class="button media-image-remove"/>

            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Enter Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                    <?php esc_html_e('Enter Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('video_link')); ?>">
                    <?php esc_html_e('Enter Youtube Video Link', 'construction-light'); ?> :
                </label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('video_link')); ?>"name="<?php echo esc_attr($this->get_field_name('video_link')); ?>" type="text" value="<?php echo esc_attr($video_link); ?>"/>
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

            $instance['bg_image'] = isset($new_instance['bg_image']) ? esc_url_raw($new_instance['bg_image']) : '';

            $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

            $instance['subtitle'] = ! empty( $new_instance['subtitle'] ) ? sanitize_text_field( $new_instance['subtitle'] ) : '';

            $instance['video_link'] = ! empty( $new_instance['video_link'] ) ? esc_url_raw( $new_instance['video_link'] ) : '';



            return $instance;
        }

    }
endif; 