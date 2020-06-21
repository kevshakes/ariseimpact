<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Sparkle Themes
 *
 * @subpackage Construction Light
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'construction_light_section_title' ) ){
    /**
     * Section Main Title
     *
     * @since 1.0.0
     */
    function construction_light_section_title( $title, $sub_title ) { 

        if( !empty( $title ) || !empty( $sub_title ) ){ ?>
            <div class="row">

                <div class="col-lg-12 col-sm-12 col-xs-12">

                    <?php if( !empty( $title ) ){ if( function_exists( 'pll_register_string' ) ){ ?>

                        <h2 class="section-title"><?php echo esc_html( pll__( $title ) ); ?></h2>

                    <?php }else{ ?>

                        <h2 class="section-title"><?php echo esc_html( $title ); ?></h2>

                    <?php } } if( !empty( $sub_title ) ){ if( function_exists( 'pll_register_string' ) ){ ?>

                          <div class="section-tagline"><?php echo esc_html( pll__( $sub_title ) ); ?></div>

                    <?php }else{ ?>

                            <div class="section-tagline"><?php echo esc_html( $sub_title ); ?></div>

                    <?php } } ?>

                </div>

            </div>
        <?php }
    }
}


if ( ! function_exists( 'construction_light_post_meta' ) ){
    /**
     * Post Meta Function
     *
     * @since 1.0.0
     */
    function construction_light_post_meta() { 
        
        $postdate    = get_theme_mod( 'construction_light_post_date_options', 'enable' );
        $postcomment = get_theme_mod( 'construction_light_post_comments_options', 'enable' );
        $postauthor  = get_theme_mod( 'construction_light_post_author_options', 'enable' );

      ?>

        <div class="entry-meta info">
            <?php
                if( !empty( $postdate ) && $postdate == 'enable' ) { construction_light_posted_on(); }
                if( !empty( $postauthor ) && $postauthor == 'enable' ) { construction_light_posted_by(); }
                if( !empty( $postcomment ) && $postcomment == 'enable' ) { construction_light_comments(); }
            ?>
        </div><!-- .entry-meta -->

       <?php
    }
}
add_action( 'construction_light_post_meta', 'construction_light_post_meta' , 10 );


if( ! function_exists( 'construction_light_post_format_media' ) ) :

    /**
     * Posts format declaration function.
     *
     * @since 1.0.0
     */
    function construction_light_post_format_media( $postformat ) {

        if( is_singular( ) ){

          $imagesize = 'post-thumbnail';

        }else{

            $imagesize = '';
        }

        if( $postformat == "gallery" ) {

            $gallery                = get_post_gallery( get_the_ID(), false );
            $gallery_attachment_ids = explode( ',', $gallery['ids'] );
            
            if( is_array( $gallery ) ){ ?>

                <div class="postgallery-carousel cS-hidden">
                    <?php foreach ( $gallery_attachment_ids as $gallery_attachment_id ) { ?>
                        <div class="list">
                            <?php echo wp_get_attachment_image( $gallery_attachment_id, $imagesize ); // WPCS xss ok. ?>
                        </div>
                    <?php } ?>
                </div>

            <?php }else{  ?>

                <div class="blog-post-thumbnail">
                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                        <?php
                          the_post_thumbnail( $imagesize );
                        ?>
                    </a>
                </div>

        <?php } }else if( $postformat == "video" ){
            
            $get_content  = apply_filters( 'the_content', get_the_content() );
            $get_video    = get_media_embedded_in_content( $get_content, array( 'video', 'object', 'embed', 'iframe' ) );

            if( !empty( $get_video ) ){ ?>

                <div class="video">
                    <?php echo $get_video[0]; // WPCS xss ok. ?>
                </div>

        <?php }else{ ?>

                <div class="blog-post-thumbnail">
                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                        <?php
                          the_post_thumbnail( $imagesize );
                        ?>
                    </a>
                </div>

        <?php  } }else if( $postformat == "audio" ){

            $get_content  = apply_filters( 'the_content', get_the_content() );
            $get_audio    = get_media_embedded_in_content( $get_content, array( 'audio', 'iframe' ) );

            if( !empty( $get_audio ) ){ ?>

                <div class="audio">
                    <?php echo $get_audio[0]; // WPCS xss ok. ?>
                </div>

        <?php }else{  ?>

                <div class="blog-post-thumbnail">
                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                        <?php
                          the_post_thumbnail( $imagesize );
                        ?>
                    </a>
                </div>

        <?php } }else if( $postformat == "quote" ) { ?>

                <div class="post-format-media-quote">
                    <blockquote>
                        <?php the_content(); ?>
                    </blockquote>
                </div>

        <?php }else{ ?>

                <div class="blog-post-thumbnail">
                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                        <?php
                          the_post_thumbnail( $imagesize );
                        ?>
                    </a>
                </div>

        <?php }

    }

