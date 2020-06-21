<?php 
function construction_light_portfolio() {
    register_widget( 'construction_light_portfolio' );
}
add_action( 'widgets_init', 'construction_light_portfolio' );

/*
* Portfolio Widget.
*/
if (!class_exists('construction_light_portfolio')):
	class construction_light_portfolio extends WP_Widget {

		private $defaults = array(
            'title'      => '',
			'subtitle'   => '',
			'postCats'   => ''
        );

	   function __construct() { 
	        parent:: __construct( 'construction_light_portfolio', //Base ID
            	esc_html__('&nbsp;CL : Portfolio', 'construction-light'), //Name
				array('description' => esc_html__('Widget Displays Success Portfolio', 'construction-light'),) //args
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
	    public function widget( $args, $instance ) {
	    	$instance = wp_parse_args((array)$instance, $this->defaults);

	    	$title    = $instance['title'];
            $subtitle = $instance['subtitle'];
            $cons_light_portfolio_cat = $instance['postCats'];

		    echo $args['before_widget'];

		    ?>
		    <section class="cons_ligcons_light_portfolio-section clearfix">
            	<div class="container">

            		<?php construction_light_section_title( $title, $subtitle ); ?>
                    
                    <?php
                        if($cons_light_portfolio_cat){
                        $cons_light_portfolio_cat_array = explode(',', $cons_light_portfolio_cat) ;
                    ?>  
                        <div class="cons_light_portfolio-cat-name-list">
                            <div class="cons_light_portfolio-cat-name active" data-filter="*"><?php echo esc_html_e('All Works','construction-light'); ?></div>
                            <?php 
                                foreach ($cons_light_portfolio_cat_array as $cons_light_portfolio_cat_single) {

                                    $category_slug = "";
                                    $category_slug = get_category_by_slug($cons_light_portfolio_cat_single);

                                    if( is_object($category_slug)){

                                    $category_slug = 'portfolio-'.$category_slug->term_id;

                                    ?>
                                    <div class="cons_light_portfolio-cat-name" data-filter=".<?php echo esc_attr($category_slug); ?>">
                                        <?php echo esc_html($cons_light_portfolio_cat_single); ?>
                                    </div>

                            <?php } } ?>
                        </div>
                    <?php } ?>

                    <div class="cons_light_portfolio-post-wrap clearfix">
                        <div class="cons_light_portfolio-posts clearfix">
                            <?php 
                                if($cons_light_portfolio_cat){

                                $portfolio_args = array( 
                                    'posts_per_page' => -1,
                                    'post_type' => 'post',
                                    'tax_query' => array(

                                        array(
                                            'taxonomy' => 'category',
                                            'field' => 'slug',
                                            'terms' => $cons_light_portfolio_cat_array
                                        ),
                                    ), 
                                );
                                $query = new WP_Query($portfolio_args);

                                if($query->have_posts()): while($query->have_posts()) : $query->the_post(); 

                                    $categories = get_the_category();
                                    $category_slug = "";
                                    $cat_slug = array();

                                foreach ($categories as $category) {
                                    $cat_slug[] = 'portfolio-'.$category->term_id;
                                }

                                $category_slug = implode(" ", $cat_slug);

                                if(has_post_thumbnail()){
                                    $image_url = get_template_directory_uri().'/assets/images/portfolio-small-blank.png';
                                    $cons_light_image = wp_get_attachment_image_src(get_post_thumbnail_id(),'construction-light-portfolio');    
                                    $cons_light_image_large = wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
                                }else{
                                    $image_url = get_template_directory_uri().'/assets/images/portfolio-small.png';
                                    $cons_light_image = "";
                                }

                            ?>
                                <div class="cons_light_portfolio <?php echo esc_attr($category_slug); ?>">
                                    <div class="cons_light_portfolio-outer-wrap">
                                        <div class="cons_light_portfolio-wrap" style="background-image: url(<?php echo esc_url( $cons_light_image[0] ) ?>);">
                                        
                                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php esc_attr(get_the_title()); ?>">

                                            <div class="cons_light_portfolio-caption">

                                                <h3><?php the_title(); ?></h3>

                                                <a class="cons_light_portfolio-link" href="<?php echo esc_url(get_permalink()); ?>"><i class="fa fa-link"></i></a>
                                                
                                                <?php if(has_post_thumbnail()){ ?>
                                                    <a class="cons_light_portfolio-image"  href="<?php echo esc_url( $cons_light_image_large[0] ) ?>"><i class="fa fa-search"></i></a>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; endif; wp_reset_postdata(); } ?>
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
		public function form( $instance ) {

			$instance       = wp_parse_args((array)$instance, $this->defaults);

            $title          = $instance['title'];
            $subtitle       = $instance['subtitle'];
		    $instance['postCats'] = !empty($instance['postCats']) ? explode(",",$instance['postCats']) : array();
		    ?>

		    <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Enter Portfolio Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                    <?php esc_html_e('Enter Portfolio Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('subtitle')); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>

		    <p>
		        <label class="cons_light_title" for="<?php echo esc_attr( $this->get_field_id( 'postCats' ) ); ?>"><?php esc_html_e( 'Select Categories you want to show:','construction-light' ); ?></label><br />
		        <?php $args = array(
		                'post_type' => 'post',
		                'taxonomy' => 'category',
		            );
		            $terms = get_terms( $args );
		        //print_r($terms);
		         foreach( $terms as $id => $name ) { 
		            $checked = "";
		            if(in_array($name->name,$instance['postCats'])){
		                $checked = "checked='checked'";
		            }
		        ?>
		            <input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('postCats') ); ?>" name="<?php echo esc_attr( $this->get_field_name('postCats[]') ); ?>" value="<?php echo esc_attr( $name->name ); ?>"  <?php echo esc_attr( $checked ); ?>/>
		            <label for="<?php echo esc_attr( $this->get_field_id('postCats') ); ?>"><?php echo esc_html( $name->name ); ?></label><br />
		        <?php } ?>
		    </p>

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
		public function update( $new_instance, $old_instance ) {
		    $instance = $old_instance;

		    $instance['title'] = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';

            $instance['subtitle'] = ! empty( $new_instance['subtitle'] ) ? sanitize_text_field( $new_instance['subtitle'] ) : '';
            
		    $instance['postCats'] = !empty($new_instance['postCats']) ? implode(",",$new_instance['postCats']) : 0;
		    return $instance;
		}
		
	}
endif;