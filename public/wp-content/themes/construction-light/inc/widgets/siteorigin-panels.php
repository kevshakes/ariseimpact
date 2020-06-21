<?php
/**
 * Adds Construction Light Theme Widgets in SiteOrigin Pagebuilder Tabs
 *
 * @since Construction Light
 *
 * @param null
 * @return null
 *
 */
function construction_light_widgets($widgets) {
    $theme_widgets = array(
        'construction_light_widget_aboutus',
        'construction_light_blogs',
        'construction_light_widget_clients',
        'construction_light_widget_counter',
        'construction_light_widget_featured_service',
        'construction_light_portfolio',
        'construction_light_widget_service',
        'construction_light_widget_team',
        'construction_light_widget_testimonial',
        'construction_light_widget_calltoaction_video',
        'construction_light_widget_calltoaction'
    );
    foreach($theme_widgets as $theme_widget) {
        if( isset( $widgets[$theme_widget] ) ) {
            $widgets[$theme_widget]['groups'] = array('construction-light');
            $widgets[$theme_widget]['icon']   = 'dashicons dashicons-screenoptions';
        }
    }
    return $widgets;
}
add_filter('siteorigin_panels_widgets', 'construction_light_widgets');

/**
 * Add a tab for the theme widgets in the page builder
 *
 * @param null
 * @return null
 *
 */
function construction_light_widgets_tab($tabs){
    $tabs[] = array(
        'title'  => esc_html__('Construction Light Widgets', 'construction-light'),
        'filter' => array(
            'groups' => array('construction-light')
        )
    );
    return $tabs;
}
add_filter('siteorigin_panels_widget_dialog_tabs', 'construction_light_widgets_tab', 20);