endif;


if ( ! function_exists( 'construction_light_footer_copyright' ) ){

    /**
     * Footer Copyright Information
     *
     * @since 1.0.0
     */
    function construction_light_footer_copyright() {

        echo esc_html( apply_filters( 'construction_light_copyright_text', $content = esc_html__('Copyright  &copy; ','construction-light') . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) .' - ' ) );

         printf( ' WordPress Theme : by %1$s', '<a href=" ' . esc_url('https://sparklewpthemes.com/') . ' " rel="designer" target="_blank">'.esc_html__('Sparkle Themes','construction-light').'</a>' );
    }
}
add_action( 'construction_light_copyright', 'construction_light_footer_copyright', 5 );



if (! function_exists( 'construction_light_quick_contact' ) ):

	function construction_light_quick_contact(){ ?>
		<ul class="sp_quick_info">
        	<?php 
	        	$contact_num = get_theme_mod('construction_light_contact_num');
	            $number      = preg_replace('/\D+/','', $contact_num );
	            $email       = get_theme_mod('construction_light_email');
	            $location    = get_theme_mod('construction_light_address');
	        	

	        	if (!empty( $contact_num ) ):
	        ?>
                <li>
                	<a href="tel:<?php echo intval( $number ); ?>">
                		<i class="fas fa-mobile-alt"></i><?php echo esc_html( $contact_num ); ?>
                	</a>
                </li>

            <?php endif; if (!empty( $email ) ): ?>

                <li>
                	<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
                		<i class="fas fa-envelope"></i><?php echo esc_html( antispambot( $email ) ); ?>
                	</a>
                </li>

            <?php endif; if (!empty( $location ) ): ?>

                <li><i class="fas fa-marker"></i><?php echo esc_html( $location ); ?></li>

            <?php endif; ?>
            
        </ul>
		<?php
	}
endif;


if (! function_exists( 'construction_light_topheader_social' ) ):

	function construction_light_topheader_social(){

		$social_icon = get_theme_mod('construction_light_topheader_social');

        if (!empty( $social_icon ) ) :

            $social_icon = json_decode($social_icon);

         	echo '<ul class="sp_socialicon">';

	            foreach ($social_icon as $icon) { ?>

	                <li>
	                	<a href="<?php echo esc_url($icon->social_link); ?>"><i class="<?php echo esc_html($icon->topheader_icon); ?>"></i></a>
	                </li>
	               
	            <?php }

            echo '</ul>';

        endif;
	}
endif;


/**
 * Breadcrumbs Section.
*/
if (! function_exists( 'construction_light_breadcrumbs' ) ):

    function construction_light_breadcrumbs(){

        $breadcrumb_image = get_theme_mod('construction_light_breadcrumbs_image'); ?>

            <section class="breadcrumb" style="background-image: url(<?php echo esc_url($breadcrumb_image); ?>);">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12 col-sm-12 col-xs-12 breadcrumb_wrapper">
                            <?php
                                if (is_single() || is_page()) {

                                    the_title('<h2 class="entry-title">', '</h2>');

                                } elseif (is_archive()) {

                                    the_archive_title('<h2 class="page-title">', '</h2>');

                                } elseif (is_search()) { ?>

                                    <h2 class="page-title">
                                        <?php printf(esc_html__('Search Results for:', 'construction-light'), '%s', '<span>' . get_search_query() . '</span>'); ?>
                                    </h2>

                                <?php } elseif (is_404()) {

                                    echo '<h2 class="entry-title">' . esc_html('404 Error', 'construction-light') . '</h2>';

                                } elseif (is_home()) {

                                $page_for_posts_id = get_option('page_for_posts');
                                $page_title = get_the_title($page_for_posts_id);

                            ?>
                                    <h2 class="entry-title"><?php echo esc_html($page_title); ?></h2>

                            <?php }else{ ?>

                                    <h2 class="entry-title"><?php echo esc_html($page_title); ?></h2>

                            <?php } ?>

                                <nav id="breadcrumb" class="cp-breadcrumb">
                                    <?php
                                        breadcrumb_trail(array(
                                            'container' => 'div',
                                            'show_browse' => false,
                                        ));
                                    ?>
                                </nav>
                        </div>
                    </div>
                </div>
            </section>
        <?php
    }
endif;
add_action('construction_light_breadcrumbs', 'construction_light_breadcrumbs', 100);


