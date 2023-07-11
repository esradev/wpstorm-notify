(function ($) {
  "use strict";
  let send_tracking_code_button = $("#send_tracking_code_button");
  let tracking_code = $("#tracking_code");
  let post_service_provider = $("#post_service_provider");
  let post_service_custom = $("#custom_provider");
  let post_date = $("#wpstorm-sms-tracking-date-field-value");
  let send_tracking_code_response = $("#send_tracking_code_response");
  let tracking_code_order_id = $("#tracking-code-order_id");

  send_tracking_code_button.click(function (event) {
    let post_service_provider_value;
    send_tracking_code_response.removeClass().empty().hide();
    let tracking_code_value = tracking_code.val();
    if (post_service_provider.val() === "custom_provider") {
      post_service_provider_value = post_service_custom.val();
    } else {
      post_service_provider_value = post_service_provider
        .find(":selected")
        .text();
    }
    let post_date_value = post_date.val();
    let data = {
      action: "send_tracking_code_sms",
      tracking_code: tracking_code_value,
      post_service_provider: post_service_provider_value,
      post_date: post_date_value,
      order_id: tracking_code_order_id.val(),
    };
    send_tracking_code_response.removeClass().empty().hide();
    send_tracking_code_button.addClass("button--loading");
    $.post(ajaxurl, data, function (response) {
      send_tracking_code_button.removeClass("button--loading");
      if (!response.success) {
        $("<span>" + response.data + "</span>").appendTo(
          send_tracking_code_response
        );
        send_tracking_code_response.addClass("error-message").show();
      } else {
        $("<span>کد رهگیری با موفقیت ارسال شد</span>").appendTo(
          send_tracking_code_response
        );
        send_tracking_code_response.addClass("success-message").show();
      }
    });
  });
})(jQuery);
