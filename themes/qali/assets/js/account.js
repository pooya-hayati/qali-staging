const msg = {
  error: "An error occurred, please try again later.",
  success: "Submitted Successfully.",
  wait: "Please wait…",
};

function showToast(type, message, title = null) {
  butterup.toast({
    title: title || (type === "success" ? "Success" : "Error"),
    message: message,
    type: type,
    location: "top-" + !directionConfig.endSide,
    dismissable: true,
  });
}

function handleFormAjax($form, action) {
  const $submitBtn = $form.find("button[type=submit]");
  const formData = $form.serialize();

  $.ajax({
    type: "POST",
    dataType: "json",
    url: URL_AJAX,
    data: {
      action: action,
      data: formData,
      security: account_params.nonce,
    },
    beforeSend: function () {
      $submitBtn.prop("disabled", true).data("original-text", $submitBtn.html()).html(msg.wait);
    },
    success: function (response) {
      if (response.success) {
        showToast("success", response.data.message);
        window.location = response.data.forward;
      } else {
        showToast("error", response.data.message);
      }
    },
    error: function () {
      showToast("error", msg.error);
    },
    complete: function () {
      $submitBtn.prop("disabled", false).html($submitBtn.data("original-text"));
    },
  });
}

$(document).ready(function () {
  $("#form-login").on("submit", function (e) {
    e.preventDefault();
    handleFormAjax($(this), "user_login");
  });

  $("#form-register").on("submit", function (e) {
    e.preventDefault();
    handleFormAjax($(this), "user_register");
  });
});
