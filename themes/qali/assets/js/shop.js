// Translation
if (document.documentElement.lang.toLowerCase() === "en-us") {
  var msg_formError = "An error occurred, please try again later.";
  var msg_formSubmit = "Submitted Successfully.";
  var msg_wait = "Please wait…";
}
(function ($) {
  const paramKeys = [
    'product_category',
    'design',
    'color',
    'origin',
    'size',
    'sortby',
    'min_price',
    'max_price'
  ];

  /**
   * مسیر پایه سایت. از متغیر global استفاده می‌کنیم تا پروژه به پروژه قابل تنظیم باشه.
   * مثلاً:
   *   const URL_SITE = '/qali/';
   */
  function getBasePath() {
    return typeof URL_SITE !== 'undefined' ? URL_SITE : '/';
  }

  function getUpdatedParams(newParams = {}) {
    const urlParams = new URLSearchParams(window.location.search);

    // حذف صفحه‌بندی
    urlParams.delete('paged');

    // اگر post_type وجود نداشت، دستی اضافه کنیم
    if (!urlParams.has('post_type')) {
      urlParams.set('post_type', 'product');
    }

    // بروزرسانی یا حذف پارامترها
    Object.entries(newParams).forEach(([key, value]) => {
      if (value?.toString().trim()) {
        urlParams.set(key, value);
      } else {
        urlParams.delete(key);
      }
    });

    const queryString = urlParams.toString();
    return queryString ? `${getBasePath()}?${queryString}` : getBasePath();
  }

  function bindFilterChange(name) {
    $(document).on('change', `[name="${name}"]`, function () {
      const input = $(this);
      const isCheckbox = input.is(':checkbox');
      const isRadio = input.is(':radio');

      let val;

      if (isCheckbox) {
        const values = $(`[name="${name}"]:checked`)
          .map(function () { return this.value; })
          .get();
        val = values.join(',');
      } else {
        val = input.val();
      }

      window.location.href = getUpdatedParams({ [name]: val });
    });
  }

  paramKeys.forEach(bindFilterChange);

  // فیلتر بازه قیمت (range) فقط پس از توقف درگ اعمال شود
  $('.range-slider').each(function () {
    const slider = $(this);
    const minInput = slider.find('input[name="min_price"]');
    const maxInput = slider.find('input[name="max_price"]');

    let isDragging = false;
    let dragTimeout;

    const applyRangeFilter = () => {
      const min = minInput.val();
      const max = maxInput.val();
      window.location.href = getUpdatedParams({ min_price: min, max_price: max });
    };

    const debounceApply = () => {
      clearTimeout(dragTimeout);
      dragTimeout = setTimeout(() => {
        if (!isDragging) applyRangeFilter();
      }, 300);
    };

    slider.find('.min-thumb, .max-thumb').on('mousedown touchstart', () => {
      isDragging = true;
    });

    $(document).on('mouseup touchend', () => {
      if (isDragging) {
        isDragging = false;
        debounceApply();
      }
    });

    slider.on('range:done', () => {
      isDragging = false;
      debounceApply();
    });
  });

  // پشتیبانی از فرم عمومی با id="filter-form"
  $('#filter-form').on('submit', function (e) {
    e.preventDefault();
    const formData = $(this).serializeArray();
    const params = {};
    formData.forEach(item => {
      params[item.name] = item.value;
    });
    window.location.href = getUpdatedParams(params);
  });
})(jQuery);

