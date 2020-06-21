<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Construction Light
 */

$layout = get_theme_mod( 'construction_light_blogtemplate_layout', 'none' );

$post_sidebar =  esc_attr( get_theme_mod( 'construction_light_archive_sidebar','right' ) );

if ($post_sidebar == 'no') { 

    $colid = 12;

} elseif ($post_sidebar == 'left' || $post_sidebar == 'right'){

    $colid = 8;

} 

get_header(); ?>

<div class="container">
	<div class="row">

		<?php if( $post_sidebar == 'left' && is_active_sidebar('sidebar-2') ){ get_sidebar('left'); } ?>
		
		<div id="primary" class="content-area col-lg-<?php echo intval ( $colid ); ?> col-md-<?php echo intval ( $colid ); ?> col-sm-12 <?php echo esc_attr( $layout  ); ?>" data-layout="<?php echo esc_attr( $layout  ); ?>">
			<main id="main" class="site-main">
				<div class="articlesListing blog-grid">	
					<?php
						if ( have_posts() ) :


							if( !empty( $layout ) && $layout == 'masonry2-rsidebar'){

								echo '<div class="construction-masonry">';
							}

								/* Start the Loop */
								while ( have_posts() ) : the_post();

									/*
									 * Include the Post-Type-specific template for the content.
									 * If you want to override this in a child theme, then include a file
									 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
									 */

									// Post Display Layout
									if( !empty( $layout ) && $layout == 'masonry2-rsidebar' ){
										
										get_template_part( 'template-parts/content', 'masonry' );
			
									}else {

										get_template_part( 'template-parts/content', get_post_format() );

									}

								endwhile;

							if( !empty( $layout ) && $layout == 'masonry2-rsidebar'){

								echo '</div>';
							}

							the_posts_pagination( 
			            		array(
								    'prev_text' => esc_html__( 'Prev', 'construction-light' ),
								    'next_text' => esc_html__( 'Next', 'construction-light' ),
								)
				            );

						else :

							get_template_part( 'template-parts/content', 'none' );

						endif;
					?>
				</div><!-- Articales Listings -->

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php if( $post_sidebar == 'right' && is_active_sidebar('sidebar-1') ){ get_sidebar('right'); } ?>

	</div>
</div>

<?php get_footer();