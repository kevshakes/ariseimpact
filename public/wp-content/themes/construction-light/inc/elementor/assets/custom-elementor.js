jQuery( window ).on( 'elementor/frontend/init', function() {
    //hook name is 'frontend/element_ready/{widget-name}.{skin} - i dont know how skins work yet, so for now presume it will
    //always be 'default', so for example 'frontend/element_ready/slick-slider.default'
    //$scope is a jquery wrapped parent element

    /**
     * Client
    */
    elementorFrontend.hooks.addAction( 'frontend/element_ready/construction-light-client.default', function($scope, $){
        $scope.find('.client_logo').owlCarousel({
            loop: true,
            margin: 10,
            dots: true,
            nav: false,
            autoplay: true,
            smartSpeed: 3000,
            autoplayTimeout: 5000,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 2
                },
                1000: {
                    items: 5
                }
            }

        });
    });

    /**
     * Testimonial
    */
    elementorFrontend.hooks.addAction( 'frontend/element_ready/construction-light-testimonial.default', function($scope, $){
        $scope.find('.testimonial_slider_ele').owlCarousel({
            loop: true,
            margin: 10,
            dots: true,
            smartSpeed: 2000,
            autoplay: true,
            autoplayTimeout: 5000,
            nav: true,
            navText: ["<i class='fas fa-angle-left'></i>", "<i class='fas fa-angle-right'></i>"],
            items: parseInt($scope.find('.testimonial_slider_ele').data('columns')) || 2
        });
    });

 } );