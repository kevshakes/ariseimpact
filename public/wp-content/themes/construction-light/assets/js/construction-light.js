var ConstructionLight = ConstructionLight || {};

ConstructionLight.primaryMenu = {

    init: function() {
        this.focusMenuWithChildren();
    },

    // The focusMenuWithChildren() function implements Keyboard Navigation in the Primary Menu
    // by adding the '.focus' class to all 'li.menu-item-has-children' when the focus is on the 'a' element.
    focusMenuWithChildren: function() {
        // Get all the link elements within the primary menu.
        var links, i, len,
            menu = document.querySelector( '.box-header-nav' );

        if ( ! menu ) {
            return false;
        }

        links = menu.getElementsByTagName( 'a' );

        // Each time a menu link is focused or blurred, toggle focus.
        for ( i = 0, len = links.length; i < len; i++ ) {
            links[i].addEventListener( 'focus', toggleFocus, true );
            links[i].addEventListener( 'blur', toggleFocus, true );
        }

        //Sets or removes the .focus class on an element.
        function toggleFocus() {
            var self = this;

            // Move up through the ancestors of the current link until we hit .primary-menu.
            while ( -1 === self.className.indexOf( 'main-menu' ) ) {
                // On li elements toggle the class .focus.
                if ( 'li' === self.tagName.toLowerCase() ) {
                    if ( -1 !== self.className.indexOf( 'focus' ) ) {
                        self.className = self.className.replace( ' focus', '' );
                    } else {
                        self.className += ' focus';
                    }
                }
                self = self.parentElement;
            }
        }
    }
}; // ConstructionLight.primaryMenu

