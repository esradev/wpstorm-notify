(function ($) {
  "use strict";
  let newsletter_send_ver_code = $("#newsletter_send_ver_code");
  let submit_div = $(".newsletter_submit");
  let submit_button = $("#newsletter_submit_button");
  let submit_code = $("#newsletter_submit_code");
  let resend_code = $("#newsletter_resend_code");
  let newsletter_completion_div = $("#newsletter_completion");
  let name = $("#newsletter_name");
  let mobile = $("#newsletter_mobile");
  let verify_code = $("#newsletter_verify_code");
  let newsletter_message = $("#newsletter_message");

  let has_error = false;
  submit_button.click(function () {
    has_error = false;
    name.removeClass("error");
    mobile.removeClass("error");
    if (name.val() === "") {
      has_error = true;
      name.addClass("error");
    }
    if (mobile.val().length < 10) {
      has_error = true;
      mobile.addClass("error");
    }
    if (has_error) {
      return;
    }
    let data = {
      action: "newsletter_send_verification_code",
      mobile: mobile.val(),
      name: name.val(),
    };
    submit_button.addClass("button--loading");
    submit_button.prop("disabled", true);
    $.post(ajax_object.ajax_url, data, function (response) {
      submit_button.removeClass("button--loading");
      if (response.success) {
        if (newsletter_send_ver_code.val().length === 0) {
          newsletter_message.removeClass("success error");
          newsletter_message.hide();
          newsletter_message.empty();
          newsletter_message.addClass("success");
          newsletter_message.append("ثبت نام با موفقیت انجام شد");
          newsletter_message.show();
        } else {
          submit_div.hide();
          $(".newsletter_input.a").hide();
          newsletter_completion_div.show();
          $(".newsletter_input.b").show();
          let seconds = 90;
          let interval;
          resend_code.prop("disabled", true);
          interval = setInterval(function () {
            resend_code
              .find("span")
              .html("ارسال مجدد کد" + " (" + seconds + ")");
            if (seconds === 0) {
              resend_code.find("span").html("ارسال مجدد کد");
              resend_code.prop("disabled", false);
              clearInterval(interval);
            }
            seconds--;
          }, 1000);
        }
      } else {
        newsletter_message.addClass("error");
        newsletter_message.append("شما عضو خبرنامه هستید");
        newsletter_message.show();
      }
    });
  });

  resend_code.click(function () {
    submit_button.click();
  });

  submit_code.click(function () {
    has_error = false;
    verify_code.removeClass("error");
    if (verify_code.val() == "" || verify_code.val().length !== 4) {
      has_error = true;
      verify_code.addClass("error");
    }
    if (has_error) {
      return;
    }
    let data = {
      action: "add_phone_to_newsletter",
      code: verify_code.val(),
      name: name.val(),
      mobile: mobile.val(),
    };
    submit_code.addClass("button--loading");
    submit_code.prop("disabled", true);
    $.post(ajax_object.ajax_url, data, function (response) {
      submit_code.removeClass("button--loading");
      submit_code.prop("disabled", false);
      newsletter_message.removeClass("success error");
      newsletter_message.hide();
      newsletter_message.empty();
      if (response.success) {
        newsletter_message.addClass("success");
        newsletter_message.append("ثبت نام با موفقیت انجام شد");
        newsletter_message.show();
        newsletter_completion_div.hide();
      } else {
        newsletter_message.addClass("error");
        newsletter_message.append("کد وارد شده صحیح نیست");
        newsletter_message.show();
      }
    });
  });
})(jQuery);
