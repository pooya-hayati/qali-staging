document.addEventListener("DOMContentLoaded", function () {
  const rugImage = document.querySelector('.summary-rug');

  function getSelectedRadioValue(name) {
    const selected = document.querySelector(`input[name="${name}"]:checked`);
    return {
      value: selected?.value || '',
      title: selected?.dataset.title || ''
    };
  }

  function updateImage() {
    const design = getSelectedRadioValue('wizard_design');
    const color = getSelectedRadioValue('wizard_color');

    if (design.value && color.value) {
      const base = rugImage.dataset.base;
      const fileName = `${design.value}-${color.value}`;
      rugImage.src = base.replace('{NAME}', fileName);
      rugImage.alt = `${design.title} / ${color.title}`;
    }
  }

  document.querySelectorAll('input[name="wizard_design"], input[name="wizard_color"]').forEach((radio) => {
    radio.addEventListener('change', updateImage);
  });

  updateImage();
  //
  const widthField = document.getElementById('wizard_width');
  const lengthField = document.getElementById('wizard_length');
  const shapeDiv = document.querySelector('.custom-size-shape');
  const shapeRadios = document.querySelectorAll('input[name="wizard_shape"]');

  function getSelectedShape() {
    const selected = document.querySelector('input[name="wizard_shape"]:checked');
    return selected ? selected.value : null;
  }

  function updateAspectRatio() {
    const width = parseFloat(widthField.value);
    const length = parseFloat(lengthField.value);
    if (width > 0 && length > 0) {
      shapeDiv.style.aspectRatio = `${width} / ${length}`;
    } else {
      shapeDiv.style.aspectRatio = '';
    }
  }

  function syncFields(sourceField, targetField) {
    const shape = getSelectedShape();
    if (shape === 'shape_square' || shape === 'shape_round') {
      targetField.value = sourceField.value;
    }
    updateAspectRatio();
  }

  widthField.addEventListener('input', () => syncFields(widthField, lengthField));
  lengthField.addEventListener('input', () => syncFields(lengthField, widthField));

  // در صورت تغییر شکل، دوباره همگام‌سازی انجام بشه
  shapeRadios.forEach((radio) => {
    radio.addEventListener('change', () => {
      const value = radio.value;
      if (radio.checked && (value === 'shape_square' || value === 'shape_round')) {
        if (widthField.value) lengthField.value = widthField.value;
        else if (lengthField.value) widthField.value = lengthField.value;
        updateAspectRatio();
      }
    });
  });


  // Wizard
  // ✅ راه‌اندازی Swiper برای فرم ویزارد
  const swiper = new Swiper(".wizard-swiper", {
    autoHeight: true,
    allowTouchMove: false,
    clickable: true,
    slideToClickedSlide: true,
    effect: "slide",
    speed: 600,
    on: {
      slideChange: function () {
        const activeSlide = this.slides[this.activeIndex];
        if ($(activeSlide).find('#custom-wizard-6').length > 0) {
          updateSummary();
        }
      }
    }
  });

  // ✅ انتخاب اسلایدها و تب‌ها
  const slides = document.querySelectorAll(".wizard-swiper>.swiper-wrapper>.swiper-slide");
  const allTabs = document.querySelectorAll(".section-tab li");

  // ✅ انیمیشن با GSAP برای هر مرحله
  function animateStep(index) {
    const el = slides[index];
    if (!el) return;

    const content = el.querySelector(".section-wrapper");
    if (content) {
      gsap.fromTo(
        content,
        { opacity: 0, y: 40 },
        { opacity: 1, y: 0, duration: 0.6, ease: "power2.out" }
      );
    }
  }

  // ✅ ولیدیشن مرحله‌ای
  function validateStep(index) {
    const inputs = slides[index].querySelectorAll(
      "input[required], select[required], textarea[required]"
    );
    let valid = true;

    inputs.forEach((input) => {
      if (!input.value.trim()) {
        input.classList.add("invalid");
        valid = false;
      } else {
        input.classList.remove("invalid");
      }
    });

    return valid;
  }

  // ✅ هندل کلیک روی تب‌ها
  allTabs.forEach((tab) => {
    const anchor = tab.querySelector("a");
    anchor.addEventListener("click", (e) => {
      e.preventDefault();

      const targetId = anchor.getAttribute("href").replace("#", "");
      const targetSlide = document.getElementById(targetId);
      const targetIndex = [...slides].findIndex(slide => slide.querySelector(`#${targetId}`));
      const currentIndex = swiper.activeIndex;

      if (targetIndex > currentIndex) {
        const isValid = validateStep(currentIndex);
        if (!isValid) return;
      }

      swiper.slideTo(targetIndex);
      animateStep(targetIndex);

      // ✅ بروزرسانی همه تب‌ها در کل فرم
      document.querySelectorAll(".section-tab li").forEach(t => {
        const href = t.querySelector("a").getAttribute("href").replace("#", "");
        t.classList.toggle("active", href === targetId);
      });
    });
  });

  // ✅ دکمه‌های داخل اسلاید مثل "Get Started"
  document.querySelectorAll(".section-btn[href^='#custom-wizard']").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const targetId = this.getAttribute("href").replace("#", "");
      const targetSlide = document.getElementById(targetId);
      const targetIndex = [...slides].findIndex(slide => slide.querySelector(`#${targetId}`));
      const currentIndex = swiper.activeIndex;

      if (targetIndex > currentIndex) {
        const isValid = validateStep(currentIndex);
        if (!isValid) return;
      }

      swiper.slideTo(targetIndex);
      animateStep(targetIndex);

      // ✅ بروزرسانی همه تب‌ها در کل فرم
      document.querySelectorAll(".section-tab li").forEach(tab => {
        const href = tab.querySelector("a").getAttribute("href").replace("#", "");
        tab.classList.toggle("active", href === targetId);
      });
    });
  });
  document.querySelectorAll(".wizard-prev, .wizard-next").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const href = this.getAttribute("href");
      if (!href || !href.startsWith("#")) return;

      const targetId = href.replace("#", "");
      const targetSlide = document.getElementById(targetId);
      const targetIndex = [...slides].findIndex(slide =>
        slide.querySelector(`#${targetId}`)
      );

      const currentIndex = swiper.activeIndex;

      if (this.classList.contains("wizard-next") && targetIndex > currentIndex) {
        const isValid = validateStep(currentIndex);
        if (!isValid) return;
      }

      swiper.slideTo(targetIndex);
      animateStep(targetIndex);

      document.querySelectorAll(".section-tab li").forEach((tab) => {
        const href = tab.querySelector("a").getAttribute("href").replace("#", "");
        tab.classList.toggle("active", href === targetId);
      });
    });
  });

  // ✅ اجرای انیمیشن اولیه
  animateStep(0);

  // ✅ اگر فرم نهایی داری:
  const wizardForm = document.getElementById("wizard-form");
  if (wizardForm) {
    wizardForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const currentIndex = swiper.activeIndex;
      const isValid = validateStep(currentIndex);

      if (isValid) {
        alert("فرم با موفقیت ارسال شد!");
        // ارسال AJAX یا رفتن به صفحه دیگر اینجا
      }
    });
  }
  // Design Slider
  const wizard_select = new Swiper(".wizard-select", {
    slidesPerView: "auto",
    centeredSlides: true,
    spaceBetween: 30,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    keyboard: {
      enabled: true,
    },
    on: {
      // وقتی اسلاید تغییر می‌کنه
      slideChange: function () {
        updateRadioSelection(this); // this همون instance مربوطه
      }
    }
  });

  // تابع انتخاب خودکار رادیو در اسلاید فعال
  function updateRadioSelection(swiperInstance) {
    // تمام رادیوها رو از حالت انتخاب خارج کن (فقط داخل این اسلایدر)
    swiperInstance.slides.forEach(slide => {
      const radio = slide.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = false;
      }
    });

    // رادیو داخل اسلاید فعال رو انتخاب کن
    const activeSlide = swiperInstance.slides[swiperInstance.activeIndex];
    const activeRadio = activeSlide.querySelector('input[type="radio"]');
    if (activeRadio) {
      activeRadio.checked = true;
    }
  }

  // اجرای اولیه وقتی صفحه لود میشه
  updateRadioSelection(wizard_select);

  // کلیک روی اسلاید باعث فعال‌شدنش بشه
  document.querySelectorAll('.wizard-design .swiper-slide').forEach((slide, i) => {
    slide.addEventListener('click', () => {
      // چون loop فعاله باید از slideToLoop استفاده کنیم
      wizard_select.slideToLoop(i);
    });
  });


  // Summary form
  function updateSummary() {
    // Shape
    let shapeTextarea = $('textarea[name="wizard_shape"]');
    let shapeRadio = $('[name="wizard_shape"]:radio:checked');
    let shapeText = shapeTextarea.length ? shapeTextarea.val().trim() : '';
    let shapeValue = shapeText || (shapeRadio.length ? shapeRadio.val() : '');
    let shapeTitle = shapeText || (shapeRadio.length ? shapeRadio.data('title') : '');
    $('.summary-shape div').text(shapeTitle);
    $('.summary-shape input').val(shapeValue);

    // Size
    let width = $('[name="wizard_width"]').val()?.trim() || '';
    let length = $('[name="wizard_length"]').val()?.trim() || '';
    let unit = $('[name="wizard_unit"]').val()?.trim() || '';
    let sizeText = width && length && unit ? `${width}×${length} ${unit}` : '';
    $('.summary-size div').text(sizeText);
    $('.summary-size input').val(sizeText);

    // Design
    let designTextarea = $('textarea[name="wizard_design"]');
    let designRadio = $('[name="wizard_design"]:radio:checked');
    let designText = designTextarea.length ? designTextarea.val().trim() : '';
    let designValue = designText || (designRadio.length ? designRadio.val() : '');
    let designTitle = designText || (designRadio.length ? designRadio.data('title') : '');
    $('.summary-design div').text(designTitle);
    $('.summary-design input').val(designValue);

    // Color
    let colorTextarea = $('textarea[name="wizard_color"]');
    let colorRadio = $('[name="wizard_color"]:radio:checked');
    let colorText = colorTextarea.length ? colorTextarea.val().trim() : '';
    let colorValue = colorText || (colorRadio.length ? colorRadio.val() : '');
    let colorTitle = colorText || (colorRadio.length ? colorRadio.data('title') : '');
    $('.summary-color div').text(colorTitle);
    $('.summary-color input').val(colorValue);

    // Image
    updateImage();

  }

});