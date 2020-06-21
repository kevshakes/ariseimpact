<?php
/**
 * Construction Light Theme Customizer
 *
 * @package Construction Light
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function construction_light_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	//$wp_customize->remove_control("header_image");

	if ( isset( $wp_customize->selective_refresh ) ) {

		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'construction_light_customize_partial_blogname',
		) );

		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'construction_light_customize_partial_blogdescription',
		) );
	}


	// Register custom section types.
	$wp_customize->register_section_type( 'Construction_Light_Customize_Section_Pro' );

	// Register sections.
	/*$wp_customize->add_section(
		new Construction_Light_Customize_Section_Pro(
			$wp_customize,
			'constructionlightpro',
			array(
				'title'    => '',
				'pro_text' => esc_html__( 'UPGRADE PRO','construction-light' ),
				'pro_url'  => 'https://www.sparklewpthemes.com/wordpress-themes/constructionlightpro/',
				'priority'  => 1,
			)
		)
	);*/

	// Register Documentation Section.
	$wp_customize->add_section(
		new Construction_Light_Customize_Section_Pro(
			$wp_customize,
			'constructionlightdoc',
			array(
				'title'    => esc_html__( 'Documentation', 'construction-light' ),
				'pro_text' => esc_html__( 'Demo Data','construction-light' ),
				'pro_url'  => 'http://docs.sparklewpthemes.com/constructionlight/#importdemo',
				'priority'  => 1,
			)
		)
	);

	$wp_customize->get_section( 'static_front_page' )->title = esc_html__('Enable Front Page', 'construction-light');
	$wp_customize->get_section( 'static_front_page' )->priority = 2;


	/**
	 *	Enable Front Page.
	*/

    $wp_customize->add_setting('construction_light_enable_frontpage', array(
    	'default' => false,
        'sanitize_callback' => 'construction_light_sanitize_checkbox',	//done
    ));

    $wp_customize->add_control('construction_light_enable_frontpage', array(
        'type' => 'checkbox',
        'label' => esc_html__('Enable Construction Light Front Page?', 'construction-light'),
        'section' => 'static_front_page',
        'description' => esc_html__( 'Noet :- Front Page only Works when ( Selected "Your latest posts" Options & Enable this check box )', 'construction-light' )
    ));


    $wp_customize->get_section( 'colors' )->title = esc_html__('Theme Colors Settings', 'construction-light');
	$wp_customize->get_section( 'colors' )->priority = 3;

	// Primary Color.
	$wp_customize->add_setting('construction_light_primary_color', array(
	    'default' => '#ffc107',
	    'sanitize_callback' => 'sanitize_hex_color',
	));

	$wp_customize->add_control('construction_light_primary_color', array(
	    'type' => 'color',
	    'label' => esc_html__('Primary Color', 'construction-light'),
	    'section' => 'colors',
	));

    /**
	 * Add General Settings Panel
	 *
	 * @since 1.0.0
	*/
	$wp_customize->add_panel(
	    'construction_light_general_settings_panel',
	    array(
	        'priority'       => 3,
	        'title'          => esc_html__( 'General Settings', 'construction-light' ),
	    )
	);


		$wp_customize->get_section( 'title_tagline' )->panel = 'construction_light_general_settings_panel';
		$wp_customize->get_section( 'title_tagline' )->priority = 5;

		$wp_customize->get_section( 'header_image' )->panel = 'construction_light_general_settings_panel';
		$wp_customize->get_section( 'header_image' )->priority = 7;


		$wp_customize->get_section( 'background_image' )->panel = 'construction_light_general_settings_panel';
		$wp_customize->get_section( 'background_image' )->priority = 15;

    // List All Pages
	$pages = array();

	$pages_obj = get_pages();

	$pages[''] = esc_html__('Select Page', 'construction-light');

	foreach ($pages_obj as $page) {
	    $pages[$page->ID] = $page->post_title;
	}

	// List All Category
	$categories = get_categories();
	$blog_cat = array();

	foreach ($categories as $category) {
	    $blog_cat[$category->term_id] = $category->name;
	}

	/**
	 * Header Settings.
	*/
	$wp_customize->add_panel('construction_light_header_settings', array(
		'title'		=>	esc_html__('Header Setting','construction-light'),
		'priority'	=>	10,
	));

	/**
	 * Top Header 
	*/
	$wp_customize->add_section('construction_light_top_header', array(
		'title'		=>	esc_html__('Top Header Settings','construction-light'),
		'panel'		=> 'construction_light_header_settings',
	));

	$topheader_options = array(
        'quick_contact' => esc_html__('Quick Contact Information', 'construction-light'),
        'social_media'  => esc_html__('Social Media Links', 'construction-light'),
        'top_menu'  => esc_html__('Top Menu Nav', 'construction-light'),
    );

		// Top Header Left Side Options.
		$wp_customize->add_setting('construction_light_topheader_left', array(
		    'default' => 'quick_contact',
		    'sanitize_callback' => 'construction_light_sanitize_select'        //done
		));

		$wp_customize->add_control('construction_light_topheader_left', array(
		    'label' => esc_html__('Top Header Left Side', 'construction-light'),
		    'section' => 'construction_light_top_header',
		    'type' => 'select',
		    'choices' => $topheader_options
		));

		$wp_customize->selective_refresh->add_partial('construction_light_topheader_left', array(
			'selector' => '.cons_light_top_bar .left',
			'container_inclusive' => true
		));

		// Top Header Right Side Options.
		$wp_customize->add_setting('construction_light_topheader_right', array(
		    'default' => 'social_media',
		    'sanitize_callback' => 'construction_light_sanitize_select'        //done
		));

		$wp_customize->add_control('construction_light_topheader_right', array(
		    'label' => esc_html__('Top Header Right Side', 'construction-light'),
		    'section' => 'construction_light_top_header',
		    'type' => 'select',
		    'choices' => $topheader_options
		));

		$wp_customize->selective_refresh->add_partial('construction_light_topheader_right', array(
			'selector' => '.cons_light_top_bar .right',
			'container_inclusive' => true
		));

	// Top Header Contact Address.
	$wp_customize->add_setting( 'construction_light_address', array(
		'sanitize_callback' => 'sanitize_text_field',			//done
	));

	$wp_customize->add_control('construction_light_address', array(
		'label'			=> esc_html__( 'Enter Contact Address', 'construction-light' ),
		'section'		=> 'construction_light_top_header',
		'type' 			=> 'text',
	));

	$wp_customize->selective_refresh->add_partial('construction_light_address', array(
		'selector' => '.nav-classic i.fas.fa-map-marker-alt',
		'container_inclusive' => true
	));
	//Top Header Contact Number.
	$wp_customize->add_setting( 'construction_light_contact_num', array(
		'sanitize_callback' => 'sanitize_text_field',			//done
	));

	$wp_customize->add_control('construction_light_contact_num', array(
		'label'			=> esc_html__( 'Enter Contact Number', 'construction-light' ),
		'section'		=> 'construction_light_top_header',
		'type' 			=> 'text',
	));

	$wp_customize->selective_refresh->add_partial('construction_light_contact_num', array(
		'selector' => '.nav-classic i.fas.fa-phone-volume',
		'container_inclusive' => true
	));
	
	//Top Header Contact Email.
	$wp_customize->add_setting( 'construction_light_email', array(
		'sanitize_callback' => 'sanitize_text_field',			//done
	));

	$wp_customize->add_control('construction_light_email', array(
		'label'			=> esc_html__( 'Enter Email Address', 'construction-light' ),
		'section'		=> 'construction_light_top_header',
		'type' 			=> 'text',
	));

	$wp_customize->selective_refresh->add_partial('construction_light_email', array(
		'selector' => '.nav-classic i.far.fa-envelope',
		'container_inclusive' => true
	));

	//  Top Header Social Links.
	$wp_customize->add_setting('construction_light_topheader_social', array(
	    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
	    'default' => json_encode(array(
	        array(
	            'topheader_icon' =>'fab fa-facebook-f',
	            'social_link'   => '',
	        )
	    ))
	));

	$wp_customize->add_control( new Construction_Light_Repeater_Control( $wp_customize, 
		'construction_light_topheader_social', 

		array(
		    'label' 	   => esc_html__('Social Links Settings', 'construction-light'),
		    'section' 	   => 'construction_light_top_header',
		    'settings' 	   => 'construction_light_topheader_social',
		    'cl_box_label' => esc_html__('Social Links Options', 'construction-light'),
		    'cl_box_add_control' => esc_html__('Add New', 'construction-light'),
		),

	    array(

	        'topheader_icon' => array(
	            'type' => 'icons',
	            'label' => esc_html__('Choose Icon', 'construction-light'),
	            'default' => 'fab fa-facebook-f'
	        ),
	        
	        'social_link' => array(
	            'type' => 'url',
	            'label' => esc_html__('Enter Social Link', 'construction-light'),
	            'default' => ''
	        )
		)

	) );


	/**
	 * Header Layout Settings
	*/
	$wp_customize->add_section('construction_light_header', array(
		'title'		=>	esc_html__('Header Layout Settings','construction-light'),
		'panel'		=> 'construction_light_header_settings',
	));

		//  Header Left Side Options.
		$wp_customize->add_setting('construction_light_header_layout', array(
		    'default' => 'layout_one',
		    'sanitize_callback' => 'construction_light_sanitize_select'        //done
		));

		$wp_customize->add_control('construction_light_header_layout', array(
		    'label' => esc_html__('Header Layout', 'construction-light'),
		    'section' => 'construction_light_header',
		    'type' => 'select',
		    'choices' => array(
		    	'layout_one' => esc_html__('Layout One' , 'construction-light'),
		    	'layout_two' => esc_html__('Layout Two' ,'construction-light'),
		    )
		));

		$wp_customize->selective_refresh->add_partial('construction_light_header_layout', array(
			'selector' => '.nav-classic',
			'container_inclusive' => true
		));
	

	/**
	 * Home Page Settings
	*/
	$wp_customize->add_panel('construction_light_frontpage_settings', array(
		'title'		=>	esc_html__('Home Sections','construction-light'),
		'priority'	=>	35,
		'description' => esc_html__('Drag and Drop to Reorder', 'construction-light'). '<img class="construction_light-drag-spinner" src="'.admin_url('/images/spinner.gif').'">',
	));


		/**
		 *	Main Banner Slider.
		*/
		$wp_customize->add_section('construction_light_slider_section', array(
			'title'		=>	esc_html__('Home Slider Settings','construction-light'),
			'panel'		=> 'construction_light_frontpage_settings',
			'priority'  => -1
		));


		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_banner_slider_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_banner_slider_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_slider_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Normal Page Slider Type
		$wp_customize->add_setting('construction_light_slider', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'slider_page' => '',
		            'button_text' => '',
		            'button_url' => '',
		            'button_one_text' => '',
		            'button_one_url' => '',
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize, 
			'construction_light_slider', 

			array(
			    'label' 	   => esc_html__('Banner Slider Page Settings', 'construction-light'),
			    'section' 	   => 'construction_light_slider_section',
			    'settings' 	   => 'construction_light_slider',
			    'cl_box_label' => esc_html__('Slider Settings Options', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New Slider', 'construction-light'),
			),

		    array(

		        'slider_page' => array(
		            'type' => 'select',
		            'label' => esc_html__('Select Slider Page', 'construction-light'),
		            'options' => $pages
		        ),

		        'button_text' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter First Button Text', 'construction-light'),
		            'default' => ''
		        ),
		        
		        'button_url' => array(
		            'type' => 'url',
		            'label' => esc_html__('Enter First Button Url', 'construction-light'),
		            'default' => ''
		        ),

		        'button_one_text' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter Second Button Text', 'construction-light'),
		            'default' => ''
		        ),
		        
		        'button_one_url' => array(
		            'type' => 'url',
		            'label' => esc_html__('Enter Second Button Url', 'construction-light'),
		            'default' => ''
		        ),
			)
		));
	
	$wp_customize->selective_refresh->add_partial('construction_light_slider', array(
		'selector' => '#banner-slider .slider-content',
		'container_inclusive' => true
	));
	/**
	 * Features Service Section 
	*/
	$wp_customize->add_section('construction_light_promoservice_section', array(
		'title'		=>	esc_html__('Features Service Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_promoservice_section')
	));


		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_features_service_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_features_service_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_promoservice_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		//  Features Service Page.
		$wp_customize->add_setting('construction_light_promo_service', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'promoservice_page' => '',
		            'promoservice_icon' =>'fa fa-cogs',

		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize, 
			'construction_light_promo_service', 

			array(
			    'label' 	   => esc_html__('Features Service Settings', 'construction-light'),
			    'section' 	   => 'construction_light_promoservice_section',
			    'settings' 	   => 'construction_light_promo_service',
			    'cl_box_label' => esc_html__('Features Service Settings', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New Page', 'construction-light'),
			),

		    array(

		        'promoservice_page' => array(
		            'type' => 'select',
		            'label' => esc_html__('Select Features Service Page', 'construction-light'),
		            'options' => $pages
		        ),

		        'promoservice_icon' => array(
		            'type' => 'icons',
		            'label' => esc_html__('Choose Icon', 'construction-light'),
		            'default' => 'fa fa-cogs'
		        )
			)
		));

		$wp_customize->selective_refresh->add_partial('construction_light_promo_service', array(
			'selector' => '#cl_feature .box',
			'container_inclusive' => true
		));
	
	/**
	 * About Us Section 
	*/
	$wp_customize->add_section('construction_light_aboutus_section', array(
		'title'		=>	esc_html__('About Us Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_aboutus_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_aboutus_service_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_aboutus_service_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_aboutus_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// About Us Page.
		$wp_customize->add_setting( 'construction_light_aboutus', array(
			'sanitize_callback' => 'absint'			//done
		) );

		$wp_customize->add_control( 'construction_light_aboutus', array(
			'label'    => esc_html__( 'Select Page ', 'construction-light' ),
			'section'  => 'construction_light_aboutus_section',
			'type'     => 'dropdown-pages'
		));

		$wp_customize->selective_refresh->add_partial('construction_light_aboutus', array(
			'selector' => '.about_us_front h3',
			'container_inclusive' => true
		));
	

		// About Us Image.
		$wp_customize->add_setting('construction_light_aboutus_image', array(
			'sanitize_callback'	=> 'absint'		//done
		));

		$wp_customize->add_control(new WP_Customize_Cropped_Image_Control($wp_customize, 'construction_light_aboutus_image', array(
			'label'	   => esc_html__('Upload About Features Image','construction-light'),
			'section'  => 'construction_light_aboutus_section',
			'width'    => 500,
	        'height'   => 500,
		)));

		$wp_customize->selective_refresh->add_partial('construction_light_aboutus_image', array(
			'selector' => '.about_us_front img',
			'container_inclusive' => true
		));
	
		// About Us Content.
		$wp_customize->add_setting( 'construction_light_aboutus_content', array(
			'default' => 'excerpt',
			'sanitize_callback' => 'construction_light_sanitize_select'			//done
		) );

		$wp_customize->add_control( 'construction_light_aboutus_content', array(
			'label'    => esc_html__( 'Select Page ', 'construction-light' ),
			'section'  => 'construction_light_aboutus_section',
			'type'     => 'select',
			'choices' => array(
				'excerpt' => esc_html__('Excerpt','construction-light'),
				'full_content' => esc_html__('Full Content', 'construction-light')
			)
		));

		// About Us Button Text.
		$wp_customize->add_setting( 'construction_light_aboutus_button_text', array(
			'default'           => esc_html__( 'Read More','construction-light' ),
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_aboutus_button_text', array(
			'label'    => esc_html__( 'Enter Button Text', 'construction-light' ),
			'section'  => 'construction_light_aboutus_section',
			'type'     => 'text',
			'active_callback' => 'construction_light_active_about_button'
		));

		$wp_customize->selective_refresh->add_partial('construction_light_aboutus_button_text', array(
			'selector' => '.about_us_front .btn',
			'container_inclusive' => true
		));
	

		$wp_customize->add_setting( 'construction_light_aboutus_email_address', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_aboutus_email_address', array(
			'label'    => esc_html__( 'Enter About Us Email Address', 'construction-light' ),
			'section'  => 'construction_light_aboutus_section',
			'type'     => 'text',
		));


		$wp_customize->add_setting( 'construction_light_aboutus_phone_number', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_aboutus_phone_number', array(
			'label'    => esc_html__( 'Enter About Us Phone Number', 'construction-light' ),
			'section'  => 'construction_light_aboutus_section',
			'type'     => 'text',
		));


		// About Us Show Progress Bar.
		$wp_customize->add_setting( 'construction_light_aboutus_progressbar', array(
			'default' => true,
			'sanitize_callback' => 'construction_light_sanitize_checkbox'			//done
		) );

		$wp_customize->add_control( 'construction_light_aboutus_progressbar', array(
			'label'    => esc_html__( 'Check to Display Progress Bar', 'construction-light' ),
			'section'  => 'construction_light_aboutus_section',
			'type'     => 'checkbox'
		));

		// About Us Progress Bar.
		$wp_customize->add_setting('construction_light_progressbar', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'progressbar_title'  =>'',
		            'progressbar_number'  =>'',	            
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control($wp_customize, 
			'construction_light_progressbar', 

			array(
			    'label' 	   => esc_html__('Achivement Awards Settings', 'construction-light'),
			    'section' 	   => 'construction_light_aboutus_section',
			    'settings' 	   => 'construction_light_progressbar',
			    'cl_box_label' => esc_html__('Achivement Awards Settings', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New Awards', 'construction-light'),
			    'active_callback' => 'construction_light_active_progressbar'
			),
		    array(
		        'progressbar_title' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter Achivement Awards Title', 'construction-light'),
		            'default' => ''
		        ),

		        'progressbar_number' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter Number Achivement Awards ', 'construction-light'),
		            'default' => ''
		        ),
		        
			)
		));
	


	/**
	 * Our Service Section 
	*/
	$wp_customize->add_section('construction_light_service_section', array(
		'title'		=>	esc_html__('Our Service Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_service_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_service_service_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_service_service_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_service_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Our Service Section Title.
		$wp_customize->add_setting( 'construction_light_service_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_service_title', array(
			'label'    => esc_html__( 'Enter Service Section Title', 'construction-light' ),
			'section'  => 'construction_light_service_section',
			'type'     => 'text',
		));


		$wp_customize->selective_refresh->add_partial('construction_light_service_title', array(
			'selector' => '#cl_services .section-title',
			'container_inclusive' => true
		));


		// Our Service Section Sub Title.
		$wp_customize->add_setting( 'construction_light_service_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_service_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_service_section',
			'type'     => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_service_sub_title', array(
			'selector' => '#cl_services .section-tagline',
			'container_inclusive' => true
		));
		
		
		$wp_customize->selective_refresh->add_partial('construction_light_service', array(
			'selector' => '#cl_services .box',
			'container_inclusive' => true
		));



		//  Our Service Page.
		$wp_customize->add_setting('construction_light_service', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'service_page' => '',
		            'service_icon' =>'fa fa-cogs'
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize,
			'construction_light_service', 

			array(
			    'label' 	   => esc_html__('Our Service Settings', 'construction-light'),
			    'section' 	   => 'construction_light_service_section',
			    'settings' 	   => 'construction_light_service',
			    'cl_box_label' => esc_html__('Service Settings Options', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New Page', 'construction-light'),
			),
		    array(
		        'service_page' => array(
		            'type' => 'select',
		            'label' => esc_html__('Select Service Page', 'construction-light'),
		            'options' => $pages
		        ),

		        'service_icon' => array(
		            'type' => 'icons',
		            'label' => esc_html__('Choose Icon', 'construction-light'),
		            'default' => 'fa fa-cogs'
		        )
			)
		));

		// Our Service Section Button text.
		$wp_customize->add_setting( 'construction_light_service_button', array(
			'default'           => esc_html__( 'Read More','construction-light' ),
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_service_button', array(
			'label'    => esc_html__( 'Enter Services Button Text', 'construction-light' ),
			'section'  => 'construction_light_service_section',
			'type'     => 'text',
		));


		// Service Section Layout.
		$wp_customize->add_setting( 'construction_light_service_layout', array(
			'default' => 'layout_one',
			'sanitize_callback' => 'construction_light_sanitize_select'			//done
		) );

		$wp_customize->add_control( 'construction_light_service_layout', array(
			'label'    => esc_html__( 'Our Service Layout', 'construction-light' ),
			'section'  => 'construction_light_service_section',
			'type'     => 'select',
			'choices'  => array(
				'layout_one'  => esc_html__('Layout One', 'construction-light'),
				'layout_two'  =>esc_html__('Layout Two', 'construction-light'),
			)
		));



	/**
	 * Call To Action Section
	*/
	$wp_customize->add_section('construction_light_calltoaction_section', array(
		'title'		=> 	esc_html__('Call To Action Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_calltoaction_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_cta_service_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_cta_service_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_calltoaction_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Call To Action Image.
		$wp_customize->add_setting('construction_light_calltoaction_image', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'construction_light_calltoaction_image', array(
			'label'	   => esc_html__('Upload Background Image','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'image',
		)));


		// Call To Action Title.
		$wp_customize->add_setting('construction_light_calltoaction_title', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control( 'construction_light_calltoaction_title', array(
			'label'	   => esc_html__('Enter Section Title','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_calltoaction_title', array(
			'selector' => '#cl_cta .calltoaction_full_widget_content h2',
			'container_inclusive' => true
		));

		// Call To Action Subtitle.
		$wp_customize->add_setting('construction_light_calltoaction_subtitle', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_calltoaction_subtitle', array(
			'label'	   => esc_html__('Enter Section Subtitle','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'text'
		));

		$wp_customize->selective_refresh->add_partial('construction_light_calltoaction_subtitle', array(
			'selector' => '#cl_cta .calltoaction_full_widget_content .calltoaction_subtitle',
			'container_inclusive' => true
		));

		// Call To Action Button.
		$wp_customize->add_setting('construction_light_calltoaction_button', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_calltoaction_button', array(
			'label'	   => esc_html__('Enter Button One Text','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_calltoaction_button', array(
			'selector' => '#cl_cta .calltoaction_button_wrap .btn',
			'container_inclusive' => true
		));
		
		// Call To Action Button Link.
		$wp_customize->add_setting('construction_light_calltoaction_link', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control('construction_light_calltoaction_link', array(
			'label'	   => esc_html__('Enter Button One Link','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'url',
		));


		// Call To Action Button.
		$wp_customize->add_setting('construction_light_calltoaction_button_one', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_calltoaction_button_one', array(
			'label'	   => esc_html__('Enter Button Two Text','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'text',
		));
		
		// Call To Action Button Link.
		$wp_customize->add_setting('construction_light_calltoaction_link_one', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control('construction_light_calltoaction_link_one', array(
			'label'	   => esc_html__('Enter Button Two Link','construction-light'),
			'section'  => 'construction_light_calltoaction_section',
			'type'	   => 'url',
		));


	/**
	 * Video Call To Action Section
	*/
	$wp_customize->add_section('construction_light_video_calltoaction_section', array(
		'title'		=> 	esc_html__('Video Call To Action Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_video_calltoaction_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_video_cta_service_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_video_cta_service_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_video_calltoaction_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Call To Action Video Button URL.
		$wp_customize->add_setting('construction_light_video_button_url', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control('construction_light_video_button_url', array(
			'label'	   => esc_html__('Enter Youtube Video URL','construction-light'),
			'section'  => 'construction_light_video_calltoaction_section',
			'type'	   => 'url'
		));


		$wp_customize->selective_refresh->add_partial('construction_light_video_button_url', array(
			'selector' => '.video_calltoaction_wrap',
			'container_inclusive' => true
		));

		// Video Call To Action Title.
		$wp_customize->add_setting('construction_light_video_calltoaction_title', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control( 'construction_light_video_calltoaction_title', array(
			'label'	   => esc_html__('Enter Section Title','construction-light'),
			'section'  => 'construction_light_video_calltoaction_section',
			'type'	   => 'text',
		));


		$wp_customize->selective_refresh->add_partial('construction_light_video_calltoaction_title', array(
			'selector' => '#cl_ctavideo .calltoaction_full_widget_content',
			'container_inclusive' => true
		));

		// Video Call To Action Subtitle.
		$wp_customize->add_setting('construction_light_video_calltoaction_subtitle', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_video_calltoaction_subtitle', array(
			'label'	   => esc_html__('Enter Section Subtitle','construction-light'),
			'section'  => 'construction_light_video_calltoaction_section',
			'type'	   => 'text',
		));

		// Video Call To Action Background Image.
		$wp_customize->add_setting('construction_light_video_calltoaction_image', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'construction_light_video_calltoaction_image', array(
			'label'	   => esc_html__('Upload Video Background Image','construction-light'),
			'section'  => 'construction_light_video_calltoaction_section',
			'type'	   => 'image',
		)));



	/**
	 * Portfolio Work Section. 
	*/
	$wp_customize->add_section('construction_light_recentwork_section', array(
		'title'		=> 	esc_html__('Portfolio Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_recentwork_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_portfolio_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_portfolio_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_recentwork_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Portfolio Work Section Title.
		$wp_customize->add_setting( 'construction_light_recentwork_title', array(
			'sanitize_callback' => 'sanitize_text_field', 	 //done	
		));

		$wp_customize->add_control('construction_light_recentwork_title', array(
			'label'		=> esc_html__( 'Enter Section Title', 'construction-light' ),
			'section'	=> 'construction_light_recentwork_section',
			'type'      => 'text'
		));

		$wp_customize->selective_refresh->add_partial('construction_light_recentwork_title', array(
			'selector' => '.cons_ligcons_light_portfolio-section .section-title',
			'container_inclusive' => true
		));
		
		// Our Service Section Sub Title.
		$wp_customize->add_setting( 'construction_light_recentwork_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_recentwork_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_recentwork_section',
			'type'     => 'text',
		));


		$wp_customize->selective_refresh->add_partial('construction_light_recentwork_sub_title', array(
			'selector' => '.cons_ligcons_light_portfolio-section .section-tagline',
			'container_inclusive' => true
		));

		// Portfolio Work Images.
		$wp_customize->add_setting( 'construction_light_recent_work', array(
			'sanitize_callback' => 'sanitize_text_field', 	 //done	
		));

		$wp_customize->add_control( new Construction_Light_Multiple_Check_Control($wp_customize, 
			'construction_light_recent_work', 

			array(
				'label'		=> esc_html__( 'Select Category', 'construction-light' ),
				'settings'	=> 'construction_light_recent_work',
				'section'	=> 'construction_light_recentwork_section',
				'choices'	=> $blog_cat,
			)
		));


	/**
	 * Counter Section. 
	*/
	$wp_customize->add_section('construction_light_counter_section', array(
		'title'		=> 	esc_html__('Counter Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_counter_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_counter_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_counter_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_counter_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Counter Section Title.
		$wp_customize->add_setting('construction_light_counter_title', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_counter_title', array(
			'label'	   => esc_html__('Enter Section Title','construction-light'),
			'section'  => 'construction_light_counter_section',
			'type'	   => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_counter_title', array(
			'selector' => '#cl_counter .section-title',
			'container_inclusive' => true
		));

		// Our Service Section Sub Title.
		$wp_customize->add_setting( 'construction_light_counter_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_counter_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_counter_section',
			'type'     => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_counter_sub_title', array(
			'selector' => '#cl_counter .section-tagline',
			'container_inclusive' => true
		));

		// Counter Background Image.
		$wp_customize->add_setting('construction_light_counter_image', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control(new WP_Customize_Image_Control( $wp_customize, 'construction_light_counter_image', array(
			'label'	   => esc_html__('Upload Counter Background Image','construction-light'),
			'section'  => 'construction_light_counter_section',
			'type'	   => 'image',
		)));


		// Counter Section.
		$wp_customize->add_setting('construction_light_counter', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'counter_icon'  =>'',
		            'counter_title'  =>'',
					'counter_number'  =>'',	            
					'counter_prefix' => '',
					'counter_suffix' => ''
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize, 
			'construction_light_counter', 

			array(
			    'label' 	   => esc_html__('Counter Settings', 'construction-light'),
			    'section' 	   => 'construction_light_counter_section',
			    'settings' 	   => 'construction_light_counter',
			    'cl_box_label' => esc_html__('Counter Settings Options', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New', 'construction-light'),
			),

		    array(

		    	'counter_icon' => array(
		            'type' => 'icons',
		            'label' => esc_html__('Choose Counter Icon', 'construction-light'),
		            'default' => 'fa fa-cogs'
		        ),

		        'counter_title' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter Title', 'construction-light'),
		            'default' => ''
		        ),

		        'counter_number' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter Number', 'construction-light'),
		            'default' => ''
				),
				'counter_prefix' => array(
		            'type' => 'text',
		            'label' => esc_html__('Prefix', 'construction-light'),
		            'default' => ''
				),
				'counter_suffix' => array(
		            'type' => 'text',
		            'label' => esc_html__('Suffix', 'construction-light'),
		            'default' => ''
		        ),
		        
			)
		));


		$wp_customize->selective_refresh->add_partial('construction_light_counter', array(
			'selector' => '#cl_counter .cons_light_counter',
			'container_inclusive' => true
		));

	/* Blog Section. */
	$wp_customize->add_section('construction_light_blog_section', array(
		'title'		=> 	esc_html__('Blog Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_blog_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_home_blog_section', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_home_blog_section', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_blog_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Blog Title.
		$wp_customize->add_setting('construction_light_blog_title', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_blog_title', array(
			'label'	   => esc_html__('Enter Section Title','construction-light'),
			'section'  => 'construction_light_blog_section',
			'type'	   => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_blog_title', array(
			'selector' => '#cl_blog .section-title',
			'container_inclusive' => true
		));
		
		// Our Service Section Sub Title.
		$wp_customize->add_setting( 'construction_light_blog_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_blog_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_blog_section',
			'type'     => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_blog_sub_title', array(
			'selector' => '#cl_blog .section-tagline',
			'container_inclusive' => true
		));

		// Blog Posts.
		$wp_customize->add_setting('construction_light_blog', array(
		    'sanitize_callback' => 'sanitize_text_field',     //done
		));

		$wp_customize->add_control(new Construction_Light_Multiple_Check_Control($wp_customize, 'construction_light_blog', array(
		    'label'    => esc_html__('Select Category To Show Posts', 'construction-light'),
		    'settings' => 'construction_light_blog',
		    'section'  => 'construction_light_blog_section',
		    'choices'  => $blog_cat,
		)));

		$wp_customize->selective_refresh->add_partial('construction_light_blog', array(
			'selector' => '#cl_blog',
			'container_inclusive' => true
		));

		// Select Blog Post Layout.
		$wp_customize->add_setting('construction_light_posts_num',array(
			'default'			 =>	'three',
			'sanitize_callback'	 =>	'construction_light_sanitize_select'		//done	
		));

		$wp_customize->add_control( 'construction_light_posts_num', array(
			'label'	  =>	esc_html__('Select Number Of Blog Posts To Display','construction-light'),
			'section' =>	'construction_light_blog_section',
			'type'	  =>	'select',
			'choices' => array(
				'three' => esc_html__('3 Column Layout','construction-light'),
				'six'   => esc_html__( '6 Column Layout','construction-light' ),
			)
		));


	/* Testimonial Section. */
	$wp_customize->add_section('construction_light_testimonial_section', array(
		'title'		=> 	esc_html__('Testimonial Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_testimonial_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_testimonial_options', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_testimonial_options', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_testimonial_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));


		// Blog Title.
		$wp_customize->add_setting('construction_light_testimonial_title', array(
			'sanitize_callback'	=> 'sanitize_text_field'		//done
		));

		$wp_customize->add_control('construction_light_testimonial_title', array(
			'label'	   => esc_html__('Enter Section Title','construction-light'),
			'section'  => 'construction_light_testimonial_section',
			'type'	   => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_testimonial_title', array(
			'selector' => '#cl_testimonial .section-title',
			'container_inclusive' => true
		));
		$wp_customize->selective_refresh->add_partial('construction_light_testimonial_sub_title', array(
			'selector' => '#cl_testimonial .section-tagline',
			'container_inclusive' => true
		));
		// Our Service Section Sub Title.
		$wp_customize->add_setting( 'construction_light_testimonial_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_testimonial_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_testimonial_section',
			'type'     => 'text',
		));

		// Testimonial Image.
		$wp_customize->add_setting('construction_light_testimonials_image', array(
			'sanitize_callback'	=> 'esc_url_raw'		//done
		));

		$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'construction_light_testimonials_image', array(
			'label'	   => esc_html__('Upload Testimonials Background Image','construction-light'),
			'section'  => 'construction_light_testimonial_section',
			'type'	   => 'image',
		)));

		//  Testimonial Page.
		$wp_customize->add_setting('construction_light_testimonials', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'testimonial_page' => '',
		            'designation'=>'',
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize, 
			'construction_light_testimonials', 

			array(
			    'label' 	   => esc_html__('Testimonials Settings', 'construction-light'),
			    'section' 	   => 'construction_light_testimonial_section',
			    'settings' 	   => 'construction_light_testimonials',
			    'cl_box_label' => esc_html__('Testimonial Settings Options', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New Page', 'construction-light'),
			),
		    array(
		        'testimonial_page' => array(
		            'type' => 'select',
		            'label' => esc_html__('Select Testimonial Page', 'construction-light'),
		            'options' => $pages
		        ),

		        'designation' => array(
		            'type' => 'text',
		            'label' => esc_html__('Enter Designation', 'construction-light'),
		            'default' => ''
		        ),
			)
		));

		$wp_customize->selective_refresh->add_partial('construction_light_testimonials', array(
			'selector' => '#cl_testimonial',
			'container_inclusive' => true
		));

	/* Team Section. */
	$wp_customize->add_section('construction_light_team_section', array(
		'title'		=> 	esc_html__('Our Team Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_team_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_team_options', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_team_options', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_team_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));

		// Team Section Title.
		$wp_customize->add_setting( 'construction_light_team_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_team_title', array(
			'label'    => esc_html__( 'Enter Section Title', 'construction-light' ),
			'section'  => 'construction_light_team_section',
			'type'     => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_team_title', array(
			'selector' => '#cl_team .section-title',
			'container_inclusive' => true
		));
		
		$wp_customize->selective_refresh->add_partial('construction_light_team_sub_title', array(
			'selector' => '#cl_team .section-tagline',
			'container_inclusive' => true
		));
		// Our Service Section Sub Title.
		$wp_customize->add_setting( 'construction_light_team_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_team_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_team_section',
			'type'     => 'text',
		));

		// Our Team Page.
		$wp_customize->add_setting('construction_light_team', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'team_page'   => '',
		            'designation' =>'',
		            'facebook'    =>'',
		            'twitter'     =>'',
		            'linkedin'      =>'',
		            'instagram'   => '',
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize, 
			'construction_light_team', 
			array(
			    'label' 	   => esc_html__('Team Settings', 'construction-light'),
			    'section' 	   => 'construction_light_team_section',
			    'settings' 	   => 'construction_light_team',
			    'cl_box_label' => esc_html__('Team Settings Options', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New Page', 'construction-light'),
			),
		    array(

		        'team_page' => array(
		            'type'    => 'select',
		            'label'   => esc_html__('Select Team Page', 'construction-light'),
		            'options' => $pages
		        ),

		        'designation' => array(
		            'type'    => 'text',
		            'label'   => esc_html__('Enter Designation', 'construction-light'),
		            'default' => ''
		        ),

		        'facebook'  => array(
		            'type'   => 'url',
		            'label'  => esc_html__('Enter Facebook Link', 'construction-light'),
		            'default' => ''
		        ),

		        'twitter' 	=> array(
		            'type'    => 'url',
		            'label'   => esc_html__('Enter Twitter Link', 'construction-light'),
		            'default' => ''
		        ),

		        'linkedin'   => array(
		            'type'    => 'url',
		            'label'   => esc_html__('Enter Linkedin Link', 'construction-light'),
		            'default' => ''
		        ),
		        
		        'instagram' => array(
		            'type'    => 'url',
		            'label'   => esc_html__('Enter Instagram Link', 'construction-light'),
		            'default' => ''
		        )
			)
		));

		$wp_customize->selective_refresh->add_partial('construction_light_team', array(
			'selector' => '#cl_team',
			'container_inclusive' => true
		));

		// Team Section Layout.
		$wp_customize->add_setting( 'construction_light_team_layout', array(
			'default'  => 'layout_one',
			'sanitize_callback' => 'construction_light_sanitize_select'			//done
		) );

		$wp_customize->add_control( 'construction_light_team_layout', array(
			'label'    => esc_html__( 'Team Section Layout', 'construction-light' ),
			'section'  => 'construction_light_team_section',
			'type' => 'select',
		    'choices' => array(
		        'layout_one' => esc_html__('Layout One', 'construction-light'),
		        'layout_two' => esc_html__('Layout Two', 'construction-light'),
		    )
		));


	/**
	 * Clients Section. 
	*/
	$wp_customize->add_section('construction_light_client_section', array(
		'title'		=> 	esc_html__('Clients Section','construction-light'),
		'panel'		=> 'construction_light_frontpage_settings',
		'priority'  => construction_light_get_section_position('construction_light_client_section')
	));

		/**
         * Enable/Disable Option
         *
         * @since 1.0.0
        */
        $wp_customize->add_setting('construction_light_client_logo_options', array(
		    'default' => 'enable',
		    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
		));

		$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_client_logo_options', array(
		    'label' => esc_html__('Enable / Disable', 'construction-light'),
		    'section' => 'construction_light_client_section',
		    'switch_label' => array(
		        'enable' => esc_html__('Enable', 'construction-light'),
		        'disable' => esc_html__('Disable', 'construction-light'),
		    ),
		)));


		// Clients Section Title.
		$wp_customize->add_setting( 'construction_light_client_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_client_title', array(
			'label'    => esc_html__( 'Enter Section Title', 'construction-light' ),
			'section'  => 'construction_light_client_section',
			'type'     => 'text',
		));

		$wp_customize->selective_refresh->add_partial('construction_light_client_title', array(
			'selector' => '#cl_clients .section-title',
			'container_inclusive' => true
		));
		$wp_customize->selective_refresh->add_partial('construction_light_client_sub_title', array(
			'selector' => '#cl_clients .section-tagline',
			'container_inclusive' => true
		));
		// Clients Sub Title.
		$wp_customize->add_setting( 'construction_light_client_sub_title', array(
			'sanitize_callback' => 'sanitize_text_field'			//done
		) );

		$wp_customize->add_control( 'construction_light_client_sub_title', array(
			'label'    => esc_html__( 'Enter Service Section Sub Title', 'construction-light' ),
			'section'  => 'construction_light_client_section',
			'type'     => 'text',
		));


		//  Clients Page.
		$wp_customize->add_setting('construction_light_client', array(
		    'sanitize_callback' => 'construction_light_sanitize_repeater',		//done
		    'default' => json_encode(array(
		        array(
		            'client_image' => '',
		            'client_link'  => '',
		        )
		    ))
		));

		$wp_customize->add_control(new Construction_Light_Repeater_Control( $wp_customize, 
			'construction_light_client',

			array(
			    'label' 	   => esc_html__('Client Logo Settings', 'construction-light'),
			    'section' 	   => 'construction_light_client_section',
			    'settings' 	   => 'construction_light_client',
			    'cl_box_label' => esc_html__('Client Logo Setting Options', 'construction-light'),
			    'cl_box_add_control' => esc_html__('Add New', 'construction-light'),
			),

		    array(

		        'client_image' => array(
		            'type' => 'upload',
		            'label' => esc_html__('Upload Clients Logo', 'construction-light'),
		        ),

		        'client_link' => array(
					'type'      => 'text',
					'label'     => esc_html__( 'Enter Client Logo Link', 'construction-light' ),
					'default'   => ''
				), 
			)
		));



	/**
	 * Theme Option Settings.
	*/
	$wp_customize->add_panel('construction_light_theme_options', array(
		'title'		=>	esc_html__('Theme Options','construction-light'),
		'priority'	=>	55,
	));

		// Site Layout.
		$wp_customize->add_section('construction_light_site_layout_section', array(
			'title'		=>	esc_html__('Site Layout','construction-light'),
			'panel'		=> 'construction_light_theme_options',
		));

			// Site Layout Options.
			$wp_customize->add_setting('construction_light_site_layout', array(
				'default' => 'full_width',
				'sanitize_callback' => 'construction_light_sanitize_select'         //done
			));

			$wp_customize->add_control('construction_light_site_layout', array(
				'label'   => esc_html__('Site Layout','construction-light'),
				'section' => 'construction_light_site_layout_section',
				'type'    => 'select',
				'choices' => array(
					'full_width' => esc_html__('Full Width','construction-light'),
					'boxed' => esc_html__('Boxed','construction-light'),			
				)
			));

		/**
		 * Page Layout Sidebar Options
		*/
		$wp_customize->add_section('construction_light_sidebar', array(
			'title'		=>	esc_html__('Display Sidebar Settings','construction-light'),
			'panel'		=> 'construction_light_theme_options',
		));

			// Enable or Disable Sticky Sidebar.
			$wp_customize->add_setting('construction_light_sticky_sidebar', array(
			    'default' => 'enable',
			    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
			));

			$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_sticky_sidebar', array(
			    'label' => esc_html__('Enable Sticky Sidebar', 'construction-light'),
			    'settings' => 'construction_light_sticky_sidebar',
			    'section' => 'construction_light_sidebar',
			    'switch_label' => array(
			        'enable' => esc_html__('Enable', 'construction-light'),
			        'disable' => esc_html__('Disable', 'construction-light'),
			    ),
			)));


			// Blog Sidebar Options.
			$wp_customize->add_setting('construction_light_blog_sidebar', array(
			    'default' => 'right',
			    'sanitize_callback' => 'construction_light_sanitize_select',     //done
			));

			$wp_customize->add_control('construction_light_blog_sidebar', array(
			    'label'   => esc_html__('Index Blog Posts Sidebar', 'construction-light'),
			    'section' => 'construction_light_sidebar',
			    'type'    => 'select',
			    'choices' => array(
			        'right' => esc_html__('Content / Sidebar', 'construction-light'),
			        'left' => esc_html__('Sidebar / Content', 'construction-light'),
			        'no' => esc_html__('Full Width', 'construction-light'),
			    ),
			));

			// Blog Archive Sidebar Options.
			$wp_customize->add_setting('construction_light_archive_sidebar', array(
			    'default' => 'right',
			    'sanitize_callback' => 'construction_light_sanitize_select',     //done
			));

			$wp_customize->add_control('construction_light_archive_sidebar', array(
			    'label'   => esc_html__('Blog Archive Sidebar', 'construction-light'),
			    'section' => 'construction_light_sidebar',
			    'type'    => 'select',
			    'choices' => array(
			        'right' => esc_html__('Content / Sidebar', 'construction-light'),
			        'left' => esc_html__('Sidebar / Content', 'construction-light'),
			        'no' => esc_html__('Full Width', 'construction-light'),	        
			    ),
			));

			// Page Sidebar Options.
			$wp_customize->add_setting('construction_light_page_sidebar', array(
			    'default' => 'right',
			    'sanitize_callback' => 'construction_light_sanitize_select',     //done
			));

			$wp_customize->add_control('construction_light_page_sidebar', array(
			    'label'   => esc_html__('Page Sidebar', 'construction-light'),
			    'section' => 'construction_light_sidebar',
			    'type'    => 'select',
			    'choices' => array(
			        'right' => esc_html__('Content / Sidebar', 'construction-light'),
			        'left' => esc_html__('Sidebar / Content', 'construction-light'),
			        'no' => esc_html__('Full Width', 'construction-light'),	        
			    ),
			));

			// Search Page Sidebar Options.
			$wp_customize->add_setting('construction_light_search_sidebar', array(
			    'default' => 'right',
			    'sanitize_callback' => 'construction_light_sanitize_select',     //done
			));

			$wp_customize->add_control('construction_light_search_sidebar', array(
			    'label'   => esc_html__('Search Page Sidebar', 'construction-light'),
			    'section' => 'construction_light_sidebar',
			    'type'    => 'select',
			    'choices' => array(
			        'right' => esc_html__('Content / Sidebar', 'construction-light'),
			        'left' => esc_html__('Sidebar / Content', 'construction-light'),
			        'no' => esc_html__('Full Width', 'construction-light'),	        
			    ),
			));


		/**
		 * Breadcrumbs Settings. 
		*/
		$wp_customize->add_section('construction_light_breadcrumb', array(
			'title'		=>	esc_html__('Breadcrumbs Settings','construction-light'),
			'panel'		=> 'construction_light_theme_options',
		));

		    // Enable or Disable Breadcrumb.
			$wp_customize->add_setting('construction_light_enable_breadcrumbs', array(
			    'default' => 'enable',
			    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
			));

			$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_enable_breadcrumbs', array(
			    'label' => esc_html__('Enable/Disable Breadcrumbs', 'construction-light'),
			    'settings' => 'construction_light_enable_breadcrumbs',
			    'section' => 'construction_light_breadcrumb',
			    'switch_label' => array(
			        'enable' => esc_html__('Enable', 'construction-light'),
			        'disable' => esc_html__('Disable', 'construction-light'),
			    ),
			)));

		    // Breadcrumb Image.
			$wp_customize->add_setting('construction_light_breadcrumbs_image', array(
				'sanitize_callback'	=> 'esc_url_raw'		//done
			));

			$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'construction_light_breadcrumbs_image', array(
				'label'	   => esc_html__('Breadcrumbs Background Image','construction-light'),
				'section'  => 'construction_light_breadcrumb',
				'type'	   => 'image',
			)));


		/**
		 * Blog Template.
		*/
		$wp_customize->add_section('construction_light_blog_template', array(
			'title'		  => esc_html__('Blog Template Settings','construction-light'),
			'priority'	  => 65,
		));


			//  Blog Template Blog Posts by Category.
			$wp_customize->add_setting('construction_light_blogtemplate_postcat', array(
			    'sanitize_callback' => 'sanitize_text_field',     //done
			));

			$wp_customize->add_control(new Construction_Light_Multiple_Check_Control($wp_customize, 'construction_light_blogtemplate_postcat', array(
			    'label'    => esc_html__('Select Category To Show Posts', 'construction-light'),
			    'settings' => 'construction_light_blogtemplate_postcat',
			    'section'  => 'construction_light_blog_template',
			    'choices'  => $blog_cat,
			    'description' => esc_html__('Note: Selected Category Only Work When you can select page template (
			    	Blog Template )','construction-light'),
			)));



			// Blog Sidebar Options.
			$wp_customize->add_setting('construction_light_blog_template_sidebar', array(
			    'default' => 'right',
			    'sanitize_callback' => 'construction_light_sanitize_select',     //done
			));

			$wp_customize->add_control('construction_light_blog_template_sidebar', array(
			    'label'   => esc_html__('Blog Template Layout Settings', 'construction-light'),
			    'section' => 'construction_light_blog_template',
			    'type'    => 'select',
			    'description' => esc_html__('Note: Blog Template Layout Only Work When you can select page template ( Blog Template )','construction-light'),
			    'choices' => array(
			        'right' => esc_html__('Content / Sidebar', 'construction-light'),
			        'left' => esc_html__('Sidebar / Content', 'construction-light'),
			        'no' => esc_html__('Full Width', 'construction-light'),
			    ),
			));


		$post_layout = array(
	        'none'  => esc_html__( 'Normal Layout', 'construction-light' ),
	        'masonry2-rsidebar'  => esc_html__( 'Masonry Layout', 'construction-light' )
	    );

			// Blog Template Layout.
			$wp_customize->add_setting('construction_light_blogtemplate_layout', array(
				'default'		=>	'none',
				'sanitize_callback'	=> 'construction_light_sanitize_select',	//done
			));

			$wp_customize->add_control('construction_light_blogtemplate_layout', array(
				'label'		=>	esc_html__('Post Display Layout','construction-light'),
				'section'	=> 'construction_light_blog_template',
				'type'		=> 'select',
				'choices' 	=> $post_layout
			));


		$post_description = array(
	        'none'     => esc_html__( 'None', 'construction-light' ),
	        'excerpt'  => esc_html__( 'Post Excerpt', 'construction-light' ),
	        'content'  => esc_html__( 'Post Content', 'construction-light' )
	    );
	        
	        $wp_customize->add_setting( 
	            'construction_light_post_description_options', 

	            array(
	                'default'           => 'excerpt',
	                'sanitize_callback' => 'construction_light_sanitize_select'
	            ) 
	        );
	        
	        $wp_customize->add_control( 
	            'construction_light_post_description_options', 

	            array(
	                'type' => 'select',
	                'label' => esc_html__( 'Post Description', 'construction-light' ),
	                'section' => 'construction_light_blog_template',
	                'choices' => $post_description
	            ) 
	        );


			// Blog Template Read More Button.
			$wp_customize->add_setting( 'construction_light_blogtemplate_btn', array(
				'default'           => esc_html__( 'Continue Reading','construction-light' ),
				'sanitize_callback' => 'sanitize_text_field',		//done
			));

			$wp_customize->add_control('construction_light_blogtemplate_btn', array(
				'label'		  => esc_html__( 'Enter Blog Button Text', 'construction-light' ),
				'section'	  => 'construction_light_blog_template',
				'type' 		  => 'text',
			));


			/**
	         * Number field for Excerpt Length section
	         *
	         * @since 1.0.0
	         */
	        $wp_customize->add_setting(
	            'construction_light_post_excerpt_length',
	            array(
	                'default'    => 50,
	                'sanitize_callback' => 'absint'
	            )
	        );

	        $wp_customize->add_control(
	            'construction_light_post_excerpt_length',

	            array(
	                'type'      => 'number',
	                'label'     => esc_html__( 'Enter Posts Excerpt Length', 'construction-light' ),
	                'section'   => 'construction_light_blog_template',
	            )
	        );


	        /**
	         * Enable/Disable Option for Post Elements Date
	         *
	         * @since 1.0.0
	        */
	        $wp_customize->add_setting('construction_light_post_date_options', array(
			    'default' => 'enable',
			    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
			));

			$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_post_date_options', array(
			    'label' => esc_html__('Post Meta Date', 'construction-light'),
			    'settings' => 'construction_light_post_date_options',
			    'section' => 'construction_light_blog_template',
			    'switch_label' => array(
			        'enable' => esc_html__('Enable', 'construction-light'),
			        'disable' => esc_html__('Disable', 'construction-light'),
			    ),
			)));


	        /**
	         * Enable/Disable Option for Post Elements Comments
	         *
	         * @since 1.0.0
	         */
	        $wp_customize->add_setting('construction_light_post_comments_options', array(
			    'default' => 'enable',
			    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
			));

			$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_post_comments_options', array(
			    'label' => esc_html__('Post Meta Comments', 'construction-light'),
			    'settings' => 'construction_light_post_comments_options',
			    'section' => 'construction_light_blog_template',
			    'switch_label' => array(
			        'enable' => esc_html__('Enable', 'construction-light'),
			        'disable' => esc_html__('Disable', 'construction-light'),
			    ),
			)));


	        /**
	         * Enable/Disable Option for Post Elements Tags
	         *
	         * @since 1.0.0
	         */
	        $wp_customize->add_setting('construction_light_post_author_options', array(
			    'default' => 'enable',
			    'sanitize_callback' => 'construction_light_sanitize_switch',     //done
			));

			$wp_customize->add_control(new Construction_Light_Switch_Control($wp_customize, 'construction_light_post_author_options', array(
			    'label' => esc_html__('Post Meta Author', 'construction-light'),
			    'settings' => 'construction_light_post_author_options',
			    'section' => 'construction_light_blog_template',
			    'switch_label' => array(
			        'enable' => esc_html__('Enable', 'construction-light'),
			        'disable' => esc_html__('Disable', 'construction-light'),
			    ),
			)));

}
add_action( 'customize_register', 'construction_light_customize_register' );


/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function construction_light_customize_partial_blogname() {

	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function construction_light_customize_partial_blogdescription() {

	bloginfo( 'description' );
}


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
*/
function construction_light_customize_preview_js() {

	wp_enqueue_script( 'construction-light-customizer', get_template_directory_uri() . '/assets/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'construction_light_customize_preview_js' );


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * Enqueue required scripts/styles for customizer panel
 *
 * @since 1.0.0
 *
 */
function construction_light_customize_scripts(){

   wp_enqueue_style( 'fontawesome', get_template_directory_uri(). '/assets/library/fontawesome/css/all.min.css');

    wp_enqueue_style('construction-light-customizer', get_template_directory_uri() . '/assets/css/customizer.css');

    wp_enqueue_script('construction-light-customizer', get_template_directory_uri() . '/assets/js/customizer-admin.js', array('jquery', 'customize-controls'), true);
}
add_action('customize_controls_enqueue_scripts', 'construction_light_customize_scripts');


/**
 * Section Re Order
*/
add_action('wp_ajax_construction_light_sections_reorder', 'construction_light_sections_reorder');

function construction_light_sections_reorder() {

    if (isset($_POST['sections'])) {

        set_theme_mod('construction_light_frontpage_sections', $_POST['sections']);
    }

    wp_die();
}

function construction_light_get_section_position($key) {

    $sections = construction_light_homepage_section();

    $position = array_search($key, $sections);

    $return = ( $position + 1 ) * 11;

    return $return;
}

if( !function_exists('construction_light_homepage_section') ){

	function construction_light_homepage_section(){

		$defaults = apply_filters('construction_light_homepage_sections',
			array(
				'construction_light_promoservice_section',
				'construction_light_aboutus_section',
				'construction_light_video_calltoaction_section',
				'construction_light_service_section',
				'construction_light_calltoaction_section',
				'construction_light_recentwork_section',
				'construction_light_counter_section',
				'construction_light_blog_section',
				'construction_light_testimonial_section',
				'construction_light_team_section',
				'construction_light_client_section'
			)
		);

		$sections = get_theme_mod('construction_light_frontpage_sections', $defaults);
		
        return $sections;
	}
}

