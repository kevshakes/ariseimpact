<?php
/**
 * The template for displaying search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Construction Light
 */

$post_sidebar =  get_theme_mod( 'construction_light_search_sidebar','right' );

if ($post_sidebar == 'no') { 

    $colid = 12;

} elseif ($post_sidebar == 'left' || $post_sidebar == 'right'){

    $colid = 8;

} 

get_header(); ?>

<div class="container">
	<div class="row">

		<?php if( $post_sidebar == 'left' && is_active_sidebar('sidebar-2') ){ get_sidebar('left'); } ?>
		
		<div id="primary" class="content-area col-lg-<?php echo intval ( $colid ); ?> col-md-<?php echo intval ( $colid ); ?> col-sm-12>
			<main id="main" class="site-main">
				<div class="articlesListing blog-grid">	

					<?php if ( have_posts() ) : ?>
						<header class="page-header">
							<h2 class="page-title">
								<?php
									/* translators: %s: search query. */
									printf( esc_html__( 'Search Results for: %s', 'construction-light' ), '<span>' . get_search_query() . '</span>' );
								?>
							</h2>
						</header><!-- .page-header -->

						<?php
							/* Start the Loop */
							while ( have_posts() ) : the_post();

								/*
								 * Include the Post-Type-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
								 */
								get_template_part( 'template-parts/content', 'search' );

							endwhile;

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