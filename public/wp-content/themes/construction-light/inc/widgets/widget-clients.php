<?php 
/**
 * Function to Register and load the widget Service
 *
 * @since 1.0.0
 *
 * @param null
 *
 * @return null
 *
 */
if (!function_exists('construction_light_widget_clients')) :

    function construction_light_widget_clients(){

        register_widget('construction_light_widget_clients');

    }

endif;
add_action('widgets_init', 'construction_light_widget_clients');

/*
* Clients Widget.
*/

if (!class_exists('construction_light_widget_clients')):

    class construction_light_widget_clients extends WP_Widget{

        private $defaults = array(

            'all_page_items' => '',
            'title'    => '',
            'subtitle' => '',
            'image'    => '',
            'link'     => '',
        );

        function __construct(){
            parent:: __construct(

            'construction_light_clients', //ID
             esc_html__('&nbsp;CL : Clients Brand Logo', 'construction-light'), //Name
             array('description' => esc_html__('Widget Displays Client Brand Logo', 'construction-light'),)//args
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

            $client_title    = $instance['title'];
            $client_subtitle = $instance['subtitle'];
            $all_page_items = $instance['all_page_items'];

            echo $args['before_widget'];

            ?>
            <section class="cons_light_client_logo_layout_two">
                <div class="container">
                    <?php construction_light_section_title( $client_title, $client_subtitle ); ?>
                    <div class="row">
                        <div class="owl-carousel owl-theme client_logo">
                            <?php
                                if (is_array($all_page_items) && count($all_page_items) > 0) :
                                foreach ($all_page_items as $image):
                            ?>
                                <div class="item">
                                    <div class="box">
                                        <a href="<?php echo esc_url($image['link']); ?>"><img src="<?php echo esc_url($image['image']); ?>" alt="image"></a>
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
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
        public function form($instance) {
            $instance = wp_parse_args((array)$instance, $this->defaults);

            $all_page_items = $instance['all_page_items'];
            $title    = $instance['title'];
            $subtitle = $instance['subtitle'];
            $image    = $instance['image'];
            $link     = $instance['link'];

            ?>
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Enter Client Section Title', 'construction-light'); ?> :
                </label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                	<?php esc_html_e('Enter Client Section Subitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>"  name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>

            <div class="cl-repeater">

            	<label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                	<?php esc_html_e('Client Settings', 'construction-light'); ?> :
                </label>
                <?php
                $repeater = 0;

                if (is_array($all_page_items) && count($all_page_items) > 0) {

                    foreach ($all_page_items as $features) {

                        $repeater_id = $this->get_field_id('all_page_items') . $repeater . 'image';

                        $repeater_name = $this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'image' . ']';
                        ?>
                        <div class="repeater-table">

                            <div class="cl-repeater-top">
                                <div class="cl-repeater-title-action">
                                    <button type="button" class="cl-repeater-action">
                                        <span class="cl-toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="cl-repeater-title">
                                    <h3><?php esc_html_e('Client', 'construction-light') ?><span
                                                class="in-cl-repeater-title"></span></h3>
                                </div>
                            </div>

                            <div class='cl-repeater-inside hidden'>

                                <!-- Image -->
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('image')); ?>">
                                        <?php esc_html_e('Upload Image', 'construction-light'); ?>:
                                    </label><br>

                                    <span class="img-preview-wrap">
						                    <img class="widefat" src="<?php echo esc_attr($features['image']); ?>" />
						                </span><!-- .img-preview-wrap -->

                                    <input type="hidden" class="widefat" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'image' . ']'); ?>" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'image'); ?>" value="<?php echo esc_attr($features['image']); ?>"/>

                                    <input type="button" value="<?php esc_html_e('Upload Image', 'construction-light'); ?>" class="button media-image-upload"data-title="<?php esc_html_e('Upload Client Image', 'construction-light'); ?>" data-button="<?php esc_html_e('Select Background Image', 'construction-light'); ?>"/>

                                    <input type="button" value="<?php esc_html_e('Remove Image', 'construction-light'); ?>" class="button media-image-remove"/>

                                </p>

                                <!-- link -->
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('link')); ?>">
                                    	<?php esc_html_e('Enter Client Link', 'construction-light'); ?> :
                                    </label>

                                    <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $repeater . 'link'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $repeater . '][' . 'link' . ']'); ?>" type="text" value="<?php echo esc_attr($features['link']); ?>"/>
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

                $repeater_id = $this->get_field_id('all_page_items') . $coder_repeater_depth . 'image';

                $repeater_name = $this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'image' . ']';

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
                                <h3><?php esc_html_e('Client', 'construction-light') ?>
	                                <span class="in-cl-repeater-title"></span>
	                            </h3>
                            </div>
                        </div>
                        <div class='cl-repeater-inside hidden'>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('image')); ?>">
                                    <?php esc_html_e('Upload Image', 'construction-light'); ?>:
                                </label><br>

                                <span class="img-preview-wrap">
				                    <img class="widefat" src="<?php echo esc_url($image); ?>"/>
				                </span>

                                <input type="hidden" class="widefat" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'image' . ']'); ?>" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'image'); ?>" value=" "/>

                                <input type="button" value="Upload Image" class="button media-image-upload" data-title="<?php esc_html_e('Select Client Image', 'construction-light'); ?>" data-button="<?php esc_html_e('Select Client Image', 'construction-light'); ?>"/>

                                <input type="button" value="Remove Image" class="button media-image-remove"/>

                            </p>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('link')); ?>">
                                	<?php esc_html_e('Enter Client Link', 'construction-light'); ?> :
                                </label>

                                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('all_page_items') . $coder_repeater_depth . 'link'); ?>" name="<?php echo esc_attr($this->get_field_name('all_page_items') . '[' . $coder_repeater_depth . '][' . 'link' . ']'); ?>" type="text" value=""/>
                            </p>


                            <div class="cl-repeater-control-actions">
                                <button type="button" class="button-link button-link-delete cl-repeater-remove">
                                	<?php esc_html_e('Remove', 'construction-light'); ?>
                               </button>
                                |
                                <button type="button" class="button-link cl-repeater-close">
                                	<?php esc_html_e('Close', 'construction-light'); ?>
                                </button>

                                <a class="button button-link cl-postid alignright hidden" target="_blank"data-href="<?php echo esc_url(admin_url('post.php?post=POSTID&action=edit')); ?>"  href=""></a>
                            </div>
                        </div>
                    </div>

                </script>
                <?php
                echo '<input class="cl-total-repeater" type="hidden" value="' . wp_kses_post($repeater) . '">';
                $add_field = esc_html__('Add Client', 'construction-light');
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

            $instance['all_page_items']  = ! empty( $new_instance['all_page_items'] ) ?  $new_instance['all_page_items'] : '';


            $clients = array();

            foreach ($new_instance['all_page_items'] as $key => $features) {

                $clients[$key]['image'] = $features['image'];
                $clients[$key]['link']  = $features['link'];
            }


            $instance['all_page_items'] = $clients;

            return $instance;
        }

    }
endif; //construction_light_widget_clients