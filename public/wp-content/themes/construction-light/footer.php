<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Construction Light
 */

?>

</div><!-- #content -->

<?php if (is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) || is_active_sidebar( 'footer-4' ) ): ?>
    
    <footer id="colophon" class="site-footer">
        <div class="container">
            <div class="row">
                <?php
                    $widget_count = 0;

                    for ($i = 1; $i <= 4; $i++) {

                        if (is_active_sidebar('footer-' . $i)) {
                            $widget_count++;
                        }
                    }

                    $widget_count = 12 / $widget_count;
                    $widget_class = 'col-md-' . absint($widget_count);

                    for ($i = 1; $i <= 4; $i++) {

                        if (is_active_sidebar('footer-' . $i)) {
                ?>
                    <div class="<?php echo esc_attr($widget_class); ?> col-sm-6 col-xs-12">
                        <?php dynamic_sidebar('footer-' . $i); ?>
                    </div>
                <?php } } ?>
            </div>

        </div>
    </footer><!-- #colophon -->
<?php endif; ?>

    <div class="sub_footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12 col-sm-12">
                    <div class="cons_light_copyright">
                        <?php apply_filters( 'construction_light_copyright', 5 ); ?> <?php the_privacy_policy_link(); ?>
                    </div><!-- Copyright -->
                </div>
                <div class="col-lg-5 col-md-12 col-sm-12 text-right">
                    <?php
                        construction_light_topheader_social();
                    ?>
                </div>
            </div>
        </div>
    </div>

</div><!-- #page -->


<div class="conslight-search-wrapper">
    <div class="conslight-search-close">
        <div class="conslight-close-icon">
            <i class="far fa-times-circle"></i>
        </div>
    </div>
    <div class="conslight-search-container">
        <?php get_search_form(); ?>
    </div>
</div>

<a href="#" id="back-to-top" class="progress" data-tooltip="Back To Top">
    <div class="arrow-top"></div>
    <div class="arrow-top-line"></div>
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="xMinYMin meet"> <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/></svg> 
</a>

<?php wp_footer(); ?>

</body>
</html>
