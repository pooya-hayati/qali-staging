window.directionConfig = (function () {
  var isRtl = document.body.classList.contains("rtl");

  return {
    rtl: isRtl,
    prevArrow: isRtl ? "right" : "left",
    nextArrow: isRtl ? "left" : "right",
    endSide: isRtl ? "left" : "right"
  };
})();

document.querySelectorAll('input[placeholder], textarea[placeholder]').forEach(function (el) {
  el.placeholder = '';
});

// Translation
if (document.documentElement.lang.toLowerCase() === "en-us") {
  var msg_formError = "An error occurred, please try again later.";
  var msg_formSubmit = "Submitted Successfully.";
  var msg_wait = "Please wait…";
} else if (document.documentElement.lang.toLowerCase() === "fa-ir") {
  var msg_formError = "خطایی رخ داد. لطفا بعدا امتحان نمایید.";
  var msg_formSubmit = "با موفقیت ثبت شد.";
  var msg_wait = "لطفا صبر کنید…";
}
// BS Transition
!function (n) { "use strict"; n.fn.emulateTransitionEnd = function (t) { var i = !1, r = this; n(this).one("bsTransitionEnd", function () { i = !0 }); return setTimeout(function () { i || n(r).trigger(n.support.transition.end) }, t), this }, n(function () { n.support.transition = function () { var n = document.createElement("bootstrap"), t = { WebkitTransition: "webkitTransitionEnd", MozTransition: "transitionend", OTransition: "oTransitionEnd otransitionend", transition: "transitionend" }; for (var i in t) if (void 0 !== n.style[i]) return { end: t[i] }; return !1 }(), n.support.transition && (n.event.special.bsTransitionEnd = { bindType: n.support.transition.end, delegateType: n.support.transition.end, handle: function (t) { if (n(t.target).is(this)) return t.handleObj.handler.apply(this, arguments) } }) }) }(jQuery);
// BS Button
!function (t) { "use strict"; var e = function (n, s) { this.$element = t(n), this.options = t.extend({}, e.DEFAULTS, s), this.isLoading = !1 }; function n(n) { return this.each(function () { var s = t(this), i = s.data("bs.button"), o = "object" == typeof n && n; i || s.data("bs.button", i = new e(this, o)), "toggle" == n ? i.toggle() : n && i.setState(n) }) } e.VERSION = "3.4.1", e.DEFAULTS = { loadingText: "loading..." }, e.prototype.setState = function (e) { var n = "disabled", s = this.$element, i = s.is("input") ? "val" : "html", o = s.data(); e += "Text", null == o.resetText && s.data("resetText", s[i]()), setTimeout(t.proxy(function () { s[i](null == o[e] ? this.options[e] : o[e]), "loadingText" == e ? (this.isLoading = !0, s.addClass(n).attr(n, n).prop(n, !0)) : this.isLoading && (this.isLoading = !1, s.removeClass(n).removeAttr(n).prop(n, !1)) }, this), 0) }, e.prototype.toggle = function () { var t = !0, e = this.$element.closest('[data-toggle="buttons"]'); if (e.length) { var n = this.$element.find("input"); "radio" == n.prop("type") ? (n.prop("checked") && (t = !1), e.find(".active").removeClass("active"), this.$element.addClass("active")) : "checkbox" == n.prop("type") && (n.prop("checked") !== this.$element.hasClass("active") && (t = !1), this.$element.toggleClass("active")), n.prop("checked", this.$element.hasClass("active")), t && n.trigger("change") } else this.$element.attr("aria-pressed", !this.$element.hasClass("active")), this.$element.toggleClass("active") }; var s = t.fn.button; t.fn.button = n, t.fn.button.Constructor = e, t.fn.button.noConflict = function () { return t.fn.button = s, this }, t(document).on("click.bs.button.data-api", '[data-toggle^="button"]', function (e) { var s = t(e.target).closest(".btn"); n.call(s, "toggle"), t(e.target).is('input[type="radio"], input[type="checkbox"]') || (e.preventDefault(), s.is("input,button") ? s.trigger("focus") : s.find("input:visible,button:visible").first().trigger("focus")) }).on("focus.bs.button.data-api blur.bs.button.data-api", '[data-toggle^="button"]', function (e) { t(e.target).closest(".btn").toggleClass("focus", /^focus(in)?$/.test(e.type)) }) }(jQuery);
// BS Modal
!function (t) { "use strict"; var e = function (e, i) { this.options = i, this.$body = t(document.body), this.$element = t(e), this.$dialog = this.$element.find(".modal-dialog"), this.$backdrop = null, this.isShown = null, this.originalBodyPad = null, this.scrollbarWidth = 0, this.ignoreBackdropClick = !1, this.fixedContent = ".navbar-fixed-top, .navbar-fixed-bottom", this.options.remote && this.$element.find(".modal-content").load(this.options.remote, t.proxy(function () { this.$element.trigger("loaded.bs.modal") }, this)) }; function i(i, o) { return this.each(function () { var s = t(this), n = s.data("bs.modal"), a = t.extend({}, e.DEFAULTS, s.data(), "object" == typeof i && i); n || s.data("bs.modal", n = new e(this, a)), "string" == typeof i ? n[i](o) : a.show && n.show(o) }) } e.VERSION = "3.4.1", e.TRANSITION_DURATION = 300, e.BACKDROP_TRANSITION_DURATION = 150, e.DEFAULTS = { backdrop: !0, keyboard: !0, show: !0 }, e.prototype.toggle = function (t) { return this.isShown ? this.hide() : this.show(t) }, e.prototype.show = function (i) { var o = this, s = t.Event("show.bs.modal", { relatedTarget: i }); this.$element.trigger(s), this.isShown || s.isDefaultPrevented() || (this.isShown = !0, this.checkScrollbar(), this.setScrollbar(), this.$body.addClass("modal-open"), this.escape(), this.resize(), this.$element.on("click.dismiss.bs.modal", '[data-dismiss="modal"]', t.proxy(this.hide, this)), this.$dialog.on("mousedown.dismiss.bs.modal", function () { o.$element.one("mouseup.dismiss.bs.modal", function (e) { t(e.target).is(o.$element) && (o.ignoreBackdropClick = !0) }) }), this.backdrop(function () { var s = t.support.transition && o.$element.hasClass("fade"); o.$element.parent().length || o.$element.appendTo(o.$body), o.$element.show().scrollTop(0), o.adjustDialog(), s && o.$element[0].offsetWidth, o.$element.addClass("in"), o.enforceFocus(); var n = t.Event("shown.bs.modal", { relatedTarget: i }); s ? o.$dialog.one("bsTransitionEnd", function () { o.$element.trigger("focus").trigger(n) }).emulateTransitionEnd(e.TRANSITION_DURATION) : o.$element.trigger("focus").trigger(n) })) }, e.prototype.hide = function (i) { i && i.preventDefault(), i = t.Event("hide.bs.modal"), this.$element.trigger(i), this.isShown && !i.isDefaultPrevented() && (this.isShown = !1, this.escape(), this.resize(), t(document).off("focusin.bs.modal"), this.$element.removeClass("in").off("click.dismiss.bs.modal").off("mouseup.dismiss.bs.modal"), this.$dialog.off("mousedown.dismiss.bs.modal"), t.support.transition && this.$element.hasClass("fade") ? this.$element.one("bsTransitionEnd", t.proxy(this.hideModal, this)).emulateTransitionEnd(e.TRANSITION_DURATION) : this.hideModal()) }, e.prototype.enforceFocus = function () { t(document).off("focusin.bs.modal").on("focusin.bs.modal", t.proxy(function (t) { document === t.target || this.$element[0] === t.target || this.$element.has(t.target).length || this.$element.trigger("focus") }, this)) }, e.prototype.escape = function () { this.isShown && this.options.keyboard ? this.$element.on("keydown.dismiss.bs.modal", t.proxy(function (t) { 27 == t.which && this.hide() }, this)) : this.isShown || this.$element.off("keydown.dismiss.bs.modal") }, e.prototype.resize = function () { this.isShown ? t(window).on("resize.bs.modal", t.proxy(this.handleUpdate, this)) : t(window).off("resize.bs.modal") }, e.prototype.hideModal = function () { var t = this; this.$element.hide(), this.backdrop(function () { t.$body.removeClass("modal-open"), t.resetAdjustments(), t.resetScrollbar(), t.$element.trigger("hidden.bs.modal") }) }, e.prototype.removeBackdrop = function () { this.$backdrop && this.$backdrop.remove(), this.$backdrop = null }, e.prototype.backdrop = function (i) { var o = this, s = this.$element.hasClass("fade") ? "fade" : ""; if (this.isShown && this.options.backdrop) { var n = t.support.transition && s; if (this.$backdrop = t(document.createElement("div")).addClass("modal-backdrop " + s).appendTo(this.$body), this.$element.on("click.dismiss.bs.modal", t.proxy(function (t) { this.ignoreBackdropClick ? this.ignoreBackdropClick = !1 : t.target === t.currentTarget && ("static" == this.options.backdrop ? this.$element[0].focus() : this.hide()) }, this)), n && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in"), !i) return; n ? this.$backdrop.one("bsTransitionEnd", i).emulateTransitionEnd(e.BACKDROP_TRANSITION_DURATION) : i() } else if (!this.isShown && this.$backdrop) { this.$backdrop.removeClass("in"); var a = function () { o.removeBackdrop(), i && i() }; t.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one("bsTransitionEnd", a).emulateTransitionEnd(e.BACKDROP_TRANSITION_DURATION) : a() } else i && i() }, e.prototype.handleUpdate = function () { this.adjustDialog() }, e.prototype.adjustDialog = function () { var t = this.$element[0].scrollHeight > document.documentElement.clientHeight; this.$element.css({ paddingLeft: !this.bodyIsOverflowing && t ? this.scrollbarWidth : "", paddingRight: this.bodyIsOverflowing && !t ? this.scrollbarWidth : "" }) }, e.prototype.resetAdjustments = function () { this.$element.css({ paddingLeft: "", paddingRight: "" }) }, e.prototype.checkScrollbar = function () { var t = window.innerWidth; if (!t) { var e = document.documentElement.getBoundingClientRect(); t = e.right - Math.abs(e.left) } this.bodyIsOverflowing = document.body.clientWidth < t, this.scrollbarWidth = this.measureScrollbar() }, e.prototype.setScrollbar = function () { var e = parseInt(this.$body.css("padding-right") || 0, 10); this.originalBodyPad = document.body.style.paddingRight || ""; var i = this.scrollbarWidth; this.bodyIsOverflowing && (this.$body.css("padding-right", e + i), t(this.fixedContent).each(function (e, o) { var s = o.style.paddingRight, n = t(o).css("padding-right"); t(o).data("padding-right", s).css("padding-right", parseFloat(n) + i + "px") })) }, e.prototype.resetScrollbar = function () { this.$body.css("padding-right", this.originalBodyPad), t(this.fixedContent).each(function (e, i) { var o = t(i).data("padding-right"); t(i).removeData("padding-right"), i.style.paddingRight = o || "" }) }, e.prototype.measureScrollbar = function () { var t = document.createElement("div"); t.className = "modal-scrollbar-measure", this.$body.append(t); var e = t.offsetWidth - t.clientWidth; return this.$body[0].removeChild(t), e }; var o = t.fn.modal; t.fn.modal = i, t.fn.modal.Constructor = e, t.fn.modal.noConflict = function () { return t.fn.modal = o, this }, t(document).on("click.bs.modal.data-api", '[data-toggle="modal"]', function (e) { var o = t(this), s = o.attr("href"), n = o.attr("data-target") || s && s.replace(/.*(?=#[^\s]+$)/, ""), a = t(document).find(n), d = a.data("bs.modal") ? "toggle" : t.extend({ remote: !/#/.test(s) && s }, a.data(), o.data()); o.is("a") && e.preventDefault(), a.one("show.bs.modal", function (t) { t.isDefaultPrevented() || a.one("hidden.bs.modal", function () { o.is(":visible") && o.trigger("focus") }) }), i.call(a, d, this) }) }(jQuery);
// BS Tab
!function (t) { "use strict"; var a = function (a) { this.element = t(a) }; function e(e) { return this.each(function () { var n = t(this), i = n.data("bs.tab"); i || n.data("bs.tab", i = new a(this)), "string" == typeof e && i[e]() }) } a.VERSION = "3.4.1", a.TRANSITION_DURATION = 150, a.prototype.show = function () { var a = this.element, e = a.closest("ul:not(.dropdown-menu)"), n = a.data("target"); if (n || (n = (n = a.attr("href")) && n.replace(/.*(?=#[^\s]*$)/, "")), !a.parent("li").hasClass("active")) { var i = e.find(".active:last a"), r = t.Event("hide.bs.tab", { relatedTarget: a[0] }), s = t.Event("show.bs.tab", { relatedTarget: i[0] }); if (i.trigger(r), a.trigger(s), !s.isDefaultPrevented() && !r.isDefaultPrevented()) { var d = t(document).find(n); this.activate(a.closest("li"), e), this.activate(d, d.parent(), function () { i.trigger({ type: "hidden.bs.tab", relatedTarget: a[0] }), a.trigger({ type: "shown.bs.tab", relatedTarget: i[0] }) }) } } }, a.prototype.activate = function (e, n, i) { var r = n.find("> .active"), s = i && t.support.transition && (r.length && r.hasClass("fade") || !!n.find("> .fade").length); function d() { r.removeClass("active").find("> .dropdown-menu > .active").removeClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !1), e.addClass("active").find('[data-toggle="tab"]').attr("aria-expanded", !0), s ? (e[0].offsetWidth, e.addClass("in")) : e.removeClass("fade"), e.parent(".dropdown-menu").length && e.closest("li.dropdown").addClass("active").end().find('[data-toggle="tab"]').attr("aria-expanded", !0), i && i() } r.length && s ? r.one("bsTransitionEnd", d).emulateTransitionEnd(a.TRANSITION_DURATION) : d(), r.removeClass("in") }; var n = t.fn.tab; t.fn.tab = e, t.fn.tab.Constructor = a, t.fn.tab.noConflict = function () { return t.fn.tab = n, this }; var i = function (a) { a.preventDefault(), e.call(t(this), "show") }; t(document).on("click.bs.tab.data-api", '[data-toggle="tab"]', i).on("click.bs.tab.data-api", '[data-toggle="pill"]', i) }(jQuery);
// BS Collapse
!function (t) { "use strict"; var e = function (a, s) { this.$element = t(a), this.options = t.extend({}, e.DEFAULTS, s), this.$trigger = t('[data-toggle="collapse"][href="#' + a.id + '"],[data-toggle="collapse"][data-target="#' + a.id + '"]'), this.transitioning = null, this.options.parent ? this.$parent = this.getParent() : this.addAriaAndCollapsedClass(this.$element, this.$trigger), this.options.toggle && this.toggle() }; function a(e) { var a, s = e.attr("data-target") || (a = e.attr("href")) && a.replace(/.*(?=#[^\s]+$)/, ""); return t(document).find(s) } function s(a) { return this.each(function () { var s = t(this), i = s.data("bs.collapse"), n = t.extend({}, e.DEFAULTS, s.data(), "object" == typeof a && a); !i && n.toggle && /show|hide/.test(a) && (n.toggle = !1), i || s.data("bs.collapse", i = new e(this, n)), "string" == typeof a && i[a]() }) } e.VERSION = "3.4.1", e.TRANSITION_DURATION = 350, e.DEFAULTS = { toggle: !0 }, e.prototype.dimension = function () { return this.$element.hasClass("width") ? "width" : "height" }, e.prototype.show = function () { if (!this.transitioning && !this.$element.hasClass("in")) { var a, i = this.$parent && this.$parent.children(".panel").children(".in, .collapsing"); if (!(i && i.length && (a = i.data("bs.collapse")) && a.transitioning)) { var n = t.Event("show.bs.collapse"); if (this.$element.trigger(n), !n.isDefaultPrevented()) { i && i.length && (s.call(i, "hide"), a || i.data("bs.collapse", null)); var l = this.dimension(); this.$element.removeClass("collapse").addClass("collapsing")[l](0).attr("aria-expanded", !0), this.$trigger.removeClass("collapsed").attr("aria-expanded", !0), this.transitioning = 1; var o = function () { this.$element.removeClass("collapsing").addClass("collapse in")[l](""), this.transitioning = 0, this.$element.trigger("shown.bs.collapse") }; if (!t.support.transition) return o.call(this); var r = t.camelCase(["scroll", l].join("-")); this.$element.one("bsTransitionEnd", t.proxy(o, this)).emulateTransitionEnd(e.TRANSITION_DURATION)[l](this.$element[0][r]) } } } }, e.prototype.hide = function () { if (!this.transitioning && this.$element.hasClass("in")) { var a = t.Event("hide.bs.collapse"); if (this.$element.trigger(a), !a.isDefaultPrevented()) { var s = this.dimension(); this.$element[s](this.$element[s]())[0].offsetHeight, this.$element.addClass("collapsing").removeClass("collapse in").attr("aria-expanded", !1), this.$trigger.addClass("collapsed").attr("aria-expanded", !1), this.transitioning = 1; var i = function () { this.transitioning = 0, this.$element.removeClass("collapsing").addClass("collapse").trigger("hidden.bs.collapse") }; if (!t.support.transition) return i.call(this); this.$element[s](0).one("bsTransitionEnd", t.proxy(i, this)).emulateTransitionEnd(e.TRANSITION_DURATION) } } }, e.prototype.toggle = function () { this[this.$element.hasClass("in") ? "hide" : "show"]() }, e.prototype.getParent = function () { return t(document).find(this.options.parent).find('[data-toggle="collapse"][data-parent="' + this.options.parent + '"]').each(t.proxy(function (e, s) { var i = t(s); this.addAriaAndCollapsedClass(a(i), i) }, this)).end() }, e.prototype.addAriaAndCollapsedClass = function (t, e) { var a = t.hasClass("in"); t.attr("aria-expanded", a), e.toggleClass("collapsed", !a).attr("aria-expanded", a) }; var i = t.fn.collapse; t.fn.collapse = s, t.fn.collapse.Constructor = e, t.fn.collapse.noConflict = function () { return t.fn.collapse = i, this }, t(document).on("click.bs.collapse.data-api", '[data-toggle="collapse"]', function (e) { var i = t(this); i.attr("data-target") || e.preventDefault(); var n = a(i), l = n.data("bs.collapse") ? "toggle" : i.data(); s.call(n, l) }) }(jQuery);
// Lettring
!function (t) { function e(e, n, i, r) { var a = e.text(), c = a.split(n), s = ""; c.length && (t(c).each(function (t, e) { s += '<span class="' + i + (t + 1) + '" aria-hidden="true">' + e + "</span>" + r }), e.attr("aria-label", a).empty().append(s)) } var n = { init: function () { return this.each(function () { e(t(this), "", "char", "") }) }, words: function () { return this.each(function () { e(t(this), " ", "word", " ") }) }, lines: function () { return this.each(function () { var n = "eefec303079ad17405c889e092e105b0"; e(t(this).children("br").replaceWith(n).end(), n, "line", "") }) } }; t.fn.lettering = function (e) { return e && n[e] ? n[e].apply(this, [].slice.call(arguments, 1)) : "letters" !== e && e ? (t.error("Method " + e + " does not exist on jQuery.lettering"), this) : n.init.apply(this, [].slice.call(arguments, 0)) } }(jQuery);
// Main
$(document).ready(function () {
  /**/
  $(window).scroll(function () {
    if ($(window).scrollTop() > 90) {
      $("#header").addClass("sticky");
    } else {
      $("#header").removeClass("sticky");
    }
  });
  /**/
  $(document).on("click", "body:not(.nav-opened) .nav-toggle", function () {
    $("#sidebar, .nav-toggle").addClass("active");
    $("body").addClass("nav-opened body-overflow");
    gsap.fromTo(
      "#sidebar .row > div",
      { opacity: 0, y: -20 },
      {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: "power2.inOut",
        stagger: 0.1,
      }
    );

    gsap.fromTo(
      ".sblock-nav > li",
      { opacity: 0, y: -20 },
      {
        opacity: 1,
        y: 0,
        duration: 0.4,
        ease: "power2.inOut",
        delay: 0.6,
        stagger: 0.03,
      }
    );
    return false;
  });
  $(document).on("click", "body.nav-opened .nav-toggle", function () {
    $("#sidebar, .nav-toggle").removeClass("active");
    $("body").removeClass("nav-opened body-overflow");
    return false;
  });
  /**/
  $(document).on("click", "body:not(.searchbar-opened) .search-toggle", function () {
    $(".searchbar").addClass("active");
    // $(".searchbar-form-input").first().focus();
    $(".searchbar").before("<div class='body-overlay'></div>");
    $("body").addClass("searchbar-opened body-overflow");
    return false;
  }
  );
  $(document).on("click", "body.searchbar-opened .body-overlay", function () {
    $(this).remove();
    $(".searchbar").removeClass("active");
    // $(".searchbar-form-input").val("");
    $("body").removeClass("searchbar-opened body-overflow");
    return false;
  });
  /**/
  $(".accordion-list > .accordion-card")
    .children(".accordion-card-body")
    .slideUp(0);
  $(".accordion-list > .accordion-card:first")
    .children(".accordion-card-body")
    .slideDown(0);
  $(".accordion-list > .accordion-card:first").addClass("active");
  $(document).on(
    "click",
    ".accordion-list .accordion-card-header",
    function () {
      if ($(this).parent(".accordion-card").hasClass("active")) {
        $(this).parent(".accordion-card").removeClass("active");
        $(this)
          .parent(".accordion-card")
          .children(".accordion-card-body")
          .slideUp(200);
      } else {
        $(this)
          .parent(".accordion-card")
          .siblings(".accordion-card")
          .children(".accordion-card-body")
          .slideUp(200);
        $(this)
          .parent(".accordion-card")
          .siblings(".accordion-card")
          .removeClass("active");
        $(this)
          .parent(".accordion-card")
          .children(".accordion-card-body")
          .slideDown(200);
        $(this).parent(".accordion-card").addClass("active");
      }
      return false;
    }
  );
  /**/
  if (document.body.classList.contains("page-template-page-about")) {
    var aboutGallery = new Swiper(".about-gallery-carousel", {
      direction: 'horizontal',
      slidesPerView: "auto",
      spaceBetween: 20,
      loop: true,
      speed: 5000,
      autoplay: {
        delay: 0,
        disableOnInteraction: false,
      },
      freeMode: true,
      freeModeMomentum: false,
      allowTouchMove: false,
    });

  }
  /**/
  function CustomSelect(selector) {
    $(selector).each(function () {
      const $customSelect = $(this);
      const $selectSelected = $customSelect.find(".select-selected");
      const $selectItems = $customSelect.find(".select-items");
      const $radioButtons = $selectItems.find('input[type="radio"]');

      // مقدار اولیه در بارگذاری
      const selectedOption = $radioButtons.filter(":checked");
      if (selectedOption.length > 0) {
        const $label = selectedOption.closest("label");
        const labelHtml = $label.find("div").html() || '';
        const labelText = $label.find("span").first().text().toLowerCase().replace(/\s+/g, '') || '';
        $selectSelected.html(labelHtml).attr("data-title", labelText);
      }

      // باز و بسته شدن آیتم‌ها
      $selectSelected.on("click", function () {
        $(".select-items").not($selectItems).slideUp(200);
        $selectItems.slideToggle(200);
        $(this).toggleClass("active");
      });

      // تغییر انتخاب
      $radioButtons.on("change", function () {
        const $label = $(this).closest("label");
        const labelHtml = $label.find("div").html() || '';
        const labelText = $label.find("span").first().text().toLowerCase().replace(/\s+/g, '') || '';
        $selectSelected.html(labelHtml).attr("data-title", labelText);
        $selectItems.slideUp(200);
        $customSelect.closest("form").submit();
      });

      // بستن در کلیک خارج
      $(window).on("click", function (e) {
        if (!$(e.target).closest($customSelect).length) {
          $selectItems.slideUp(200);
          $selectSelected.removeClass("active");
        }
      });
    });

    /* Masonry Grid */
    let $grid = $(".masonry-grid");

    $grid.imagesLoaded(function () {
      $grid.masonry({
        itemSelector: ".masonry-grid-item",
        columnWidth: ".masonry-grid-sizer",
        percentPosition: true,
        isOriginLeft: !directionConfig.rtl
      });
    });

    /* Contact */
    $("#contact-form").on("submit", function (e) {
      e.preventDefault();
      $.ajax({
        type: "POST",
        dataType: "json",
        url: URL_AJAX,
        data: {
          action: "contact",
          data: $(this).serialize(),
        },
        beforeSend: function () {
          $(this).find("button[type=submit]").button("loading");
        },
        success: function (response) {
          if (response.success) {
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
        },
        error: function (xhr, textStatus, errorThrown) {
          if (errorThrown === "Bad Request") {
            butterup.toast({
              title: "Error",
              message: "Error!",
              type: "error",
              location: "top-" + !directionConfig.endSide,
              dismissable: true,
            });
          }
        },
        complete: function () {
          $(this).find("button[type=submit]").button("reset");
          $(this).trigger("reset");
        },
      });
    });
  }
  CustomSelect(".icon-select");
  /**/
  const header = $("#header");
  const headerHeight = header.outerHeight();
  let lastScrollTop = 0;
  let deltaScroll = 50;                    // مقدار حداقل جابجایی

  $(window).on("scroll", function () {
    if ($(this).scrollTop() > headerHeight) {
      // اگر اسکرول بیشتر از ارتفاع هدر باشد
      header.addClass("sticky");
    } else {
      // اگر اسکرول کمتر از ارتفاع هدر باشد
      header.removeClass("sticky");
    }

    let currentScrollTop = $(this).scrollTop();

    // فقط اگر مقدار جابجایی بیشتر از delta باشد
    if (Math.abs(currentScrollTop - lastScrollTop) > deltaScroll) {
      if (currentScrollTop > lastScrollTop) {
        // اسکرول به پایین
        header.css("transform", "translateY(-100%)");
      } else {
        // اسکرول به بالا
        header.css("transform", "translateY(0)");
      }

      lastScrollTop = currentScrollTop;
    }
  });
  // Animations
  $(".section-title:not(.no-animate)").lettering("words").children("span").lettering();
  $(".section-subtitle:not(.no-animate)").lettering("words");
  $(".section-desc:not(.no-animate)").lettering("words");
  //
  gsap.registerPlugin(ScrollTrigger);

  document.querySelectorAll(".section").forEach((section) => {
    if (!section) return;
    ScrollTrigger.create({
      trigger: section,
      start: "top 70%",               // معادل threshold: 0.3
      toggleActions: "play none none none",   // فقط یکبار پخش
      once: true,
      onEnter: () => {
        section.classList.add("active");

        gsap.fromTo(
          section.querySelectorAll(".section-title > span > span"),
          { opacity: 0 },
          {
            opacity: 1,
            duration: 0.4,
            stagger: 0.05,
            ease: "power2.inOut",
          }
        );

        gsap.fromTo(
          section.querySelectorAll(".section-subtitle > span"),
          { opacity: 0 },
          {
            opacity: 1,
            duration: 0.6,
            stagger: 0.1,
            ease: "power2.inOut",
          }
        );

        gsap.fromTo(
          section.querySelectorAll(".section-desc > span"),
          { opacity: 0 },
          {
            opacity: 1,
            duration: 0.8,
            stagger: 0.1,
            ease: "power2.inOut",
          }
        );
      },
      /*onLeaveBack: () => {
        section.classList.remove("active");
      },*/
    });
  });


  $(".page-header-title").lettering("words").children("span").lettering();
  $(".page-header-subtitle").lettering("words");
  gsap.registerPlugin(ScrollTrigger);

  // #page-header animation with GSAP
  ScrollTrigger.create({
    trigger: "#page-header",
    start: "top 70%",
    once: true,
    onEnter: () => {
      const el = document.querySelector("#page-header");
      if (!el) return;
      el.classList.add("active");
      gsap.fromTo(
        el.querySelectorAll(".page-header-title > span > span"),
        { opacity: 0 },
        {
          opacity: 1,
          duration: 0.4,
          stagger: 0.05,
          ease: "power2.inOut",
        }
      );

      gsap.fromTo(
        el.querySelectorAll(".page-header-subtitle > span"),
        { opacity: 0 },
        {
          opacity: 1,
          duration: 0.6,
          stagger: 0.1,
          ease: "power2.inOut",
        }
      );
    },
    /*onLeaveBack: () => {
      const el = document.querySelector("#page-header");
      el.classList.remove("active");
    },*/
  });

  // [data-animate] toggle class
  document.querySelectorAll("[data-animate]").forEach((el) => {
    ScrollTrigger.create({
      trigger: el,
      start: "top 70%",
      once: true,
      onEnter: () => {
        el.classList.add("animated");
      },
      /*onLeaveBack: () => {
        el.classList.remove("animated");
      },*/
    });
  });

});

// Range Slider
(function ($) {
  $.fn.customRangeSlider = function (options) {
    return this.each(function () {
      const rangeSlider = $(this);
      const minThumb = rangeSlider.find(".min-thumb");
      const maxThumb = rangeSlider.find(".max-thumb");
      const sliderRange = rangeSlider.find(".range-slider-bar");
      const minInput = rangeSlider.find(".range-slider-min");
      const maxInput = rangeSlider.find(".range-slider-max");
      const minValueLabel = rangeSlider.find(".range-slider-label-min");
      const maxValueLabel = rangeSlider.find(".range-slider-label-max");
      const track = rangeSlider.find(".range-slider-track");

      // بررسی راست‌به‌چپ بودن صفحه
      const isRTL = $("body").hasClass("rtl");

      track.width(track.parent().width());

      let trackWidth = track.width();
      const stepValue = parseInt(rangeSlider.data("step")) || 1;
      const maxRangeValue = parseInt(rangeSlider.data("max")) || 500000000;
      const minRangeValue = parseInt(rangeSlider.data("min")) || 0;
      let minVal = parseInt(minInput.val()) || minRangeValue;
      let maxVal = parseInt(maxInput.val()) || maxRangeValue;

      const settings = $.extend(
        {
          max: maxRangeValue,
          min: minRangeValue,
          step: stepValue,
        },
        options
      );

      function updateThumbs() {
        const minPercent =
          ((minVal - settings.min) / (settings.max - settings.min)) * 100;
        const maxPercent =
          ((maxVal - settings.min) / (settings.max - settings.min)) * 100;

        if (isRTL) {
          minThumb.css("right", `${minPercent}%`);
          maxThumb.css("right", `${maxPercent}%`);
          sliderRange.css({
            right: `${minPercent}%`,
            width: `${maxPercent - minPercent}%`,
          });
        } else {
          minThumb.css("left", `${minPercent}%`);
          maxThumb.css("left", `${maxPercent}%`);
          sliderRange.css({
            left: `${minPercent}%`,
            width: `${maxPercent - minPercent}%`,
          });
        }

        minValueLabel.text(formatNumberWithCommas(minVal));
        maxValueLabel.text(formatNumberWithCommas(maxVal));
        minInput.val(minVal).trigger("change");
        maxInput.val(maxVal).trigger("change");
      }

      function moveThumb(event, thumb) {
        const trackOffset = track.offset().left;
        const pageX = event.pageX || event.originalEvent.touches[0].pageX;
        let newLeft = pageX - trackOffset;

        newLeft = Math.max(0, Math.min(newLeft, track.width()));
        let newValue =
          Math.round(
            ((newLeft / track.width()) * (settings.max - settings.min)) /
            settings.step
          ) *
          settings.step +
          settings.min;

        if (isRTL) {
          newValue = settings.max - (newValue - settings.min);
        }

        if (thumb.is(minThumb)) {
          minVal = Math.min(newValue, maxVal - settings.step);
        } else if (thumb.is(maxThumb)) {
          maxVal = Math.max(newValue, minVal + settings.step);
        }

        updateThumbs();
      }

      function attachEvents(thumb) {
        thumb.on("mousedown touchstart", function (event) {
          event.preventDefault();
          $(document).on("mousemove touchmove", function (e) {
            moveThumb(e, thumb);
          });
          $(document).on("mouseup touchend", function () {
            $(document).off("mousemove touchmove");
            $(document).off("mouseup touchend");
          });
        });
      }

      attachEvents(minThumb);
      attachEvents(maxThumb);
      updateThumbs();
    });

    function formatNumberWithCommas(number) {
      return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
  };
})(jQuery);
$(".range-slider").customRangeSlider();
// Zoom effect

(function ($) {
  $.fn.zoomEffect = function (options) {
    const settings = $.extend({
      magnify: 2,
      enableOnTouch: true,
    }, options);

    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    return this.each(function () {
      const $container = $(this);
      const $img = $container.find('img');

      if ($img.length === 0) return;
      if (isTouchDevice && !settings.enableOnTouch) return; // ⛔ توقف اگر تاچ است و غیر فعال شده

      const $lens = $('<div class="zoom-lens"></div>').css({
        backgroundImage: `url('${$img.attr('src')}')`,
        backgroundSize: `${$img[0].naturalWidth * settings.magnify}px ${$img[0].naturalHeight * settings.magnify}px`,
        display: 'none',
        position: 'absolute',
        top: 0,
        left: 0,
        width: '100%',
        height: '100%',
        pointerEvents: 'none',
        zIndex: 10,
        backgroundRepeat: 'no-repeat',
      });

      $container.css('position', 'relative').append($lens);

      function updateLensPosition(x, y) {
        const offset = $img.offset();
        const relX = x - offset.left;
        const relY = y - offset.top;
        const width = $img.width();
        const height = $img.height();

        const xPercent = (relX / width) * 100;
        const yPercent = (relY / height) * 100;

        $lens.css({
          backgroundPosition: `${xPercent}% ${yPercent}%`,
        });
      }

      // Mouse
      $container
        .on('mousemove.zoomEffect', function (e) {
          updateLensPosition(e.pageX, e.pageY);
        })
        .on('mouseenter.zoomEffect', function () {
          $lens.show();
        })
        .on('mouseleave.zoomEffect', function () {
          $lens.hide();
        });

      // Touch (اگر فعال باشه)
      if (isTouchDevice && settings.enableOnTouch) {
        let touchActive = false;

        $container
          .on('touchstart.zoomEffect', function () {
            touchActive = true;
            $lens.show();
          })
          .on('touchmove.zoomEffect', function (e) {
            if (!touchActive) return;
            const touch = e.originalEvent.touches[0];
            if (touch) {
              updateLensPosition(touch.pageX, touch.pageY);
              e.preventDefault();
            }
          })
          .on('touchend.zoomEffect touchcancel.zoomEffect', function () {
            touchActive = false;
            $lens.hide();
          });
      }
    });
  };
})(jQuery);


$(function () {
  $('.presentation-card-image').zoomEffect({
    magnify: 2,
    enableOnTouch: false,
  });

});