jQuery(document).ready(function ($){

    /**
     * Call Primary Menu Focus Class
    */
    ConstructionLight.primaryMenu.init();    // Primary Menu


    /**
     * Add RTL Class in Body
    */
    var brtl;

    if ($("body").hasClass('rtl')) {

        brtl = true;

    }else{

        brtl = false;
    }

    /**
     * Header Search
    */
    $('.menu-item-search a').click(function() {
        $('.conslight-search-wrapper').addClass('conslight-search-triggered');
        setTimeout(function() {
            $('.conslight-search-wrapper .search-field').focus();
        }, 1000);
    });

    $('.conslight-close-icon').click(function() {
        $('.conslight-search-wrapper').removeClass('conslight-search-triggered');
    });

    
    /**
     * Banner Slider
    */
    $(".features-slider").owlCarousel({
       items: 1,
       loop: true,
       smartSpeed: 2000,
       dots: true,
       nav: false,
       autoplay: true,
       mouseDrag: true,
       rtl: brtl,
       responsive: {
          0: {
             nav: false,
             mouseDrag: false,
             touchDrag:false,
          },
          600: {
             nav: false,
             mouseDrag: false,
             touchDrag:false,

          },
          1000: {
             nav: true,
             mouseDrag: true,
             touchDrag:true,

          }
       }
    });


    /**
     * scrollTop To Top
    */
    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            $('#back-to-top').addClass('show');
        } else {
            $('#back-to-top').removeClass('show');
        }
    });

    $('#back-to-top').click(function(e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 800);
    });
    
    var progressPath = document.querySelector('.progress path');
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 300ms linear';
    var updateProgress = function() {
        var scroll = $(window).scrollTop();
        var height = $(document).height() - $(window).height();
        var percent = Math.round(scroll * 100 / height);
        var progress = pathLength - (scroll * pathLength / height);
        progressPath.style.strokeDashoffset = progress;
        $('.percent').text(percent + "%");
    };
    updateProgress();
    $(window).scroll(updateProgress);


    /**
     * Theia sticky slider
    */
    var sticky_sidebar = construction_light_script.sticky_sidebar;

    if( sticky_sidebar == 'enable' ){
        try{
            $('.content-area').theiaStickySidebar({
                additionalMarginTop: 30
            });

            $('.widget-area').theiaStickySidebar({
                additionalMarginTop: 30
            });
        }
        catch(e){
            //console.log( e );
        }
    }

    /**
     * Video popup
    */
    $('.popup-youtube, .popup-vimeo, .popup-gmaps').magnificPopup({
        disableOn: 700,
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false
    });


    /**
     * Isotop Portfolio
    */
    if ($('.cons_light_portfolio-posts').length > 0) {

        var first_class = $('.cons_light_portfolio-cat-name:first').data('filter');

        var $container = $('.cons_light_portfolio-posts').imagesLoaded(function() {

            $container.isotope({
                itemSelector: '.cons_light_portfolio',
                filter: first_class
            });

            var elems = $container.isotope('getFilteredItemElements');

            elems.forEach(function(item, index) {
                if (index == 0 || index == 4) {
                    $(item).addClass('wide');
                    var bg = $(item).find('.cons_light_portfolio-image').attr('href');
                    $(item).find('.cons_light_portfolio-wrap').css('background-image', 'url(' + bg + ')');
                } else {
                    $(item).removeClass('wide');
                }
            });

            GetMasonary();

            setTimeout(function() {
                $container.isotope({
                    itemSelector: '.cons_light_portfolio',
                    filter: first_class,
                });
            }, 2000);

            $(window).on('resize', function() {
                GetMasonary();
            });

        });

        $('.cons_light_portfolio-cat-name-list').on('click', '.cons_light_portfolio-cat-name', function() {

            $('.cons_light_portfolio-cat-name-list').find('.cons_light_portfolio-cat-name').removeClass('active');

            var filterValue = $(this).attr('data-filter');

            $container.isotope({
                filter: filterValue
            });

            var elems = $container.isotope('getFilteredItemElements');

            elems.forEach(function(item, index) {
                if (index == 0 || index == 4) {
                    $(item).addClass('wide');
                    var bg = $(item).find('.cons_light_portfolio-image').attr('href');
                    $(item).find('.cons_light_portfolio-wrap').css('background-image', 'url(' + bg + ')');
                } else {
                    $(item).removeClass('wide');
                }
            });

            GetMasonary();

            var filterValue = $(this).attr('data-filter');
            $container.isotope({
                filter: filterValue
            });

            $('.cons_light_portfolio-cat-name').removeClass('active');
            $(this).addClass('active');
        });

        function GetMasonary() {
            var winWidth = window.innerWidth;
            if (winWidth > 580) {

                $container.find('.cons_light_portfolio').each(function() {
                    var image_width = $(this).find('img').width();
                    if ($(this).hasClass('wide')) {
                        $(this).find('.cons_light_portfolio-wrap').css({
                            height: (image_width * 2) + 15 + 'px'
                        });
                    } else {
                        $(this).find('.cons_light_portfolio-wrap').css({
                            height: image_width + 'px'
                        });
                    }
                });

            } else {
                $container.find('.cons_light_portfolio').each(function() {
                    var image_width = $(this).find('img').width();
                    if ($(this).hasClass('wide')) {
                        $(this).find('.cons_light_portfolio-wrap').css({
                            height: (image_width * 2) + 8 + 'px'
                        });
                    } else {
                        $(this).find('.cons_light_portfolio-wrap').css({
                            height: image_width + 'px'
                        });
                    }
                });
            }
        }

    }


    /**
     * Portfolio Open Light Box
    */
    $('.cons_light_portfolio-image').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        mainClass: 'mfp-img-mobile',
        image: {
            verticalFit: true
        }
    });

    /**
     * About us Achiments Awards Counter
    */
    $('.achivement').counterUp();


    /**
     * Success Product Counter
    */
    $('.cons_light_team-counter-wrap').waypoint(function() {
        setTimeout(function() {
          $('.odometer1').html($('.odometer1').data('count'));
        }, 500);
        setTimeout(function() {
          $('.odometer2').html($('.odometer2').data('count'));
        }, 1000);
        setTimeout(function() {
          $('.odometer3').html($('.odometer3').data('count'));
        }, 1500);
        setTimeout(function() {
          $('.odometer4').html($('.odometer4').data('count'));
        }, 2000);
        setTimeout(function() {
          $('.odometer5').html($('.odometer5').data('count'));
        }, 2500);
        setTimeout(function() {
          $('.odometer6').html($('.odometer6').data('count'));
        }, 3000);
        setTimeout(function() {
          $('.odometer7').html($('.odometer7').data('count'));
        }, 3500);
        setTimeout(function() {
          $('.odometer8').html($('.odometer8').data('count'));
        }, 4000);
    }, {
      offset: 800,
      triggerOnce: true
    });


    /**
     * Masonry Posts Layout
    */
    var grid = document.querySelector(
            '.construction-masonry'
        ),
        masonry;

    if (
        grid &&
        typeof Masonry !== undefined &&
        typeof imagesLoaded !== undefined
    ) {
        imagesLoaded( grid, function( instance ) {
            masonry = new Masonry( grid, {
                itemSelector: '.hentry',
                gutter: 15
            } );
        } );
    }

    /**
     * Testimonial
    */
    $('.testimonial_slider').owlCarousel({
        loop: true,
        margin: 10,
        dots: true,
        smartSpeed: 2000,
        autoplay: true,
        autoplayTimeout: 5000,
        nav: true,
        navText: ["<i class='fas fa-angle-left'></i>", "<i class='fas fa-angle-right'></i>"],
        items: $('.testimonial_slider').data('columns') || 3,
        rtl: brtl,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    });


    /**
     * Client logo owl slider
    */
    $(' .client_logo').owlCarousel({
        loop: true,
        margin: 10,
        dots: true,
        nav: false,
        autoplay: true,
        smartSpeed: 3000,
        autoplayTimeout: 5000,
        rtl: brtl,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 5
            }
        }

    });



    /**
     * Responsive Menu Toggle
    */
    $('.header-nav-toggle').click(function(){
        $('.header-nav-toggle').toggleClass('on');
        $('.box-header-nav').slideToggle('1000');
    });

    /**
     * Add Icon Sub Menu
    */
    $('.box-header-nav .menu-item-has-children').append('<span class="sub-toggle"><i class="fas fa-plus"></i></span>');
    //$('.box-header-nav .page_item_has_children').append('<span class="sub-toggle-children"> <i class="fas fa-plus"></i> </span>');

    $('.box-header-nav .sub-toggle').click(function () {
        $(this).parent('.menu-item-has-children').children('ul.sub-menu').first().toggle();
        $(this).children('.fa-plus').first().toggleClass('fa-minus');
    });

    $(".header-nav-toggle").keydown(function(event) {
        if (event.keyCode === 13) {
          event.preventDefault();
          jQuery(".box-header-nav.main-menu-wapper").show();
        }
    });

    /********************
     *  search
     * *****************/
    $('.search_main_menu a').click(function () {
        $('.ss-content').addClass('ss-content-act');
    });
    $('.ss-close').click(function () {
        $('.ss-content').removeClass('ss-content-act');
    });

    /********************
     *  init wow js
     * *****************/
    var wow = new WOW(
        {
            boxClass: 'wow',      // animated element css class (default is wow)
            animateClass: 'animated', // animation css class (default is animated)
            offset: 0,          // distance to the element when triggering the animation (default is 0)
            mobile: true,       // trigger animations on mobile devices (default is true)
            live: true,       // act on asynchronously loaded content (default is true)
            callback: function (box) {
                // the callback is fired every time an animation is started
                // the argument that is passed in is the DOM node being animated
            },
            scrollContainer: null,    // optional scroll container selector, otherwise use window,
            resetAnimation: true,     // reset animation on end (default is true)
        }
    );
    wow.init();

});