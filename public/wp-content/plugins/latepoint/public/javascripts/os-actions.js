"use strict";

function latepoint_generate_form_message_html(messages, status) {
  var message_html = '<div class="os-form-message-w status-' + status + '"><ul>';

  if (Array.isArray(messages)) {
    messages.forEach(function (message) {
      message_html += '<li>' + message + '</li>';
    });
  } else {
    message_html += '<li>' + messages + '</li>';
  }

  message_html += '</ul></div>';
  return message_html;
}

function latepoint_clear_form_messages($form) {
  $form.find('.os-form-message-w').remove();
}

function latepoint_show_data_in_lightbox(message) {
  var extra_classes = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
  jQuery('.latepoint-lightbox-w').remove();
  var lightbox_css_classes = 'latepoint-lightbox-w latepoint-w ';
  if (extra_classes) lightbox_css_classes += extra_classes;
  jQuery('body').append('<div class="' + lightbox_css_classes + '"><div class="latepoint-lightbox-i">' + message + '<a href="#" class="latepoint-lightbox-close"><i class="latepoint-icon latepoint-icon-x"></i></a></div><div class="latepoint-lightbox-shadow"></div></div>');
  jQuery('body').addClass('latepoint-lightbox-active');
} // DOCUMENT READY


jQuery(document).ready(function ($) {
  /* 
    Ajax buttons action
  */
  $('.latepoint').on('click', 'button[data-os-action], a[data-os-action], div[data-os-action], span[data-os-action]', function (e) {
    var $this = $(this);
    if ($this.data('os-prompt') && !confirm($this.data('os-prompt'))) return false;
    var params = $(this).data('os-params');

    if ($(this).data('os-source-of-params')) {
      params = $($(this).data('os-source-of-params')).find('select, input, textarea').serialize();
    }

    var return_format = $this.data('os-return-format') ? $this.data('os-return-format') : 'json';
    var data = {
      action: 'latepoint_route_call',
      route_name: $(this).data('os-action'),
      params: params,
      return_format: return_format
    };
    $this.addClass('os-loading');
    $.ajax({
      type: "post",
      dataType: "json",
      url: latepoint_helper.ajaxurl,
      data: data,
      success: function success(response) {
        if (response.status === "success") {
          if ($this.data('os-output-target') == 'lightbox') {
            latepoint_show_data_in_lightbox(response.message, $this.data('os-lightbox-classes'));
          } else if ($this.data('os-output-target') == 'side-panel') {
            $('.latepoint-side-panel-w').remove();
            $('body').append('<div class="latepoint-side-panel-w"><div class="latepoint-side-panel-i">' + response.message + '</div><div class="latepoint-side-panel-shadow"></div></div>');
          } else if ($this.data('os-success-action') == 'reload') {
            latepoint_add_notification(response.message);
            location.reload();
            return;
          } else if ($this.data('os-success-action') == 'redirect') {
            if ($this.data('os-redirect-to')) {
              latepoint_add_notification(response.message);
              window.location.replace($this.data('os-redirect-to'));
            } else {
              window.location.replace(response.message);
            }

            return;
          } else if ($this.data('os-output-target') && $($this.data('os-output-target')).length) {
            if ($this.data('os-output-target-do') == 'append') {
              $($this.data('os-output-target')).append(response.message);
            } else {
              $($this.data('os-output-target')).html(response.message);
            }
          } else {
            if ($this.data('os-before-after') == 'before') {
              $this.before(response.message);
            } else if ($this.data('os-before-after') == 'before') {
              $this.after(response.message);
            } else {
              latepoint_add_notification(response.message);
            }
          }

          if ($this.data('os-after-call')) {
            var func_name = $this.data('os-after-call');

            if ($this.data('os-pass-this')) {
              window[func_name]($this);
            } else if ($this.data('os-pass-response')) {
              window[func_name](response);
            } else {
              window[func_name]();
            }
          }

          $this.removeClass('os-loading');
        } else {
          $this.removeClass('os-loading');

          if ($this.data('os-output-target') && $($this.data('os-output-target')).length) {
            $($this.data('os-output-target')).prepend(latepoint_generate_form_message_html(response.message, 'error'));
          } else {
            alert(response.message);
          }
        }
      }
    });
    return false;
  });
  $('.latepoint').on('click', 'form[data-os-action] button[type="submit"]', function (e) {
    $(this).addClass('os-loading');
  });
  /* 
    Form ajax submit action
  */

  $('.latepoint').on('submit', 'form[data-os-action]', function (e) {
    e.preventDefault(); // prevent native submit

    var $form = $(this);
    var form_data = $form.serialize();
    var data = {
      action: 'latepoint_route_call',
      route_name: $(this).data('os-action'),
      params: form_data,
      return_format: 'json'
    };
    $form.find('button[type="submit"]').addClass('os-loading');
    $.ajax({
      type: "post",
      dataType: "json",
      url: latepoint_helper.ajaxurl,
      data: data,
      success: function success(response) {
        $form.find('button[type="submit"].os-loading').removeClass('os-loading');
        latepoint_clear_form_messages($form);

        if (response.status === "success") {
          if ($form.data('os-success-action') == 'reload') {
            latepoint_add_notification(response.message);
            location.reload();
            return;
          } else if ($form.data('os-success-action') == 'redirect') {
            if ($form.data('os-redirect-to')) {
              latepoint_add_notification(response.message);
              window.location.replace($form.data('os-redirect-to'));
            } else {
              window.location.replace(response.message);
            }

            return;
          } else if ($form.data('os-output-target') && $($form.data('os-output-target')).length) {
            $($form.data('os-output-target')).html(response.message);
          } else {
            if (response.message == 'redirect') {
              window.location.replace(response.url);
            } else {
              latepoint_add_notification(response.message);
              $form.prepend(latepoint_generate_form_message_html(response.message, 'success'));
            }
          }

          if ($form.data('os-record-id-holder') && response.record_id) {
            $form.find('[name="' + $form.data('os-record-id-holder') + '"]').val(response.record_id);
          }

          if ($form.data('os-after-call')) {
            var func_name = $form.data('os-after-call');

            if ($form.data('os-pass-response')) {
              window[func_name](response);
            } else {
              window[func_name]();
            }
          }

          if (response.form_values_to_update) {
            $.each(response.form_values_to_update, function (name, value) {
              $form.find('[name="' + name + '"]').val(value);
            });
          }

          $('button.os-loading').removeClass('os-loading');
        } else {
          $('button.os-loading').removeClass('os-loading');

          if ($form.data('os-show-errors-as-notification')) {
            latepoint_add_notification(response.message, 'error');
          } else {
            $form.prepend(latepoint_generate_form_message_html(response.message, 'error'));
            $([document.documentElement, document.body]).animate({
              scrollTop: $form.find(".os-form-message-w").offset().top - 30
            }, 200);
          }
        }
      }
    });
    return false;
  });
});

//# sourceMappingURL=os-actions.js.map
