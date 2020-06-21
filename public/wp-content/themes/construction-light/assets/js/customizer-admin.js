( function( api ) {

    // Extends our custom "constructionlight" section.
    api.sectionConstructor['constructionlight'] = api.Section.extend( {

        // No events for this type of section.
        attachEvents: function () {},

        // Always make the section active.
        isContextuallyActive: function () {
            return true;
        }
    } );

} )( wp.customize );

jQuery(document).ready(function ($) {
    
    /**
     * Repeater Fields
     */
    function cl_refresh_repeater_values() {
        $(".cl-repeater-field-control-wrap").each(function () {
            var values = [];
            var $this = $(this);
            $this.find(".cl-repeater-field-control").each(function () {
                var valueToPush = {};
                $(this).find('[data-name]').each(function () {
                    var dataName = $(this).attr('data-name');
                    var dataValue = $(this).val();
                    valueToPush[dataName] = dataValue;
                });
                values.push(valueToPush);
            });
            $this.next('.cl-repeater-collector').val(JSON.stringify(values)).trigger('change');
        });
    }

    $('#customize-theme-controls').on('click', '.cl-repeater-field-title', function () {
        $(this).next().slideToggle();
        $(this).closest('.cl-repeater-field-control').toggleClass('expanded');
    });
    $('#customize-theme-controls').on('click', '.cl-repeater-field-close', function () {
        $(this).closest('.cl-repeater-fields').slideUp();
        ;
        $(this).closest('.cl-repeater-field-control').toggleClass('expanded');
    });

    $("body").on("click", '.cl-add-control-field', function () {
        var $this = $(this).parent();
        if (typeof $this != 'undefined') {
            var field = $this.find(".cl-repeater-field-control:first").clone();
            if (typeof field != 'undefined') {
                
                field.find("input[type='text'][data-name]").each(function () {
                    var defaultValue = $(this).attr('data-default');
                    $(this).val(defaultValue);
                });

                field.find("textarea[data-name]").each(function(){
                    var defaultValue = $(this).attr('data-default');
                    $(this).val(defaultValue);
                });
                field.find("select[data-name]").each(function () {
                    var defaultValue = $(this).attr('data-default');
                    $(this).val(defaultValue);
                });

                field.find(".cl-repeater-icon-list").each(function () {
                    var defaultValue = $(this).next('input[data-name]').attr('data-default');
                    $(this).next('input[data-name]').val(defaultValue);
                    $(this).prev('.cl-repeater-selected-icon').children('i').attr('class', '').addClass(defaultValue);

                    $(this).find('li').each(function () {
                        var icon_class = $(this).find('i').attr('class');
                        if (defaultValue == icon_class) {
                            $(this).addClass('icon-active');
                        } else {
                            $(this).removeClass('icon-active');
                        }
                    });
                });

                field.find(".attachment-media-view").each(function () {
                    var defaultValue = $(this).find('input[data-name]').attr('data-default');
                    $(this).find('input[data-name]').val(defaultValue);
                    if (defaultValue) {
                        $(this).find(".thumbnail-image").html('<img src="' + defaultValue + '"/>').prev('.placeholder').addClass('hidden');
                    } else {
                        $(this).find(".thumbnail-image").html('').prev('.placeholder').removeClass('hidden');
                    }
                });

                field.find('.cl-fields').show();

                $this.find('.cl-repeater-field-control-wrap').append(field);

                field.addClass('expanded').find('.cl-repeater-fields').show();
                $('.accordion-section-content').animate({scrollTop: $this.height()}, 1000);
                cl_refresh_repeater_values();
            }

        }
        return false;
    });

    $("#customize-theme-controls").on("click", ".cl-repeater-field-remove", function () {
        if (typeof    $(this).parent() != 'undefined') {
            $(this).closest('.cl-repeater-field-control').slideUp('normal', function () {
                $(this).remove();
                cl_refresh_repeater_values();
            });
        }
        return false;
    });

    $("#customize-theme-controls").on('keyup change', '[data-name]', function () {
        cl_refresh_repeater_values();
        return false;
    });

    // Set all variables to be used in scope
    var frame;
    // ADD IMAGE LINK
    $('.customize-control-repeater').on( 'click', '.cl-upload-button', function( event ){
        event.preventDefault();
        var imgContainer = $(this).closest('.cl-fields-wrap').find( '.thumbnail-image'),
        placeholder = $(this).closest('.cl-fields-wrap').find( '.placeholder'),
        imgIdInput = $(this).siblings('.upload-id');

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Image',
            button: {
            text: 'Use Image'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media frame...
        frame.on( 'select', function() {
            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();
            // Send the attachment URL to our custom image input field.
            imgContainer.html( '<img src="'+attachment.url+'" style="max-width:100%;"/>' );
            placeholder.addClass('hidden');
            // Send the attachment id to our hidden input
            imgIdInput.val( attachment.url ).trigger('change');
        });

        // Finally, open the modal on click
        frame.open();
    });


    // DELETE IMAGE LINK
    $('.customize-control-repeater').on( 'click', '.cl-delete-button', function( event ){

        event.preventDefault();
        var imgContainer = $(this).closest('.cl-fields-wrap').find( '.thumbnail-image'),
        placeholder = $(this).closest('.cl-fields-wrap').find( '.placeholder'),
        imgIdInput = $(this).siblings('.upload-id');

        // Clear out the preview image
        imgContainer.find('img').remove();
        placeholder.removeClass('hidden');

        // Delete the image id from the hidden input
        imgIdInput.val( '' ).trigger('change');

    });

    $('body').on('click', '.cl-selected-icon', function () {
        $(this).next().slideToggle();
    });

    /*Drag and drop to change order*/
    $(".cl-repeater-field-control-wrap").sortable({
        orientation: "vertical",
        update: function (event, ui) {
            cl_refresh_repeater_values();
        }
    });

    $('body').on('click', '.cl-repeater-icon-list li', function () {
        var icon_class = $(this).find('i').attr('class');
        $(this).addClass('icon-active').siblings().removeClass('icon-active');
        $(this).parent('.cl-repeater-icon-list').prev('.cl-repeater-selected-icon').children('i').attr('class', '').addClass(icon_class);
        $(this).parent('.cl-repeater-icon-list').next('input').val(icon_class).trigger('change');
        cl_refresh_repeater_values();
    });

    $('body').on('click', '.cl-repeater-selected-icon', function () {
        $(this).next().slideToggle();
    });

    /**
     * Select Multiple Category
     */
    $('.customize-control-checkbox-multiple input[type="checkbox"]').on('change', function () {

            var checkbox_values = $(this).parents('.customize-control').find('input[type="checkbox"]:checked').map(
                function () {
                    return $(this).val();
                }
            ).get().join(',');

            $(this).parents('.customize-control').find('input[type="hidden"]').val(checkbox_values).trigger('change');

        }
    );

    /*
      * Switch Enable/Disable Control.
    */
    $('body').on('click', '.onoffswitch', function () {
        var $this = $(this);
        if ($this.hasClass('switch-on')) {
            $(this).removeClass('switch-on');
            $this.next('input').val('disable').trigger('change')
        } else {
            $(this).addClass('switch-on');
            $this.next('input').val('enable').trigger('change')
        }
    });


    //Homepage Section Sortable
    function construction_light_sections_order(container) {
        var sections = $(container).sortable('toArray');
        var sec_ordered = [];
        $.each(sections, function (index, sec_id) {
            sec_id = sec_id.replace("accordion-section-", "");
            sec_ordered.push(sec_id);
        });

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'html',
            data: {
                'action': 'construction_light_sections_reorder',
                'sections': sec_ordered,
            }
        }).done(function (data) {
            $.each(sec_ordered, function (key, value) {
                wp.customize.section(value).priority(key);
            });
            $(container).find( '.construction_light-drag-spinner' ).hide();
            wp.customize.previewer.refresh();
        });
    }

    $('#sub-accordion-panel-construction_light_frontpage_settings').sortable({
        axis: 'y',
        helper: 'clone',
        cursor: 'move',
        items: '> li.control-section:not(#accordion-section-construction_light_slider_section)',
        delay: 150,
        update: function (event, ui) {
            $('#sub-accordion-panel-construction_light_frontpage_settings').find( '.construction_light-drag-spinner' ).show();
            construction_light_sections_order('#sub-accordion-panel-construction_light_frontpage_settings');
            $( '.wp-full-overlay-sidebar-content' ).scrollTop( 0 );
        }
    });


    //Scroll to section
    $('body').on('click', '#sub-accordion-panel-construction_light_frontpage_settings .control-subsection .accordion-section-title', function(event) {
        var section_id = $(this).parent('.control-subsection').attr('id');
        Construction_Light_ScrollToSection( section_id );
    });

});