$(document).ready(function () {

  // Product Add to cart
  $(".product-action-cart").on("click", function (e) {
    e.preventDefault();

    let button = $(this);
    let productID = button.data("product_id");
    let quantity = button.data("product_qty") || 1;

    $.ajax({
      url: URL_AJAX,
      type: "POST",
      data: {
        action: "add_to_cart",
        product_id: productID,
        quantity: quantity,
      },
      beforeSend: function () {
        button.button("loading");
      },
      success: function (response) {
        if (response.success) {
          updateCartCount();
          butterup.toast({
            title: "Success",
            message: response.data.message,
            type: "success",
            location: "top-" + !directionConfig.endSide,
            dismissable: true,
          });
        } else {
          butterup.toast({
            title: "Error",
            message: response.data.message,
            type: "error",
            location: "top-" + !directionConfig.endSide,
            dismissable: true,
          });
        }
        button.button("reset");
      },
      error: function () {
        butterup.toast({
          title: "Error",
          message: msg_formError,
          type: "error",
          location: "top-" + !directionConfig.endSide,
          dismissable: true,
        });
      },
    });
  });
  // Cart Counter
  function updateCartCount() {
    $.ajax({
      url: URL_AJAX,
      type: "POST",
      data: { action: "get_cart_count" },
      success: function (response) {
        if (response.success) {
          $(".cart-toggle strong").text(response.data.count); // به‌روزرسانی نمایش تعداد
        }
      },
      error: function () {
        butterup.toast({
          title: "خطا",
          message: "خطا در دریافت تعداد محصولات سبد خرید",
          type: "error",
          location: "top-" + endSide,
          dismissable: true,
        });
      },
    });
  }
  updateCartCount();
  $("body").on("click", "body.woocommerce-cart .quantity-field > button", function () {
    //var $form = $(this).closest("form");
    // به‌روزرسانی خودکار سبد خرید
    $("[name='update_cart']").prop("disabled", false); // فعال کردن دکمه
    $("[name='update_cart']").trigger("click");
  });

  // Filter
  // حذف همه فیلترها (به‌جز موارد مجاز)
  $(".page-header-filter-reset").on("click", function () {
    const url = window.location.href;
    const urlParts = url.split("?");
    if (urlParts.length < 2) return;

    const baseUrl = urlParts[0];
    const queryParams = urlParts[1].split("&");
    const allowedParams = ["post_type", "product_category"];
    const filteredParams = queryParams.filter(param => {
      const key = param.split("=")[0];
      return allowedParams.includes(key);
    });

    const newUrl = filteredParams.length
      ? `${baseUrl}?${filteredParams.join("&")}`
      : baseUrl;

    window.location.href = newUrl;
  });

  // حذف فیلتر خاص با کلیک روی تگ
  $(".filter-tag").on("click", function () {
    const key = $(this).data("key");
    const value = $(this).data("value");
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has(key)) {
      const values = urlParams.get(key).split(",");
      const newValues = values.filter(item => item !== value);

      if (newValues.length > 0) {
        urlParams.set(key, newValues.join(","));
      } else {
        urlParams.delete(key);
      }
    }

    // حذف page/x از مسیر
    const cleanedPath = window.location.pathname.replace(/\/page\/\d+\//, '/');
    const newUrl = `${cleanedPath}?${urlParams.toString()}`;
    window.location.href = newUrl;
  });

});