/**
 * Main Slider Function Area
*/
if (! function_exists( 'construction_light_banner_slider' ) ):

    function construction_light_banner_slider(){ 

        $all_slider = get_theme_mod('construction_light_slider');

        $banner_slider = json_decode( $all_slider );

        if ($banner_slider && $banner_slider[0]->slider_page ) {

         ?>

        <div id="banner-slider" class="banner-slider owl-carousel features-slider">
            <?php 
                foreach ($banner_slider as $slider) {

                    $page_id = $slider->slider_page;

                if (!empty($page_id)) {

                    $slider_page = new WP_Query('page_id=' . $page_id);

                    if ($slider_page->have_posts()) { while ($slider_page->have_posts()) { $slider_page->the_post();
            ?>
                <div class="slider-item" style="background-image: url(<?php the_post_thumbnail_url(); ?>);">
                    <div class="banner-table">
                        <div class="banner-table-cell">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-7 mx-auto">
                                        <div class="slider-content">
                                            <h2 class="slider-title">
                                                <?php the_title(); ?>
                                            </h2>
                                            <?php the_excerpt(); ?>
                                            <div class="btn-area">
                                                <?php if (!empty( $slider->button_text ) ): ?>
                                                    <a href="<?php echo esc_url( $slider->button_url ); ?>" class="btn btn-primary">
                                                        <?php echo esc_html( $slider->button_text ); ?>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (!empty( $slider->button_one_text ) ): ?>
                                                    <a href="<?php echo esc_url( $slider->button_one_url ); ?>" class="btn btn-border">
                                                        <?php echo esc_html($slider->button_one_text) ?>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div> <!-- slider content end-->
                                    </div> <!-- col end-->
                                </div> <!-- row end-->
                            </div><!-- container end -->
                        </div><!-- banner table cell end -->
                    </div>!-- banner table end -->
                </div>
            <?php } } } } ?>
        </div><!-- Slider section end -->
    <?php } } 
endif;
add_action('construction_light_action_banner_slider', 'construction_light_banner_slider', 25);


/**
 * Our Service Featues Section.
*/
if (! function_exists( 'construction_light_promo_service' ) ):

    function construction_light_promo_service(){

        $features_options = get_theme_mod('construction_light_features_service_section','enable');
        
        if( !empty( $features_options ) && $features_options == 'enable' ){
        ?>
        <section id="cl_feature" class="cons_light_feature">
            <div class="container">
                <div class="row">
                    <?php
                        $promo_service = get_theme_mod('construction_light_promo_service');

                        if (!empty($promo_service)):

                        $pages = json_decode($promo_service);

                        foreach ($pages as $page):

                        $page_id = $page->promoservice_page;

                        if (!empty($page_id)):

                            $service_query = new WP_Query('page_id=' . $page_id);

                            if ( $service_query->have_posts() ): while ( $service_query->have_posts() ): $service_query->the_post();

                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 feature-list">
                            <div class="box">
                                <figure>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('construction-light-medium'); ?>
                                    </a>
                                </figure>

                                <div class="bottom-content">
                                    <div class="icon-box">
                                        <i class="<?php echo esc_html( $page->promoservice_icon ); ?>"></i>
                                    </div>

                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </div>

                    <?php   endwhile;  endif; endif; endforeach; endif; ?>
                </div>
            </div>
        </section>
        <?php } }
endif;
add_action('construction_light_action_promo_service', 'construction_light_promo_service', 30);


