<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Construction Light
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
 *
 * @return void
 */
function construction_light_woocommerce_setup() {

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'construction_light_woocommerce_setup' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
//add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function construction_light_woocommerce_active_body_class( $classes ) {

	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'construction_light_woocommerce_active_body_class' );


/**
 * Product gallery thumnbail columns.
 *
 * @return integer number of columns.
 */
function construction_light_woocommerce_thumbnail_columns() {
	return 4;
}
add_filter( 'woocommerce_product_thumbnails_columns', 'construction_light_woocommerce_thumbnail_columns' );

/**
 * Descriptions on Header Menu
 * @author AF themes
 * @param string $item_output , HTML outputp for the menu item
 * @param object $item , menu item object
 * @param int $depth , depth in menu structure
 * @param object $args , arguments passed to wp_nav_menu()
 * @return string $item_output
 */
function construction_light_header_menu_desc($item_output, $item, $depth, $args){

    if ('menu-1' == $args->theme_location && $item->description)

        $item_output = str_replace('</a>', '<span class="menu-description">' . $item->description . '</span></a>', $item_output);

    return $item_output;
}
//add_filter('walker_nav_menu_start_el', 'construction_light_header_menu_desc', 10, 4);


remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20 );
add_filter( 'woocommerce_show_page_title', '__return_false' );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

function construction_light_woocommerce_template_loop_product_thumbnail(){ ?>

    <div class="construction_light_products_item">
        <div class="construction_light_products_item_body">
			<?php
		        global $post, $product, $product_label_custom; 

		        $sale_class = '';
		        if( $product->is_on_sale() == 1 ){
					$sale_class = 'new_sale';
				}
			?>
			<div class="flash <?php echo esc_attr( $sale_class ); ?>">

				<?php construction_light_sale_percentage_loop(); ?>

				<?php 
		            if ($product_label_custom != ''){

						echo '<span class="onnew"><span class="text">'.esc_html__('New','construction-light').'</span></span>';
					}

		            if ( $product->is_on_sale() ) :

		             	echo apply_filters( 'woocommerce_sale_flash', '<span class="construction_light_sale_label"><span class="text">' . esc_html__( 'Sale!', 'construction-light' ) . '</span></span>', $post, $product );
		            
					endif;
	            ?>
			</div>

            <a href="<?php the_permalink(); ?>" class="construction_light_product_item_link">
            	<?php the_post_thumbnail('woocommerce_thumbnail'); #Products Thumbnail ?>
            </a>
			
        </div>
    </div>    
  	<?php 
}
add_action( 'woocommerce_before_shop_loop_item_title', 'construction_light_woocommerce_template_loop_product_thumbnail', 10 );

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

if ( !function_exists('construction_light_woocommerce_shop_loop_item_title') ) {

    function construction_light_woocommerce_shop_loop_item_title(){ ?>

        <div class="construction_light_products_item_details">
        	<h3>
	          	<a class="construction_light_products_title" href="<?php the_permalink(); ?>">
	            	<?php the_title( ); ?>
	          	</a>
          	</h3>
      <?php 
    }
}
add_action( 'woocommerce_shop_loop_item_title', 'construction_light_woocommerce_shop_loop_item_title', 10 );


remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 11);

/**
 * Price & Rating Wrap
*/
if (!function_exists('construction_light_woocommerce_before_rating_loop_price')) {

    function construction_light_woocommerce_before_rating_loop_price(){ ?>

    	 <div class="price-rating-wrap clearfix"> 

      <?php 
    }
}
add_action( 'woocommerce_after_shop_loop_item_title', 'construction_light_woocommerce_before_rating_loop_price', 6);

if (!function_exists('construction_light_woocommerce_after_rating_loop_price')) {

    function construction_light_woocommerce_after_rating_loop_price(){ ?>

    	</div>

      <?php 
    }
}
add_action( 'woocommerce_after_shop_loop_item_title', 'construction_light_woocommerce_after_rating_loop_price', 12 );



if (!function_exists('construction_light_woocommerce_product_item_details_close')) {

    function construction_light_woocommerce_product_item_details_close(){ ?>

    	</div>

      <?php 
    }
}
add_action( 'woocommerce_template_loop_price', 'construction_light_woocommerce_product_item_details_close', 9 );


/**
 * Result Count & Pagination Wrap
*/
if (!function_exists('construction_light_woocommerce_before_catalog_ordering')) {

    function construction_light_woocommerce_before_catalog_ordering(){ ?>

    	<div class="shop-before-control">

      <?php 
    }
}
add_action( 'woocommerce_before_shop_loop', 'construction_light_woocommerce_before_catalog_ordering', 9);



if (!function_exists('construction_light_woocommerce_after_catalog_ordering')) {

    function construction_light_woocommerce_after_catalog_ordering(){ ?>

    	</div>

      <?php 
    }
}
add_action( 'woocommerce_before_shop_loop', 'construction_light_woocommerce_after_catalog_ordering', 31 );


/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'construction_light_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function construction_light_woocommerce_wrapper_before() { ?>
		<div class="container">
			<div class="row">
				<div id="primary" class="content-area col-lg-8 col-md-8 col-sm-12">
					<main id="main" class="site-main" role="main">
				<?php
	}
}
add_action( 'woocommerce_before_main_content', 'construction_light_woocommerce_wrapper_before' );

if ( ! function_exists( 'construction_light_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function construction_light_woocommerce_wrapper_after() {
			?>
					</main><!-- #main -->
				</div><!-- #primary -->

				<?php get_sidebar('woocommerce'); ?>
				
			</div>

		</div>

		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'construction_light_woocommerce_wrapper_after' );

remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );


