<header id="masthead" class="site-header headertwo">
	<div class="cons_light_top_bar">
        <div class="container">
        	<div class="row">
            	<div class="col-lg-6 col-md-6 col-sm-12 top-bar-menu left wow fadeInLeft">
	            	<?php
						$topheaderleft = get_theme_mod( 'construction_light_topheader_left', 'quick_contact' );
						
						if($topheaderleft == 'quick_contact'){    

						    construction_light_quick_contact();

						}else if($topheaderleft == 'social_media'){    

						    construction_light_topheader_social();

						}else{

							wp_nav_menu( array( 'theme_location' => 'menu-2', 'depth' => 1 ) );
						}
					?>
	            </div>

	            <div class="col-lg-6 col-md-6 col-sm-12 top-bar-menu right wow fadeInRight">
	            	<?php
						$topheaderright = get_theme_mod( 'construction_light_topheader_right', 'social_media' );

						if($topheaderright == 'quick_contact'){    

						    construction_light_quick_contact();

						}else if($topheaderright == 'social_media'){    

						    construction_light_topheader_social();

						}else{

							wp_nav_menu( array( 'theme_location' => 'menu-2', 'depth' => 1 ) );
						}
					?>
	            </div>
	        </div>
        </div>
    </div>

    <div class="nav-classic">
	    <div class="container">
	        <div class="row ">

	            <div class="col-lg-12 col-md-12">
	            	<div class="site-branding">
					
						<div class="brandinglogo-wrap">
				            <?php the_custom_logo(); ?>

				            <h1 class="site-title">
				                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				                    <?php bloginfo( 'name' ); ?>
				                </a>
				            </h1>
				            <?php 
				                $construction_light_description = get_bloginfo( 'description', 'display' );
				                if ( $construction_light_description || is_customize_preview() ) :?>
				                    <p class="site-description"><?php echo $construction_light_description; /* WPCS: xss ok. */ ?></p>
				            <?php endif; ?> 
				        </div> 

				        <button class="header-nav-toggle">
				            <div class="one"></div>
				            <div class="two"></div>
				            <div class="three"></div>
				        </button><!-- Mobile navbar toggler --> 

			        </div> <!-- .site-branding -->

	            </div><!-- Col end -->

	        </div><!-- .row end -->
	    </div><!-- .container end -->
	</div>


	<div class="nav-classic-wrap">
	    <div class="container">
	        <div class="row ">
	            <div class="col-lg-12 col-md-12 nav-wrap">
	            	<nav class="box-header-nav main-menu-wapper" aria-label="<?php esc_attr_e( 'Main Menu', 'construction-light' ); ?>" role="navigation">
						<?php
							wp_nav_menu( array(
									'theme_location'  => 'menu-1',
									'menu'            => 'primary-menu',
									'container'       => '',
									'container_class' => '',
									'container_id'    => '',
									'menu_class'      => 'main-menu',
								)
							);
						?>
	                </nav>
	            </div>
	        </div><!-- .row end -->
	    </div><!-- .container end -->
	</div>

</header><!-- #masthead -->