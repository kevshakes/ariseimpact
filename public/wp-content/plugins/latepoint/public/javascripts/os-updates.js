"use strict";

function latepoint_check_for_updates() {
  if (jQuery('.version-log-w').length) {
    var $log_wrapper = jQuery('.version-log-w');
    $log_wrapper.addClass('os-loading');
    var route = $log_wrapper.data('route');
    var data = {
      action: 'latepoint_route_call',
      route_name: route,
      params: '',
      return_format: 'json'
    };
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: latepoint_helper.ajaxurl,
      data: data,
      success: function success(response) {
        $log_wrapper.removeClass('os-loading');

        if (response.status === "success") {
          $log_wrapper.html(response.message);
        } else {
          alert(response.message, 'error');
        }
      }
    });
  }

  if (jQuery('.version-status-info').length) {
    var $version_info_wrapper = jQuery('.version-status-info');
    $version_info_wrapper.addClass('os-loading');
    var route = $version_info_wrapper.data('route');
    var data = {
      action: 'latepoint_route_call',
      route_name: route,
      params: '',
      return_format: 'json'
    };
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: latepoint_helper.ajaxurl,
      data: data,
      success: function success(response) {
        $version_info_wrapper.removeClass('os-loading');

        if (response.status === "success") {
          $version_info_wrapper.html(response.message);
        } else {
          alert(response.message, 'error');
        }
      }
    });
  }

  if (jQuery('.addons-info-holder').length) {
    var $addons_info_wrapper = jQuery('.addons-info-holder');
    $addons_info_wrapper.addClass('os-loading');
    var route = $addons_info_wrapper.data('route');
    var data = {
      action: 'latepoint_route_call',
      route_name: route,
      params: '',
      return_format: 'json'
    };
    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: latepoint_helper.ajaxurl,
      data: data,
      success: function success(response) {
        $addons_info_wrapper.removeClass('os-loading');

        if (response.status === "success") {
          $addons_info_wrapper.html(response.message);
        } else {
          alert(response.message, 'error');
        }
      }
    });
  }
} // DOCUMENT READY


jQuery(document).ready(function ($) {
  latepoint_check_for_updates();
});

//# sourceMappingURL=os-updates.js.map