/**
 * Single Product Page Wrapper
*/
if (!function_exists('construction_light_woocommerce_before_single_product_summary')) {

    function construction_light_woocommerce_before_single_product_summary(){ ?>

    	<div class="product-summary-wrapper clearfix wow fadeInUp">

      <?php 
    }
}
add_action( 'woocommerce_before_single_product_summary', 'construction_light_woocommerce_before_single_product_summary', 9);



if (!function_exists('construction_light_woocommerce_after_single_product_summary')) {

    function construction_light_woocommerce_after_single_product_summary(){ ?>

    	</div>

      <?php 
    }
}
add_action( 'woocommerce_after_single_product_summary', 'construction_light_woocommerce_after_single_product_summary', 9 );


/* 
 * Product Single Page
*/
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

function construction_light_group_flash(){

    global $post, $product; ?>

	<div class="flash">
		<?php 

			construction_light_sale_percentage_loop(); 

		    $newness_days = 7;
		    
		    $created = strtotime( $product->get_date_created() );
		    if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) > $created ) {
		        echo '<span class="onnew"><span class="text">' . esc_html__( 'New!', 'construction-light' ) . '</span></span>';
		    }

            if ( $product->is_on_sale() ) :

             	echo apply_filters( 'woocommerce_sale_flash', '<span class="construction_light_sale_label"><span class="text">' . esc_html__( 'Sale!', 'construction-light' ) . '</span></span>', $post, $product );
            
			endif;
        ?>
	</div>

	<?php 
}
add_action( 'woocommerce_single_product_summary','construction_light_group_flash', 10 );


function construction_light_custom_ratting_single_product(){
	global $product;
	if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
		return;
	}
	$rating_count = $product->get_rating_count();
	$average      = $product->get_average_rating();
	if ( $rating_count > 0 ) : ?>
        <div class="woocommerce-product-rating">
            <span class="star-rating">
                <span style="width:<?php echo( ( intval($average) / 5 ) * 100 ); ?>%">
                    <?php printf(
						wp_kses( '%1$s out of %2$s', 'construction-light' ),
						'<strong class="rating">' . esc_html( $average ) . '</strong>',
						'<span>5</span>'
					); ?>
                </span>
            </span>

            <span>
                <?php printf(
					wp_kses( 'based on %s rating', 'Based on %s ratings', $rating_count, 'construction-light' ),
					'<span class="rating">' . esc_html( $rating_count ) . '</span>'
				); ?>
            </span>

			<?php if ( comments_open() ) : ?>
                <a href="#reviews" class="woocommerce-review-link" rel="nofollow">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
					<?php echo esc_html__( 'write a preview', 'construction-light' ) ?>
                </a>
			<?php endif ?>
        </div>
	<?php endif;
}
add_action( 'woocommerce_single_product_summary', 'construction_light_custom_ratting_single_product', 5 );


if( !function_exists ('construction_light_sale_percentage_loop') ){
	/**
     * Woocommerce Products Discount Show
     *
     * @since 1.0.0
    */
	function construction_light_sale_percentage_loop() {

		global $product;
		
		if ( $product->is_on_sale() ) {
			
			if ( ! $product->is_type( 'variable' ) and $product->get_regular_price() and $product->get_sale_price() ) {
				
				$max_percentage = ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100;
			
			} else {
				$max_percentage = 0;
				
				foreach ( $product->get_children() as $child_id ) {

					$variation = wc_get_product( $child_id );

					$price = $variation->get_regular_price();

					$sale = $variation->get_sale_price();

					$percentage = '';

					if ( $price != 0 && ! empty( $sale ) ) $percentage = ( $price - $sale ) / $price * 100;

						if ( $percentage > $max_percentage ) {
							$max_percentage = $percentage;
						}
				}
			
			}
			
			echo "<span class='on_sale'>" . esc_html( round( - $max_percentage ) ) . esc_html__("%", 'construction-light')."</span>";
		
		}

	}
}


function construction_light_woocommerce_related_products_args( $args ) {

	$defaults = array(
		'posts_per_page' => 6,
		'columns'        => 3,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'construction_light_woocommerce_related_products_args' );


/**
 * Output product up sells.
 *
 * @param int $posts_per_page (default: -1)
 * @param int $columns (default: 2)
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

if ( ! function_exists( 'construction_light_woocommerce_upsell_display' ) ) {

  	function construction_light_woocommerce_upsell_display() {
	    
    	woocommerce_upsell_display( 6, 3 ); 

  	}
}
add_action( 'woocommerce_after_single_product_summary', 'construction_light_woocommerce_upsell_display', 15 );

/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package Blog99
 */

if ( ! function_exists( 'construction_light_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function construction_light_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		construction_light_woocommerce_header_cart();
		$fragments['li.site-header-cart'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'construction_light_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'construction_light_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function construction_light_woocommerce_cart_link() {
		?>
		<li id="site-header-cart" class="site-header-cart">
			
			<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'construction-light' ); ?>">
				<i class="fa fa-cart-arrow-down" aria-hidden="true"></i>
				<span class="cart-count-item"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
			</a>
		</li>
		<?php
	}
}

if ( ! function_exists( 'construction_light_woocommerce_header_cart' ) ) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function construction_light_woocommerce_header_cart() {

		construction_light_woocommerce_cart_link();
	}
}
add_action('construction_light_woocommerce_header_cart_section','construction_light_woocommerce_header_cart');