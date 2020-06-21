<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Construction Light
 * @since 1.0.0
 */

$postformat = get_post_format();

$post_description = get_theme_mod( 'construction_light_post_description_options', 'excerpt' );
$post_content_type 	= apply_filters( 'construction_light_content_type', $post_description );

if( function_exists( 'pll_register_string' ) ){ 

    $blogreadmore_btn = pll__( get_theme_mod( 'construction_light_blogtemplate_btn', 'Continue Reading' ) );

}else{ 

    $blogreadmore_btn = get_theme_mod( 'construction_light_blogtemplate_btn', 'Continue Reading' );

}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('article'); ?>>
	
	<?php construction_light_post_format_media( $postformat ); ?>

	<div class="box">
		<?php 

			the_title( '<h3 class="title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );

			if ( 'post' === get_post_type() ){ do_action( 'construction_light_post_meta', 10 ); } 
			
		?>

		<div class="entry-content">
			<?php
				if ( 'excerpt' === $post_content_type ) {

					the_excerpt();

				} elseif ( 'content' === $post_content_type ) {

					the_content( sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'construction-light' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						get_the_title()
					) );
				}
			?>
		</div>
		
		<?php if ( 'excerpt' === $post_content_type ) { ?>
			<div class="btns text-center">
				<a href="<?php the_permalink(); ?>" class="btn btn-primary">
					<span><?php echo esc_html( $blogreadmore_btn ); ?><i class="fas fa-arrow-right"></i></span>
				</a>
			</div>
		<?php } ?>
		
	</div>

</article><!-- #post-<?php the_ID(); ?> -->