<?php
/**
 * Template Name: Construction Light - Full Width
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Construction Light
 */
get_header();  ?>

<div class="constructionlight_wrap">
	<?php

		while ( have_posts() ) : the_post();

		    the_content();

		endwhile; // End of the loop.
	?>
</div>

<?php get_footer();