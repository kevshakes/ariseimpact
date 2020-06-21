<?php
/**
 * The template for displaying  page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Construction Light
 */

global $post;

$post_sidebar = get_post_meta($post->ID, 'construction_light_page_layouts', true );

if(!$post_sidebar){
    $post_sidebar =  get_theme_mod( 'construction_light_page_sidebar','right' );
}

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
						while ( have_posts() ) :
							
							the_post();

							get_template_part( 'template-parts/content', 'page' );

							// If comments are open or we have at least one comment, load up the comment template.
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;

						endwhile; // End of the loop.
					?>
				</div><!-- Articales Listings -->
			</main><!-- #main -->
		</div><!-- #primary -->

		<?php if( $post_sidebar == 'right' && is_active_sidebar('sidebar-1') ){ get_sidebar('right'); } ?>
		
	</div>
</div>

<?php get_footer();