/**
 * About Us Section.
*/
if (! function_exists( 'construction_light_about' ) ):

    function construction_light_about(){ 

        $aboutus_options = get_theme_mod('construction_light_aboutus_service_section','enable');
        
        if( !empty( $aboutus_options ) && $aboutus_options == 'enable' ){
        ?>
        <section id="cl_aboutus" class="about_us_front">
            <div class="container">
                <div class="row">
                    <?php
                        $aboutus = get_theme_mod('construction_light_aboutus');

                        if (!empty( $aboutus ) ):

                        $aboutus_args = array(
                            'posts_per_page' => 1,
                            'post_type' => 'page',
                            'page_id' => $aboutus,
                            'post_status' => 'publish',
                        );

                        $aboutus_query = new WP_Query($aboutus_args);

                        if ( $aboutus_query->have_posts() ) : while ( $aboutus_query->have_posts() ) : $aboutus_query->the_post();
                    
                        $about_image = get_theme_mod('construction_light_aboutus_image');

                        $about_col = '';
                        if( !empty( $about_image ) ){
                            $about_col = 7;
                        }else{
                            $about_col = 12;
                        }

                            if (!empty($about_image)):
                        ?>
                            <div class="col-lg-5 col-md-5 col-sm-12">
                                <img src="<?php echo esc_url( wp_get_attachment_url( $about_image ) ); ?>"/>
                            </div>
                        <?php endif; ?>

                        <div class="col-lg-<?php echo intval( $about_col ); ?> col-md-<?php echo intval( $about_col ); ?> col-sm-12">

                            <h3><?php the_title(); ?></h3>
                            
                            <?php
                                $aboutus_info = get_theme_mod('construction_light_aboutus_content', 'excerpt');
                                if ( !empty( $aboutus_info ) && $aboutus_info == 'excerpt') {

                                    the_excerpt();

                                } else {

                                    the_content();
                                } 
                            ?>

                            <?php 
                                $about_email  = get_theme_mod('construction_light_aboutus_email_address');
                                $about_phone  = get_theme_mod('construction_light_aboutus_phone_number');
                                $phone_number = preg_replace("/[^0-9]/","",$about_phone);

                                if( !empty( $about_email ) || !empty( $about_phone ) ){
                            ?>
                                <div class="address-info">
                                    <ul>
                                        <?php if( !empty( $about_email ) ){ ?>

                                            <li><?php esc_html_e('Email Us :','construction-light'); ?>
                                                <a href="mailto:<?php echo esc_attr( antispambot( $about_email ) ); ?>">
                                                    <?php echo esc_html( antispambot( $about_email ) ); ?>
                                                </a>
                                            </li>

                                        <?php } if( !empty( $about_phone ) ){ ?>

                                            <li><?php esc_html_e('Contact Us :','construction-light'); ?>
                                                <a href="tel:<?php echo intval( $phone_number ); ?>">
                                                    <?php echo esc_html( $about_phone ); ?>
                                                </a>
                                            </li>

                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?>

                            <?php
                                $aboutus_info = get_theme_mod('construction_light_aboutus_content', 'excerpt');
                                
                                if( function_exists( 'pll_register_string' ) ){ 

                                    $about_button = pll__( get_theme_mod( 'construction_light_aboutus_button_text','Read More' ) ); 

                                }else{ 

                                    $about_button = get_theme_mod( 'construction_light_aboutus_button_text','Read More' );
                                }

                                if ( !empty( $aboutus_info ) && $aboutus_info == 'excerpt') {
                            ?>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                    <?php echo esc_html( $about_button ); ?><i class="fas fa-arrow-right"></i>
                                </a>

                            <?php } 

                                if (get_theme_mod('construction_light_aboutus_progressbar', true) == true):

                                $about_progressbar = get_theme_mod('construction_light_progressbar');

                                if (!empty( $about_progressbar ) ):
                            ?>
                                <div class="achivement-items">
                                    <ul>
                                        <?php
                                            $progressbars = json_decode($about_progressbar);
                                            foreach ($progressbars as $progressbar):
                                        ?>
                                            <li>
                                                <div class="timer achivement"><?php echo intval( $progressbar->progressbar_number ); ?></div>
                                                <span class="medium"><?php echo esc_html( $progressbar->progressbar_title ); ?></span>
                                            </li>
                                        <?php endforeach; endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                        </div>

                    <?php endwhile; endif; endif; ?>
                </div>
            </div>
        </section>
    <?php } }
endif;
add_action('construction_light_action_about', 'construction_light_about', 35);


/**
 * Video Call To Action Section.
*/
if (! function_exists( 'construction_light_video_calltoaction' ) ):

    function construction_light_video_calltoaction(){

        $video_cta_bg_image  =  get_theme_mod('construction_light_video_calltoaction_image');

        if( function_exists( 'pll_register_string' ) ){

            $video_cta_title = pll__( get_theme_mod('construction_light_video_calltoaction_title') );
            $video_cta_sub_title = pll__( get_theme_mod('construction_light_video_calltoaction_subtitle') );
        
        }else{

            $video_cta_title     = get_theme_mod( 'construction_light_video_calltoaction_title' );
            $video_cta_sub_title = get_theme_mod( 'construction_light_video_calltoaction_subtitle' );

        }

        $yourtube_video_url  = get_theme_mod('construction_light_video_button_url');

        $video_cta_options = get_theme_mod('construction_light_video_cta_service_section','enable');

        if( !empty( $video_cta_options ) && $video_cta_options == 'enable' ){ ?>

            <div id="cl_ctavideo" class="calltoaction_promo_wrapper video_calltoaction" style="background-image:url(<?php echo esc_url( $video_cta_bg_image ); ?>);background-repeat:no-repeat;background-size:cover;background-attachment:fixed;background-position: center;">
                <div class="container">
                    
                    <div class="video_calltoaction_wrap">
                        <a href="<?php echo esc_url( $yourtube_video_url ); ?>" target="_blank" class="popup-youtube  box-shadow-ripples"><i class="fas fa-play "></i></a>
                    </div>

                    <div class="calltoaction_full_widget_content">

                        <h2 class="wow zoomIn"><?php echo esc_html( $video_cta_title ); ?></h2>

                        <div class="calltoaction_subtitle wow zoomIn">
                            <p><?php echo esc_html( $video_cta_sub_title ); ?></p>
                        </div>
                    </div>

                </div>
            </div>
    <?php } }

endif;

add_action('construction_light_action_video_calltoaction', 'construction_light_video_calltoaction', 40);


/**
 * Our Main Service Section.
*/
if (! function_exists( 'construction_light_service' ) ):
    function construction_light_service(){

        $title          = get_theme_mod('construction_light_service_title');
        $sub_title      = get_theme_mod('construction_light_service_sub_title');
        $service_layout = get_theme_mod('construction_light_service_layout', 'layout_one');
        $service_page   = get_theme_mod('construction_light_service');

        $services_options = get_theme_mod('construction_light_service_service_section','enable');
        
        if( !empty( $services_options ) && $services_options == 'enable' ){
        ?>
        <section id="cl_services" class="cons_light_feature <?php echo esc_attr( $service_layout ); ?>">
            <div class="container">
                
                <?php construction_light_section_title( $title, $sub_title ); ?>

                <div class="row">
                    <?php
                        if (!empty($service_page)):

                        $pages = json_decode($service_page);

                        foreach ($pages as $page):

                            $page_id = $page->service_page;

                            if (!empty($page_id)):

                            $service_query = new WP_Query('page_id=' . $page_id);

                            if ( $service_query->have_posts() ): while ( $service_query->have_posts() ): $service_query->the_post();

                                if( function_exists( 'pll_register_string' ) ){ 

                                    $service_button = pll__( get_theme_mod( 'construction_light_service_button','Read More' ) ); 

                                }else{ 

                                    $service_button = get_theme_mod( 'construction_light_service_button','Read More' );
                                }

                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 feature-list">
                            <div class="box">
                                <?php if( !empty( $service_layout ) && $service_layout == 'layout_one' ){ ?>
                                    <figure>
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('construction-light-medium'); ?>
                                        </a>
                                    </figure>
                                <?php } ?>

                                <div class="bottom-content">

                                    <?php if( !empty( $service_layout ) && $service_layout == 'layout_two' ){ ?>
                                        <div class="icon-box">
                                            <i class="<?php echo esc_attr($page->service_icon); ?>"></i>
                                        </div>
                                    <?php } ?>

                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                    <?php the_excerpt(); ?>


                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                        <?php

                                        ?>
                                        <?php echo esc_html( $service_button ); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                       
                    <?php endwhile; endif; endif; endforeach; endif; ?>
                </div>
            </div>
        </section>
    <?php } }
endif;
add_action('construction_light_action_service', 'construction_light_service', 45);


/**
 * Call To Action Section.
*/
if (! function_exists( 'construction_light_calltoaction' ) ):

    function construction_light_calltoaction(){

        $cta_bg_image =  get_theme_mod('construction_light_calltoaction_image');

        if( function_exists( 'pll_register_string' ) ){

            $cta_title       = pll__( get_theme_mod( 'construction_light_calltoaction_title' ) );
            $cta_sub_title   = pll__( get_theme_mod( 'construction_light_calltoaction_subtitle' ) );
            $button_text     = pll__( get_theme_mod('businessroy_calltoaction_button','Our Services') );
            $button_text_one = pll__( get_theme_mod('businessroy_calltoaction_button_one','Contact Us') );
        
        }else{

            $cta_title       = get_theme_mod( 'construction_light_calltoaction_title' );
            $cta_sub_title   = get_theme_mod( 'construction_light_calltoaction_subtitle' );
            $button_text     = get_theme_mod('construction_light_calltoaction_button','Our Services');
            $button_text_one = get_theme_mod('construction_light_calltoaction_button_one','Contact Us');

        }

        $button_link = get_theme_mod('construction_light_calltoaction_link');
        $button_link_one = get_theme_mod('construction_light_calltoaction_link_one');

        $cta_options = get_theme_mod('construction_light_cta_service_section','enable');
        if( !empty( $cta_options ) && $cta_options == 'enable' ){
        ?>
            <div id="cl_cta" class="calltoaction_promo_wrapper" style="background-image:url(<?php echo esc_url( $cta_bg_image ); ?>);background-repeat:no-repeat;background-size:cover;background-attachment:fixed;background-position: center;">
                <div class="container">
                    <div class="calltoaction_full_widget_content">

                        <h2 class="wow zoomIn"><?php echo esc_html( $cta_title ); ?></h2>

                        <div class="calltoaction_subtitle wow zoomIn">
                            <p><?php echo esc_html( $cta_sub_title ); ?></p>
                        </div>
                    </div>

                    <div class="calltoaction_button_wrap">
                        <?php if( !empty( $button_text ) ){ ?>

                            <a href="<?php echo esc_url( $button_link ); ?>" class="btn btn-primary wow fadeInLeft">
                                <?php echo esc_html( $button_text ); ?> <i class="fas fa-arrow-right"></i>
                            </a>

                        <?php } if( !empty( $button_text_one ) ){ ?>

                            <a href="<?php echo esc_url( $button_link_one ); ?>" class="btn btn-border wow fadeInRight">
                                <?php echo esc_html( $button_text_one ); ?> <i class="fas fa-arrow-right"></i>
                            </a>

                        <?php } ?>

                    </div>
                </div>
            </div>
    <?php } }
endif;
add_action('construction_light_action_calltoaction', 'construction_light_calltoaction', 50);


/**
 *  Our Work Portfolio Section.
*/
if (! function_exists( 'construction_light_recentwork' ) ):

    function construction_light_recentwork() {

        $title = get_theme_mod('construction_light_recentwork_title');
        $sub_title = get_theme_mod('construction_light_recentwork_sub_title');

        $cons_light_portfolio_cat = get_theme_mod('construction_light_recent_work');

        $portfolio_options = get_theme_mod('construction_light_portfolio_section','enable');

        if( !empty( $portfolio_options ) && $portfolio_options == 'enable' ){
        ?>
        <section id="cl_portfolio" class="cons_ligcons_light_portfolio-section clearfix">
            <div class="container">

                <?php construction_light_section_title( $title, $sub_title ); ?>

                <?php
                    if($cons_light_portfolio_cat){
                    $cons_light_portfolio_cat_array = explode(',', $cons_light_portfolio_cat) ;
                ?>  
                    <div class="cons_light_portfolio-cat-name-list">
                        <div class="cons_light_portfolio-cat-name active" data-filter="*"><?php echo esc_html_e('All Works','construction-light'); ?></div>
                        <?php 
                            foreach ($cons_light_portfolio_cat_array as $cons_light_portfolio_cat_single) {

                                $category_slug = "";
                                $category_slug = get_category($cons_light_portfolio_cat_single);

                                if( is_object($category_slug)){

                                $category_slug = 'portfolio-'.$category_slug->term_id;
                        ?>
                                <div class="cons_light_portfolio-cat-name" data-filter=".<?php echo esc_attr($category_slug); ?>">
                                    <?php echo esc_html(get_cat_name($cons_light_portfolio_cat_single)); ?>
                                </div>

                        <?php } } ?>
                    </div>
                <?php } ?>

                <div class="cons_light_portfolio-post-wrap clearfix">
                    <div class="cons_light_portfolio-posts clearfix">
                        <?php 
                            if($cons_light_portfolio_cat){
                            $count = 1;
                            $args = array( 'cat' => $cons_light_portfolio_cat, 'posts_per_page' => -1 );
                            $query = new WP_Query($args);

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
    <?php } }
endif;
add_action('construction_light_action_recentwork', 'construction_light_recentwork', 55);


/**
 *  Success Product Counter Section.
*/
if (! function_exists( 'construction_light_counter' ) ):

    function construction_light_counter(){
        
        $title = get_theme_mod('construction_light_counter_title');
        $sub_title = get_theme_mod('construction_light_counter_sub_title');

        $counter_bg = get_theme_mod('construction_light_counter_image');

        $counter_options = get_theme_mod('construction_light_counter_section','enable');
        if( !empty( $counter_options ) && $counter_options == 'enable' ){
        ?>
        <section id="cl_counter" class="cons_light_counter_wrap" style="background-image:url(<?php echo esc_url( $counter_bg ); ?>);">
            <div class="container">

                <?php construction_light_section_title( $title, $sub_title ); ?>

                <div class="row cons_light_team-counter-wrap">
                    <?php
                        $counter_page = get_theme_mod('construction_light_counter');

                        if (!empty($counter_page)):

                        $counters = json_decode($counter_page);
                        $i = 1;
                        foreach ( $counters as $counter ):
                    ?>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="cons_light_counter">
                                <div class="cons_light_counter-icon">
                                    <i class="<?php echo esc_attr( $counter->counter_icon ); ?>"></i>
                                </div>
                                <div class="cons_light_counter_wrapper">
                                    <?php if($counter->counter_prefix): ?>
                                    <div class="counter_prefix"><?php echo esc_html($counter->counter_prefix); ?></div>
                                    <?php endif; ?>
                                    <div class="cons_light_counter-count odometer odometer<?php echo esc_attr($i); ?>" data-count="<?php echo absint($counter->counter_number); ?>">
                                        99
                                    </div>
                                    <?php if($counter->counter_suffix): ?>
                                    <div class="counter_suffix"><?php echo esc_html($counter->counter_suffix); ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                
                                <h6 class="cons_light_counter-title">
                                    <?php echo esc_html( $counter->counter_title ); ?>
                                </h6>
                            </div>
                        </div>
                    <?php  $i++; endforeach; endif; ?>
                </div>
            </div>
        </section>
    <?php } }
endif;
add_action('construction_light_action_counter', 'construction_light_counter', 60);


/**
 *  Blog Section.
*/
if (! function_exists( 'construction_light_blog' ) ):
    function construction_light_blog(){

        $title = get_theme_mod('construction_light_blog_title');
        $sub_title = get_theme_mod('construction_light_blog_sub_title');

        $blog_options = get_theme_mod('construction_light_home_blog_section','enable');
        if( !empty( $blog_options ) && $blog_options == 'enable' ){
        ?>
        <section id="cl_blog" class="cons_light_blog-list-area">
            <div class="container">

                <?php construction_light_section_title( $title, $sub_title ); ?>

                <div class="row">
                    <?php
                        $blog = get_theme_mod('construction_light_blog');
                        $cat_id = explode(',', $blog);
                        $blog_posts = get_theme_mod('construction_light_posts_num', 'three');

                        if ($blog_posts == 'three') {

                            $post_num = 3;

                        } else {

                            $post_num = 6;

                        }

                        $args = array(
                            'posts_per_page' => $post_num,
                            'post_type' => 'post',
                            'tax_query' => array(

                                array(
                                    'taxonomy' => 'category',
                                    'field' => 'term_id',
                                    'terms' => $cat_id
                                ),
                            ),
                        );

                        $blog_query = new WP_Query ($args);

                        if ( $blog_query->have_posts() ): while ( $blog_query->have_posts() ) : $blog_query->the_post();

                            if( function_exists( 'pll_register_string' ) ){ 

                                $blogreadmore_btn = pll__( get_theme_mod( 'construction_light_blogtemplate_btn', 'Continue Reading' ) );

                            }else{ 

                                $blogreadmore_btn = get_theme_mod( 'construction_light_blogtemplate_btn', 'Continue Reading' );

                            }
                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 articlesListing blog-grid">
                            <article id="post-<?php the_ID(); ?>" <?php post_class('article'); ?>>
                                <div class="blog-post-thumbnail">
                                    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                        <?php the_post_thumbnail('construction-light-medium'); ?>
                                    </a>
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
                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                            <span><?php echo esc_html( $blogreadmore_btn ); ?><i class="fas fa-arrow-right"></i></span>
                                        </a>
                                    </div>
                                    
                                </div>

                            </article><!-- #post-<?php the_ID(); ?> -->
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </section>
    <?php } }
endif;
add_action('construction_light_action_blog', 'construction_light_blog', 65);


/**
 *  Testimonial Section.
*/
if (! function_exists( 'construction_light_testimonial' ) ):
    function construction_light_testimonial(){

        $title = get_theme_mod('construction_light_testimonial_title');
        $sub_title = get_theme_mod('construction_light_testimonial_sub_title');

        $testimonial_bg = get_theme_mod('construction_light_testimonials_image');
        $testimonial_page = get_theme_mod('construction_light_testimonials'); 

        $testimonial_options = get_theme_mod('construction_light_testimonial_options','enable');
        if( !empty( $testimonial_options ) && $testimonial_options == 'enable' ){
        ?>
        <section id="cl_testimonial" class="cons_light_testimonial" style="background-image:url(<?php echo esc_url( $testimonial_bg ); ?>);">
            <div class="container">

                <?php construction_light_section_title( $title, $sub_title ); ?>

                <div class="row">
                    <div class="owl-carousel owl-theme testimonial_slider">
                        <?php
                            if (!empty($testimonial_page)):

                            $testimonial_pages = json_decode($testimonial_page);

                            foreach ($testimonial_pages as $testimonial_page):

                            $page_id = $testimonial_page->testimonial_page;

                            if (!empty($page_id)):

                            $testimonial_query = new WP_Query('page_id=' . $page_id);

                            if ( $testimonial_query->have_posts() ): while ($testimonial_query->have_posts()): $testimonial_query->the_post();
                        ?>
                            <div class="item">
                                <div class="col-lg-12 col-md-12 col-sm-12">

                                    <div class="client-img">
                                        <?php the_post_thumbnail('thumbnail'); ?>
                                    </div>

                                    <?php the_excerpt(); ?>

                                    <div class="client-text">
                                        <h3>
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <h4><?php echo esc_html( $testimonial_page->designation ); ?></h4>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; endif; endif; endforeach; endif; ?>

                    </div>
                </div>
            </div>
        </section>
    <?php } }
endif;
add_action('construction_light_action_testimonial', 'construction_light_testimonial', 70);


/**
 *  Our Team Member Section
*/
if (! function_exists( 'construction_light_team' ) ):
    function construction_light_team(){

        $title = get_theme_mod('construction_light_team_title');
        $sub_title = get_theme_mod('construction_light_team_sub_title');

        $team_layout = get_theme_mod('construction_light_team_layout', 'layout_one');
        $team_page = get_theme_mod('construction_light_team');

        $team_options = get_theme_mod('construction_light_team_options','enable');
        if( !empty( $team_options ) && $team_options == 'enable' ){
        ?>
        <section id="cl_team" class="cons_light_team_layout_two <?php echo esc_attr( $team_layout ); ?>">
            <div class="container">
                
                <?php construction_light_section_title( $title, $sub_title ); ?>

                <div class="row">
                    <?php

                        if (!empty( $team_page ) ):

                        $team_pages = json_decode($team_page);

                        foreach ($team_pages as $team_page):
                        
                        $page_id = $team_page->team_page;

                            if (!empty( $page_id )):

                            $team_query = new WP_Query('page_id=' . $page_id);

                            if ($team_query->have_posts()): while ($team_query->have_posts()): $team_query->the_post();
                    ?>

                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="box">
                                <figure>
                                    <?php
                                        if( !empty( $team_layout ) && $team_layout == 'layout_two') {

                                            the_post_thumbnail('thumbnail');

                                        } else {

                                            the_post_thumbnail('construction-light-team');

                                        }
                                    ?>
                                </figure>

                                <div class="team-wrap">

                                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

                                    <?php if (!empty( $team_page->designation ) ): ?>

                                        <span><?php echo esc_html($team_page->designation); ?></span>

                                    <?php endif; ?>

                                    <?php the_excerpt(); ?>

                                    <ul class="sp_socialicon">
                                        <?php if (!empty( $team_page->facebook ) ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url( $team_page->facebook ); ?>">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>
                                            </li>
                                        <?php endif; if (!empty( $team_page->twitter ) ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url($team_page->twitter); ?>">
                                                    <i class="fab fa-twitter"></i>
                                                </a>
                                            </li>
                                        <?php endif; if (!empty( $team_page->linkedin ) ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url($team_page->linkedin); ?>">
                                                    <i class="fab fa-linkedin-in"></i>
                                                </a>
                                            </li>
                                        <?php endif; if (!empty( $team_page->instagram ) ) : ?>
                                            <li>
                                                <a href="<?php echo esc_url($team_page->instagram); ?>">
                                                    <i class="fab fa-instagram"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>

                                </div>
                            </div>
                        </div>

                    <?php endwhile; endif; endif; endforeach; endif; ?>
                </div>
            </div>
        </section>
    <?php } }
endif;
add_action('construction_light_action_team', 'construction_light_team', 75);


/**
 *  Our Client Brand Logo Section.
*/
if (! function_exists( 'costruction_light_clients' ) ):
    function costruction_light_clients(){ 

        $title = get_theme_mod('construction_light_client_title');
        $sub_title = get_theme_mod('construction_light_client_sub_title');

        $client_logo_options = get_theme_mod('construction_light_client_logo_options','enable');
        if( !empty( $client_logo_options ) && $client_logo_options == 'enable' ){
        ?>
        <section id="cl_clients" class="cons_light_client_logo_layout_two">
            <div class="container">

                <?php construction_light_section_title( $title, $sub_title ); ?>

                <div class="row">
                    <div class="owl-carousel owl-theme client_logo">
                        <?php

                        $client_images = get_theme_mod('construction_light_client');

                        if (!empty($client_images)) :

                            $client_images = json_decode($client_images);

                            foreach ($client_images as $image) {
                        ?>
                            <div class="item">
                                <div class="box">
                                    <a href="<?php echo esc_url( $image->client_link ); ?>">
                                        <img src="<?php echo esc_url( $image->client_image ); ?>" class="img-fluid">
                                    </a>
                                </div>
                            </div>
                        <?php } endif; ?>
                    </div>
                </div>

            </div>
        </section>
    <?php } }
endif;
add_action('costruction_light_action_clients', 'costruction_light_clients', 80);