// "Show More" pagination for category/tag/shop product grids.
$(function () {
  const $wrap = $('#product-grid-wrap');
  if (!$wrap.length) return;

  const $grid = $('#product-grid');
  const $btn = $wrap.find('.show-more-btn');
  const $backToTop = $wrap.find('.show-more-back-to-top');
  const $countShown = $wrap.find('.show-more-count-shown');
  const $countTotal = $wrap.find('.show-more-count-total');
  const $progressFill = $wrap.find('.show-more-progress-bar-fill');

  const archiveType = $wrap.data('archive-type') || '';
  const archiveTerm = $wrap.data('archive-term') || '';
  // jQuery auto-parses this data attribute's JSON into an array of {taxonomy, slug} objects.
  const archivePaFilters = $wrap.data('archive-pa-filters') || [];
  // 40 here is only a defensive fallback for a missing/zero data-per-page
  // attribute — the real value always comes from that attribute, itself
  // rendered from Shop::PRODUCTS_PER_PAGE (see product-grid.php), so this
  // literal has to be kept in sync by hand if that constant ever changes
  // again (can't share a single source across PHP/JS without wiring up a
  // localized script var, which felt like overkill for a fallback that
  // structurally never fires in normal use).
  const perPage = parseInt($wrap.data('per-page'), 10) || 40;
  let currentPage = parseInt($wrap.data('current-page'), 10) || 1;
  let maxPages = parseInt($wrap.data('max-pages'), 10) || 1;
  let foundPosts = parseInt($wrap.data('found-posts'), 10) || 0;

  function updateProgress(shownCount) {
    $countShown.text(shownCount.toLocaleString());
    $countTotal.text(foundPosts.toLocaleString());
    const pct = foundPosts > 0 ? Math.min(100, (shownCount / foundPosts) * 100) : 0;
    $progressFill.css('width', pct + '%');
  }

  // The site's [data-animate] fade-in only becomes visible once GSAP's
  // ScrollTrigger fires its "animated" class — and that's wired up once, for
  // elements present at page load (see main.js). Cards injected later via
  // AJAX would otherwise stay permanently invisible (opacity:0 from
  // fw.min.css), since nothing ever registers a ScrollTrigger for them. Mark
  // them pre-animated before insertion so they just render normally.
  function markAnimated($html) {
    $html.find('[data-animate]').addBack('[data-animate]').addClass('animated');
    return $html;
  }

  function pushPageUrl(page) {
    let path = window.location.pathname.replace(/\/page\/\d+\/?$/, '/');
    if (!path.endsWith('/')) path += '/';
    if (page > 1) path += 'page/' + page + '/';
    history.pushState({ qaliPage: page }, '', path + window.location.search);
  }

  function fetchPage(page) {
    const params = new URLSearchParams(window.location.search);
    params.delete('paged');
    params.set('action', 'qali_load_more_products');
    params.set('paged', page);
    params.set('archive_type', archiveType);
    params.set('archive_term', archiveTerm);
    params.set('archive_pa_filters', JSON.stringify(archivePaFilters));
    params.set('per_page', perPage);
    return $.get(URL_AJAX, params.toString());
  }

  function loadNextPage() {
    const targetPage = currentPage + 1;
    $btn.prop('disabled', true).addClass('is-loading');

    fetchPage(targetPage).done(function (response) {
      if (response && response.success) {
        $grid.append(markAnimated($(response.data.html)));
        currentPage = response.data.current_page;
        maxPages = response.data.max_num_pages;
        foundPosts = response.data.found_posts;
        updateProgress(Math.min(foundPosts, currentPage * perPage));
        pushPageUrl(currentPage);
        if (currentPage >= maxPages) {
          $btn.attr('hidden', true);
        }
      }
    }).fail(function () {
      if (typeof butterup !== 'undefined') {
        butterup.toast({
          title: 'Error',
          message: typeof msg_formError !== 'undefined' ? msg_formError : 'An error occurred, please try again later.',
          type: 'error',
          dismissable: true,
        });
      }
    }).always(function () {
      $btn.prop('disabled', false).removeClass('is-loading');
    });
  }

  $btn.on('click', function (e) {
    e.preventDefault();
    loadNextPage();
  });

  $backToTop.on('click', function (e) {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // Deep link (e.g. shared/crawled ?paged=3 or /page/3/ link): the server only
  // rendered that page's own products (so real WP pagination / Yoast rel=next/
  // prev for this URL stay untouched); fetch the earlier pages via the same
  // endpoint and prepend them so the visible grid matches what a visitor would
  // see after clicking "Show More" that many times from page 1.
  if (currentPage > 1) {
    const pageNumbers = [];
    for (let p = 1; p < currentPage; p++) pageNumbers.push(p);

    Promise.all(pageNumbers.map(function (p) {
      return fetchPage(p).then(function (response) {
        return { page: p, response: response };
      });
    })).then(function (results) {
      results.sort(function (a, b) { return a.page - b.page; });
      let html = '';
      results.forEach(function (r) {
        if (r.response && r.response.success) {
          html += r.response.data.html;
          foundPosts = r.response.data.found_posts;
          maxPages = r.response.data.max_num_pages;
        }
      });
      $grid.prepend(markAnimated($(html)));
      updateProgress(Math.min(foundPosts, currentPage * perPage));
      if (currentPage >= maxPages) {
        $btn.attr('hidden', true);
      }
    });
  }
});

// "Suggested next filter" chips (pa_* archive pages, single-attribute or chained) — "Skip" swaps
// in the next remaining dimension's row via AJAX, without changing the page URL. Delegated so it
// keeps working after the row is replaced by a later "Skip" click.
$(function () {
  $(document).on('click', '.page-header-next-filter-skip', function (e) {
    e.preventDefault();
    const $btn = $(this);
    const $wrap = $btn.closest('#next-filter-suggestion');
    const base = $btn.data('base');
    const skipped = ($wrap.data('skipped') || '').toString().split(',').filter(Boolean);
    skipped.push(base);
    // jQuery auto-parses this data attribute's JSON — the page's own active filter chain, since
    // this AJAX request has no page context of its own (see Shop::ajax_next_filter_suggestion()).
    const active = $wrap.data('active') || [];

    $btn.prop('disabled', true);
    $.get(URL_AJAX, { action: 'qali_next_filter_suggestion', skip: skipped.join(','), active: JSON.stringify(active) })
      .done(function (response) {
        if (response && response.success && response.data.html) {
          $wrap.replaceWith(response.data.html);
        } else {
          $wrap.remove();
        }
      })
      .fail(function () {
        $btn.prop('disabled', false);
      });
  });
});
