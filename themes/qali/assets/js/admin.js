(function ($) {
  'use strict';

  $(document).ready(function () {
    const $form = $('#custom-payment-form');
    const $resultDiv = $('#payment-link-result');
    const $generatedLink = $('#generated_link');
    const $testBtn = $('#test-link-btn');
    const $copyBtn = $('#copy-link-btn');
    const $generateAnotherBtn = $('#generate-another-btn');
    const $spinner = $('.spinner');

    // Form submission handler
    $form.on('submit', function (e) {
      e.preventDefault();

      const amount = parseFloat($('#payment_amount').val());

      // Validate amount
      if (!amount || amount <= 0) {
        alert(customPayAjax.strings.invalid_amount);
        $('#payment_amount').focus();
        return;
      }

      generatePaymentLink();
    });

    // Generate payment link via AJAX
    function generatePaymentLink() {
      $spinner.addClass('is-active');
      $form.find('button[type="submit"]').prop('disabled', true);

      const formData = {
        action: customPayAjax.action,
        _wpnonce: $form.find('input[name="_wpnonce"]').val(),
        amount: $('#payment_amount').val(),
        qty: $('#payment_qty').val() || 1,
        label: $('#payment_label').val(),
        ttl: $('#payment_ttl').val() || 24,
        from_order_id: $('#prefill_order_id').val() || 0
      };

      $.ajax({
        url: customPayAjax.ajaxurl,
        type: 'POST',
        data: formData,
        success: function (response) {
          if (response.success) {
            showResult(response.data.url);
          } else {
            showError(response.data.message || 'Unknown error occurred');
          }
        },
        error: function (xhr, status, error) {
          showError('Network error: ' + error);
        },
        complete: function () {
          $spinner.removeClass('is-active');
          $form.find('button[type="submit"]').prop('disabled', false);
        }
      });
    }

    // Show result section with generated link
    function showResult(url) {
      $generatedLink.val(url);
      $testBtn.attr('href', url);
      $resultDiv.slideDown();

      // Scroll to result
      $('html, body').animate({
        scrollTop: $resultDiv.offset().top - 50
      }, 500);
    }

    // Show error message
    function showError(message) {
      const errorHtml = `
                <div class="notice notice-error is-dismissible">
                    <p><strong>${customPayAjax.strings.error}:</strong> ${message}</p>
                </div>
            `;

      $form.before(errorHtml);

      // Auto-dismiss after 5 seconds
      setTimeout(function () {
        $('.notice-error').fadeOut();
      }, 5000);
    }

    // Copy link to clipboard
    $copyBtn.on('click', function (e) {
      e.preventDefault();

      $generatedLink.select();

      try {
        const successful = document.execCommand('copy');
        if (successful) {
          showToast(customPayAjax.strings.copied, 'success');
        } else {
          showToast(customPayAjax.strings.copy_failed, 'error');
        }
      } catch (err) {
        // Fallback for modern browsers
        if (navigator.clipboard) {
          navigator.clipboard.writeText($generatedLink.val()).then(function () {
            showToast(customPayAjax.strings.copied, 'success');
          }).catch(function () {
            showToast(customPayAjax.strings.copy_failed, 'error');
          });
        } else {
          showToast(customPayAjax.strings.copy_failed, 'error');
        }
      }
    });

    // Generate another link
    $generateAnotherBtn.on('click', function (e) {
      e.preventDefault();

      $resultDiv.slideUp();
      $form[0].reset();
      $('#payment_qty').val(1);
      $('#payment_ttl').val(24);
      $('#payment_amount').focus();
    });

    // Show toast notification
    function showToast(message, type) {
      const toastClass = type === 'success' ? 'notice-success' : 'notice-error';
      const toast = $(`
                <div class="notice ${toastClass} is-dismissible" style="position: fixed; top: 32px; right: 20px; z-index: 999999; min-width: 300px;">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);

      $('body').append(toast);

      // Auto-dismiss after 3 seconds
      setTimeout(function () {
        toast.fadeOut(function () {
          toast.remove();
        });
      }, 3000);

      // Manual dismiss
      toast.find('.notice-dismiss').on('click', function () {
        toast.fadeOut(function () {
          toast.remove();
        });
      });
    }

    // Auto-dismiss existing notices
    $(document).on('click', '.notice-dismiss', function () {
      $(this).closest('.notice').fadeOut();
    });

    // Focus on amount field on page load
    $('#payment_amount').focus();
  });

})(jQuery);