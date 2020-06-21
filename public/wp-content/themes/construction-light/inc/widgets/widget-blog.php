<?php 
function construction_light_blogs() {
    register_widget( 'construction_light_blogs' );
}
add_action( 'widgets_init', 'construction_light_blogs' );

/*
* Blog Widget.
*/
if (!class_exists('construction_light_blogs')):

	class construction_light_blogs extends WP_Widget {

		private $defaults = array(
            'title'      => '',
			'subtitle'   => '',
			'blogcats'   => ''
        );

	   function __construct() { 
	        parent:: __construct( 'construction_light_blogs', //Base ID
            	esc_html__('&nbsp;CL : Our Blog Posts', 'construction-light'), //Name
				array('description' => esc_html__('Widget Displays Selected Category Blog Posts', 'construction-light'),) //args
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

			    $blog_title    = $instance['title'];
	            $blog_subtitle = $instance['subtitle'];
			    $blog = $instance[ 'blogcats' ];

			    $cat_id = explode(',', $blog);

		    echo $args['before_widget'];

		    ?>
		    <section class="cons_light_blog-list-area">
	            <div class="container">
	            	<?php construction_light_section_title( $blog_title, $blog_subtitle ); ?>
	                <div class="row">
	                    <?php
	                    
	                        $blog_args = array(
	                            'post_type' => 'post',
	                            'posts_per_page' => 6,
	                            'tax_query' => array(

	                                array(
	                                    'taxonomy' => 'category',
	                                    'field' => 'slug',
	                                    'terms' => $cat_id
	                                ),
	                            ),
	                        );

	                        $blog_query = new WP_Query ($blog_args);

	                        if ( $blog_query->have_posts() ): while ( $blog_query->have_posts() ) : $blog_query->the_post();

	                        	if( function_exists( 'pll_register_string' ) ){ 

		                            $blogreadmore_btn = pll__( get_theme_mod( 'construction_light_blogtemplate_btn', 'Continue Reading' ) );

		                        }else{ 

		                            $blogreadmore_btn = get_theme_mod( 'construction_light_blogtemplate_btn', 'Continue Reading' );

		                        }
	                   		?>
	                        <div class="col-lg-4 col-md-6 col-sm-12 articlesListing blog-grid">
	                            <article id="post-<?php the_ID(); ?>" <?php post_class('article'); ?>>
	                                <div class="blog-post-thumbnail">
	                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1"><?php the_post_thumbnail('construction-light-medium'); ?></a>
	                                </div>
	                                <div class="box">
	                                    <?php 
	                                        the_title( '<h3 class="title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); 
	                                        if ( 'post' === get_post_type() ){ do_action( 'construction_light_post_meta', 10 ); } 
	                                    ?>
	                                    <div class="entry-content">
	                                        <?php the_excerpt(); ?>
	                                    </div>
	                                    <div class="btns text-center">
	                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary"><span><?php echo esc_html( $blogreadmore_btn ); ?><i class="fas fa-arrow-right"></i></span></a>
	                                    </div>
	                                </div>
	                            </article><!-- #post-<?php the_ID(); ?> -->
	                        </div>
	                    <?php endwhile; endif; ?>
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
		    $instance['blogcats'] = !empty($instance['blogcats']) ? explode(",",$instance['blogcats']) : array();
		    ?>

		    <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                	<?php esc_html_e('Enter Blog Section Title', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
            </p>

            <p>
                <label class="cons_light_title" for="<?php echo esc_attr($this->get_field_id('subtitle')); ?>">
                    <?php esc_html_e('Enter Blog Section Subtitle', 'construction-light'); ?> :
                </label>

                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('subtitle') ); ?>" name="<?php echo esc_attr($this->get_field_name('subtitle')); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>"/>
            </p>

		    <p>
		        <label class="cons_light_title" for="<?php echo esc_attr( $this->get_field_id( 'blogcats' ) ); ?>"><?php esc_html_e( 'Select Categories you want to show:','construction-light' ); ?></label><br />
		        <?php
		         $args = array(
	                'post_type' => 'post',
	                'taxonomy' => 'category',
	            );

		        $terms = get_terms( $args );

		         foreach( $terms as $id => $name ) { 

		            $checked = "";

		            if(in_array($name->name,$instance['blogcats'])){

		                $checked = "checked='checked'";
		            }
		        ?>
		            <input type="checkbox" class="checkbox" id="<?php echo esc_attr( $this->get_field_id('blogcats') ); ?>" name="<?php echo esc_attr( $this->get_field_name('blogcats[]') ); ?>" value="<?php echo esc_attr($name->name); ?>"  <?php echo esc_attr( $checked ); ?>/>
		            
		            <label for="<?php echo esc_attr( $this->get_field_id('blogcats') ); ?>"><?php echo esc_html( $name->name ); ?></label><br />
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
            
		    $instance['blogcats'] = !empty($new_instance['blogcats']) ? implode(",",$new_instance['blogcats']) : 0;
		    return $instance;
		}

		
	}
endif;