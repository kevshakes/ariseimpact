<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Construction Light
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function construction_light_body_classes($classes){

    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    // Adds a class of no-sidebar when there is no sidebar present.
    if (!is_active_sidebar('sidebar-1')) {
        $classes[] = 'no';
    }

    //Web Page Layout
    if (get_theme_mod('construction_light_site_layout', 'full_width') == 'boxed') {
        $classes[] = 'boxed';
    }

    return $classes;
}

add_filter('body_class', 'construction_light_body_classes');


if ( ! function_exists( 'wp_body_open' ) ) {
    /**
     * Body open hook.
     */
    function wp_body_open() {
        
        do_action( 'wp_body_open' );
    }
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function construction_light_pingback_header(){
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'construction_light_pingback_header');


/**
 *  Add Metabox.
*/
if( !function_exists( 'construction_light_page_layout_metabox' ) ):

    function construction_light_page_layout_metabox() {

        add_meta_box('construction_light_display_layout', 
            esc_html__( 'Display Page Layout Options', 'construction-light' ), 
            'construction_light_display_layout_callback', 
            array('page'), 
            'normal', 
            'high'
        );
    }
endif;
add_action('add_meta_boxes', 'construction_light_page_layout_metabox');

/**
 * Page and Post Page Display Layout Metabox function
*/

$construction_light_page_layouts =array(

    'leftsidebar' => array(
        'value'     => 'left',
        'label'     => esc_html__( 'Left Sidebar', 'construction-light' ),
        'thumbnail' => get_template_directory_uri() . '/assets/images/left-sidebar.png',
    ),

    'rightsidebar' => array(
        'value'     => 'right',
        'label'     => esc_html__( 'Right (Default)', 'construction-light' ),
        'thumbnail' => get_template_directory_uri() . '/assets/images/right-sidebar.png',
    ),

     'nosidebar' => array(
        'value'     => 'no',
        'label'     => esc_html__( 'Full width', 'construction-light' ),
        'thumbnail' => get_template_directory_uri() . '/assets/images/no-sidebar.png',
    )
);

/**
 * Function for Page layout meta box
*/

if ( ! function_exists( 'construction_light_display_layout_callback' ) ) {
    function construction_light_display_layout_callback(){
        global $post, $construction_light_page_layouts;
        wp_nonce_field( basename( __FILE__ ), 'construction_light_settings_nonce' ); ?>
        <table>
            <tr>
              <td>            
                <?php
                  $i = 0;  
                  foreach ($construction_light_page_layouts as $field) {  
                  $construction_light_page_metalayouts = esc_attr( get_post_meta( $post->ID, 'construction_light_page_layouts', true ) ); 
                ?>            
                  <div class="radio-image-wrapper slidercat" id="slider-<?php echo intval( $i ); ?>" style="float: right; margin-right: 25px;">
                    <label class="description">
                        <span>
                          <img src="<?php echo esc_url( $field['thumbnail'] ); ?>" />
                        </span></br>
                        <input type="radio" name="construction_light_page_layouts" value="<?php echo esc_attr( $field['value'] ); ?>" <?php checked( esc_html( $field['value'] ), 
                            $construction_light_page_metalayouts ); if(empty($construction_light_page_metalayouts) && esc_html( $field['value'] ) =='right'){ echo "checked='checked'";  } ?>/>
                         <?php echo esc_html( $field['label'] ); ?>
                    </label>
                  </div>
                <?php  $i++; }  ?>
              </td>
            </tr>            
        </table>
    <?php
    }
}

/**
 * Save the custom metabox data
*/
if ( ! function_exists( 'construction_light_save_page_settings' ) ) {
    function construction_light_save_page_settings( $post_id ) { 
        global $construction_light_page_layouts, $post;
         if ( !isset( $_POST[ 'construction_light_settings_nonce' ] ) || !wp_verify_nonce( sanitize_key( $_POST[ 'construction_light_settings_nonce' ] ) , basename( __FILE__ ) ) ) 
            return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)  
            return;        
        if (isset( $_POST['post_type'] ) && 'page' == $_POST['post_type']) {  
            if (!current_user_can( 'edit_page', $post_id ) )  
                return $post_id;  
        } elseif (!current_user_can( 'edit_post', $post_id ) ) {  
                return $post_id;  
        }  

        foreach ($construction_light_page_layouts as $field) {  
            $old = esc_attr( get_post_meta( $post_id, 'construction_light_page_layouts', true) );
            if ( isset( $_POST['construction_light_page_layouts']) ) { 
                $new = sanitize_text_field( wp_unslash( $_POST['construction_light_page_layouts'] ) );
            }
            if ($new && $new != $old) {  
                update_post_meta($post_id, 'construction_light_page_layouts', $new);  
            } elseif ('' == $new && $old) {  
                delete_post_meta($post_id,'construction_light_page_layouts', $old);  
            } 
         } 
    }
}
add_action('save_post', 'construction_light_save_page_settings');

