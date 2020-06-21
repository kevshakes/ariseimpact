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
if (!function_exists('construction_light_widget_aboutus')) :

    function construction_light_widget_aboutus(){
        register_widget('construction_light_widget_aboutus');

    }

endif;
add_action('widgets_init', 'construction_light_widget_aboutus');

/*
* About Us Widget.
*/
if (!function_exists('construction_light_about_content')):
    function construction_light_about_content()
    {
        $construction_light_about_content = array(
            'excerpt' 	   => esc_html__('Excerpt', 'construction-light'),
            'full_content' => esc_html__('Full Content', 'construction-light')
        );

        return apply_filters('construction_light_about_content', $construction_light_about_content);
    }
endif;

if (!class_exists('construction_light_widget_aboutus')):

	class construction_light_widget_aboutus extends WP_Widget{

		private $defaults = array(
            'page_id'     => '',
            'content'     => '',
            'button'      => '',
            'email'       => '',
            'contact_number'=> '',
            'all_page_items' => '',
            'award_title'    => '',
            'award_num'      => ''
        );

        function __construct(){

            parent::__construct(
            	'construction_light_aboutus', // Base ID
            	esc_html__('&nbsp;CL : About Us', 'construction-light'), // Name
            	 array('description' => esc_html__('Widget Displays About Us with some Details', 'construction-light')) // Args
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

            $instance      = wp_parse_args((array)$instance, $this->defaults);

            $about_page    = $instance['page_id'];
            $about_content = $instance['content'];
            $about_button  = $instance['button'];
            $about_email   = $instance['email'];
            $about_phone   = $instance['contact_number'];
            $phone_number = preg_replace("/[^0-9]/","",$about_phone);
            $all_page_items = $instance['all_page_items'];

            echo $args['before_widget'];
            ?>
            <section class="about_us_front">
                <div class="container">
                    <div class="row">
                        <?php

                        $aboutus_args = array(
                            'page_id' => $about_page,
                            'posts_per_page' => 1,
                        );

                        $aboutus_query = new WP_Query($aboutus_args);

                        if ($aboutus_query->have_posts()) :

                           while ($aboutus_query->have_posts()) :

                            $aboutus_query->the_post();

                                $about_col = '';

                                if( !empty( $about_image ) ){

                                    $about_col = 7;

                                }else{

                                    $about_col = 12;

                                }

                                ?>

                                <div class="col-lg-5 col-md-5 col-sm-12">
                                    <?php the_post_thumbnail('construction-light-aboutus'); ?>
                                </div>

                                <div class="col-lg-7 col-md-7 col-sm-12">

                                    <h3><?php the_title(); ?></h3>
                                    
                                    <?php
                                        if ( $about_content == 'excerpt') {

                                            the_excerpt();

                                        } else {

                                            the_content();
                                        } 
                                    
                                    if( !empty( $about_email ) || !empty( $about_phone ) ){
                                    ?>
                                        <div class="address-info">
                                            <ul>
                                                <?php if( !empty( $about_email ) ){ ?>

                                                    <li><?php esc_html_e('Email Us :','construction-light'); ?>
                                                        <a href="mailto:'<?php echo esc_attr( antispambot( $about_email ) ); ?>"><?php echo esc_html($about_email); ?></a>
                                                    </li>

                                                <?php } if( !empty( $about_phone ) ){ ?>

                                                    <li><?php esc_html_e('Contact Us :','construction-light'); ?>
                                                        <a href="tel:'<?php echo esc_attr( $phone_number ); ?>"><?php echo esc_html( $about_phone ); ?></a>
                                                    </li>

                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>

                                    <?php
                                        if ( !empty( $about_button ) && $about_content == 'excerpt') {
                                    ?>
                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php echo esc_html($about_button); ?><i class="fas fa-arrow-right"></i></a>
                                    <?php } ?>

                                    <div class="achivement-items">
                                        <ul>
                                            <?php
                                            if (is_array($all_page_items) && count($all_page_items) > 0) :

                                                foreach ($all_page_items as $awards):
                                                    ?>
                                                    <li>
                                                        <div class="timer achivement"><?php echo intval($awards['award_num']); ?></div>
                                                        <span class="medium"><?php echo esc_html($awards['award_title']); ?></span>
                                                    </li>
                                                <?php endforeach;
                                            endif; ?>
                                        </ul>
                                    </div>

                                </div>
                            <?php endwhile;
                        endif; ?>

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

        	$page_id  = $instance['page_id'];
            $content  = $instance['content'];
            $button   = $instance['button'];
            $email    = $instance['email'];
            $contact_number = $instance['contact_number'];
            $all_page_items = $instance['all_page_items'];
            $award_title    = $instance['award_title'];
            $award_num      = $instance['award_num'];

            ?>
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('page_id')); ?>">
                	<?php esc_html_e('Select AboutUs Page', 'construction-light'); ?> :
                </label>
                <br/>
                <?php
                /* see more here https://codex.wordpress.org/Function_Reference/wp_dropdown_pages*/
                $args = array(
                    'selected' => $page_id,
                    'name' => $this->get_field_name('page_id'),
                    'id' => $this->get_field_id('page_id'),
                    'class' => 'widefat',
                    'show_option_none' => esc_html__('Select Page', 'construction-light'),
                    'option_none_value' => 0 // string
                );
                wp_dropdown_pages($args);
                ?>
            </p>

            <!-- Content Option -->
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('content')); ?>">
                	<?php esc_html_e('Content Options', 'construction-light'); ?> :
                </label>

                <select class="widefat" id="<?php echo esc_attr($this->get_field_id('content')); ?>" name="<?php echo esc_attr($this->get_field_name('content')); ?>">

                    <?php
                   		$about_content = construction_light_about_content();

	                    foreach ($about_content as $key => $value){ echo '<option value = "'.esc_attr($key).'"'.selected($key, $content).'>'.esc_html($value).'</option>'; } 
	                ?>
                </select>
            </p>

            <!-- Contact Number -->
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('contact_number')); ?>">
                	<?php esc_html_e('Enter About Us Phone Number', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('contact_number')); ?>" name="<?php echo esc_attr($this->get_field_name('contact_number')); ?>" type="text" value="<?php echo esc_attr($contact_number); ?>"/>
            </p>

            <!-- Email Address -->
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('email')); ?>">
                	<?php esc_html_e('Enter About Us Email Address', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('email')); ?>" name="<?php echo esc_attr($this->get_field_name('email')); ?>" type="text" value="<?php echo esc_attr($email); ?>"/>
            </p>

            <!-- Button Text -->
            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('button')); ?>">
                	<?php esc_html_e('Enter Button Text', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button')); ?>" name="<?php echo esc_attr($this->get_field_name('button')); ?>" type="text" value="<?php echo esc_attr($button); ?>"/>
            </p>

            <div class="cl-repeater">

            	<label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Achivement Awards Settings', 'construction-light'); ?> :
                </label>

                <?php
                $repeater = 0;

                if (is_array($all_page_items) && count($all_page_items) > 0) {

                    foreach ($all_page_items as $award) {

                        ?>
                        <div class="repeater-table">

                            <div class="cl-repeater-top">
                                <div class="cl-repeater-title-action">
                                    <button type="button" class="cl-repeater-action">
                                        <span class="cl-toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                </div>
                                <div class="cl-repeater-title">
                                    <h3><?php esc_html_e('Achivement Award Settings', 'construction-light') ?><span class="in-cl-repeater-title"></span></h3>
                                </div>
                            </div>

                            <div class='cl-repeater-inside hidden'>

                                <!-- Award Title -->
                                <?php 
                                    $award_title_id = $this->get_field_id('all_page_items') .$repeater.'award_title';
                            		$award_title_name = $this->get_field_name('all_page_items'). '[' .$repeater. '][' . 'award_title' . ']';
                                ?>
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('award_title')); ?>"><?php esc_html_e('Enter Award Title', 'construction-light'); ?>
                                        :</label>
                                    <input class="widefat" id="<?php echo esc_attr($award_title_id); ?>" name="<?php echo esc_attr($award_title_name); ?>" type="text" value="<?php echo esc_attr($award['award_title']); ?>"/>
                                </p>

                                <!-- Award Number -->
                                <?php 
                                    $award_num_id = $this->get_field_id('all_page_items') .$repeater.'award_num';
                            		$award_num_name = $this->get_field_name('all_page_items'). '[' .$repeater. '][' . 'award_num' . ']';
                                ?>
                                <p>
                                    <label for="<?php echo esc_attr($this->get_field_id('award_num')); ?>">
                                    	<?php esc_html_e('Enter Award Number', 'construction-light'); ?>  :
                                    </label>

                                    <input class="widefat" id="<?php echo esc_attr($award_num_id); ?>" name="<?php echo esc_attr($award_num_name); ?>" type="text" value="<?php echo esc_attr($award['award_num']); ?>"/>
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

                $coder_award_title_id = $this->get_field_id('all_page_items') . $coder_repeater_depth . 'award_title';

                $coder_award_title_name = $this->get_field_name('all_page_items') . '['. $coder_repeater_depth .'][' . 'award_title' . ']';

                $coder_award_num_id = $this->get_field_id('all_page_items') . $coder_repeater_depth . 'award_num';

                $coder_award_num_name = $this->get_field_name('all_page_items') . '['. $coder_repeater_depth .'][' . 'award_num' . ']';

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
                                	<?php esc_html_e('Award Achivement Settings', 'construction-light') ?>
                                	<span class="in-cl-repeater-title"></span>
                                </h3>
                            </div>
                        </div>
                        <div class='cl-repeater-inside hidden'>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('award_title')); ?>">
                                	<?php esc_html_e('Enter Award Title', 'construction-light'); ?> :
                                </label>
                                <input class="widefat" id="<?php echo esc_attr($coder_award_title_id); ?>" name="<?php echo esc_attr($coder_award_title_name); ?>" type="text" value=""/>
                            </p>

                            <p>
                                <label for="<?php echo esc_attr($this->get_field_id('award_num')); ?>">
                                	<?php esc_html_e('Enter Award Number', 'construction-light'); ?> :
                                </label>

                                <input class="widefat" id="<?php echo esc_attr($coder_award_num_id); ?>" name="<?php echo esc_attr($coder_award_num_name); ?>" type="text" value=""/>
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
                $add_field = esc_html__('Add Award Achivements', 'construction-light');
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

        	$instance['page_id'] = ! empty( $new_instance['page_id'] ) ? absint( $new_instance['page_id'] ) : '';

        	$instance['content'] = ! empty( $new_instance['content'] ) ? sanitize_text_field( $new_instance['content'] ) : '';

        	$instance['email'] = ! empty( $new_instance['email'] ) ? sanitize_text_field( $new_instance['email'] ) : '';

        	$instance['contact_number'] = ! empty( $new_instance['contact_number'] ) ? sanitize_text_field( $new_instance['contact_number'] ) : '';

        	$instance['button'] = ! empty( $new_instance['button'] ) ? sanitize_text_field( $new_instance['button'] ) : '';

        	$instance['all_page_items'] = ! empty( $new_instance['all_page_items'] ) ? $new_instance['all_page_items'] : '';

            
            $award_achivement = array();

            foreach ($new_instance['all_page_items'] as $key => $awards) {

            	$award_achivement[$key]['award_title'] = !empty( $awards['award_title'] ) ? sanitize_text_field( $awards['award_title'] ) : '';

            	$award_achivement[$key]['award_num'] = !empty( $awards['award_num'] ) ? intval( $awards['award_num'] ) : '';

            }

            $instance['all_page_items'] = $award_achivement;

            return $instance;

        }

	}

endif;