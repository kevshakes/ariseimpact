<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Construction Light
 */

$post_sidebar =  get_theme_mod( 'construction_light_blog_sidebar','right' );

if ($post_sidebar == 'no') { 

    $colid = 12;

} elseif ($post_sidebar == 'left' || $post_sidebar == 'right'){

    $colid = 8;

}

get_header(); ?>

<div class="container">
	<div class="row">

		<?php if( $post_sidebar == 'left' && is_active_sidebar('sidebar-2') ){ get_sidebar('left'); } ?>

		<div id="primary" class="content-area col-lg-<?php echo intval ( $colid ); ?> col-md-<?php echo intval ( $colid ); ?> col-sm-12">
			<main id="main" class="site-main">
				<div class="articlesListing">	
					<?php
						if ( have_posts() ) :

							/* Start the Loop */
							while ( have_posts() ) :
								the_post();

								/*
								 * Include the Post-Type-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
								 */
								get_template_part( 'template-parts/content', get_post_format() );

							endwhile;

					?>	
								
							<div class="prevNextArticle box">
								<div class="row">
									<div class="col-sm-6">
										<?php previous_post_link( '%link', '<div class="hoverExtend active prev"><span>'.esc_html__('Previous article','construction-light').'</span></div><div class="title prev">%title</div>' ); ?>
									</div>
									<div class="col-sm-6">
										<?php next_post_link( '%link', '<div class="hoverExtend active next"><span>'.esc_html__('Next article','construction-light').'</span></div><div class="title next">%title</div>' ); ?>
									</div>
								</div>
							</div><!-- Previous / next article -->

						<?php
							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;

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
