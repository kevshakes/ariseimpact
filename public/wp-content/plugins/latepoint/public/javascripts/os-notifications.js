"use strict";

function latepoint_add_notification(message) {
  var message_type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'success';
  var wrapper = jQuery('body').find('.os-notifications');

  if (!wrapper.length) {
    jQuery('body').append('<div class="os-notifications"></div>');
    wrapper = jQuery('body').find('.os-notifications');
  }

  if (wrapper.find('.item').length > 0) wrapper.find('.item:first-child').remove();
  wrapper.append('<div class="item item-type-' + message_type + '">' + message + '<span class="os-notification-close"><i class="latepoint-icon latepoint-icon-x"></i></span></div>');
}

//# sourceMappingURL=os-notifications.js.map