/**
 * Fully Translation ready Multilingual Compatible with Polylang and WPML plugins.
*/
if( function_exists( 'pll_register_string' ) ){

    /**
     * About Us 
    */
    pll_register_string( 'aboutus_readmore_btn', get_theme_mod('construction_light_aboutus_button_text'), 'Construction Light', true );

    /**
     * Portfolio Services Section
    */
    pll_register_string( 'recentwork_title', get_theme_mod('construction_light_recentwork_title'), 'Construction Light', true );
    pll_register_string( 'recentwork_subtitle', get_theme_mod('construction_light_recentwork_sub_title'), 'Construction Light', true );

    /**
     * Video Call To Action Section
    */
    pll_register_string( 'video_calltoaction_title', get_theme_mod('construction_light_video_calltoaction_title'), 'Construction Light', true );
    pll_register_string( 'video_calltoaction_subtitle', get_theme_mod('construction_light_video_calltoaction_subtitle'), 'Construction Light', true );

    /** 
     * Our Services Section
    */
    pll_register_string( 'service_title', get_theme_mod('construction_light_service_title'), 'Construction Light', true );
    pll_register_string( 'service_subtitle', get_theme_mod('construction_light_service_sub_title'), 'Construction Light', true );
    pll_register_string( 'service_readmore_btn', get_theme_mod('construction_light_service_button'), 'Construction Light', true );

    /**
     * Call To Action Section
    */
    pll_register_string( 'calltoaction_title', get_theme_mod('construction_light_calltoaction_title'), 'Construction Light', true );
    pll_register_string( 'calltoaction_subtitle', get_theme_mod('construction_light_calltoaction_subtitle'), 'Construction Light', true );
    pll_register_string( 'calltoaction_button', get_theme_mod('construction_light_calltoaction_button'), 'Construction Light', true );
    pll_register_string( 'calltoaction_button_one', get_theme_mod('construction_light_calltoaction_button_one'), 'Construction Light', true );

    /**
     * Counter Services Section
    */
    pll_register_string( 'counter_title', get_theme_mod('construction_light_counter_title'), 'Construction Light', true );
    pll_register_string( 'counter_subtitle', get_theme_mod('construction_light_counter_sub_title'), 'Construction Light', true );

    /**
     * Blog Services Section
    */
    pll_register_string( 'blog_title', get_theme_mod('construction_light_blog_title'), 'Construction Light', true );
    pll_register_string( 'blog_subtitle', get_theme_mod('construction_light_blog_sub_title'), 'Construction Light', true );
    pll_register_string( 'blog_readmore_btn', get_theme_mod('construction_light_blogtemplate_btn'), 'Construction Light', true );

    /**
     * Testimonial Services Section
    */
    pll_register_string( 'testimonial_title', get_theme_mod('construction_light_testimonial_title'), 'Construction Light', true );
    pll_register_string( 'testimonial_subtitle', get_theme_mod('construction_light_testimonial_sub_title'), 'Construction Light', true );

    /**
     * Team Services Section
    */
    pll_register_string( 'team_title', get_theme_mod('construction_light_team_title'), 'Construction Light', true );
    pll_register_string( 'team_subtitle', get_theme_mod('construction_light_team_sub_title'), 'Construction Light', true );

    /**
     * Client Logo Section
    */
    pll_register_string( 'client_title', get_theme_mod('construction_light_client_title'), 'Construction Light', true );
    pll_register_string( 'client_subtitle', get_theme_mod('construction_light_client_sub_title'), 'Construction Light', true );
}