function Construction_Light_ScrollToSection( section_id ){
    
    var preview_section_id = "banner-slider";

    var $contents = jQuery('#customize-preview iframe').contents();

    switch ( section_id ) {

        case 'sub-accordion-section-construction_light_slider_section':
        preview_section_id = "banner-slider";
        break;

        case 'accordion-section-construction_light_promoservice_section':
        preview_section_id = "cl_feature";
        break;

        case 'accordion-section-construction_light_aboutus_section':
        preview_section_id = "cl_aboutus";
        break;

        case 'accordion-section-construction_light_video_calltoaction_section':
        preview_section_id = "cl_ctavideo";
        break;

        case 'accordion-section-construction_light_service_section':
        preview_section_id = "cl_services";
        break;

        case 'accordion-section-construction_light_calltoaction_section':
        preview_section_id = "cl_cta";
        break;

        case 'accordion-section-construction_light_recentwork_section':
        preview_section_id = "cl_portfolio";
        break;

        case 'accordion-section-construction_light_counter_section':
        preview_section_id = "cl_counter";
        break;

        case 'accordion-section-construction_light_blog_section':
        preview_section_id = "cl_blog";
        break;

        case 'accordion-section-construction_light_testimonial_section':
        preview_section_id = "cl_testimonial";
        break;

        case 'accordion-section-construction_light_team_section':
        preview_section_id = "cl_team";
        break;

        case 'accordion-section-construction_light_client_section':
        preview_section_id = "cl_clients";
        break;

    }

    if( $contents.find('#'+preview_section_id).length > 0 ){
        $contents.find("html, body").animate({
        scrollTop: $contents.find( "#" + preview_section_id ).offset().top
        }, 1000);
    }
}