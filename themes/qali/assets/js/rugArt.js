$(document).ready(function () {
  var swiper = new Swiper(".art-slideshow", {
    direction: "vertical",
    slidesPerView: 1,
    spaceBetween: 0,
    pagination: {
      el: ".swiper-pagination",
      type: "fraction",
    },
    mousewheel: {
      forceToAxis: true,
      releaseOnEdges: false,
      thresholdDelta: 25,
      thresholdTime: 100,
      sensitivity: 1,
    },
    keyboard: {
      enabled: true,
    },
    nested: true,
    touchStartPreventDefault: false,
    simulateTouch: true,
    on: {
      init: function () {
        this.el.setAttribute('data-current-slide', this.realIndex + 1);
      },
      slideChange: function () {
        this.el.setAttribute('data-current-slide', this.realIndex + 1);
      }
    }
  });

  var curveSwiper = new Swiper(".curve-carousel", {
    grabCursor: true,
    centeredSlides: true,
    effect: "creative",
    slidesPerView: "auto",
    loop: false,
    creativeEffect: {
      perspective: false,
      limitProgress: 10,
      prev: {
        translate: ["-100%", 0, 0],
        rotate: [0, 0, -15],
        origin: "right bottom"
      },
      next: {
        translate: ["100%", 0, 0],
        rotate: [0, 0, 15],
        origin: "left bottom"
      }
    },
    keyboard: {
      enabled: true,
    },
  });

  const sliders = document.querySelectorAll('.inline-swiper');

  sliders.forEach((sliderEl) => {
    const swiper = new Swiper(sliderEl, {
      loop: true,
      effect: 'fade',
      speed: 10,
      autoplay: {
        delay: 1000,
        disableOnInteraction: false,
      },
      fadeEffect: {
        crossFade: true
      },
      on: {
        slideChangeTransitionEnd: function () {
          // مقدار delay بین 50 تا 1000 میلی‌ثانیه به‌صورت رندوم
          const randomDelay = Math.floor(Math.random() * (1000 - 50 + 1)) + 50;
          swiper.params.autoplay.delay = randomDelay;
          swiper.autoplay.start();
        }
      }
    });
  });
  // Product Gallery
  document.querySelectorAll(".art-gallery").forEach(function (galleryWrapper, index) {
    const thumbEl = galleryWrapper.querySelector(".art-gallery-thumb");
    const largeEl = galleryWrapper.querySelector(".art-gallery-large");

    // تولید شناسه یکتا برای هر گالری
    const thumbId = "thumb-swiper-" + index;
    const largeId = "large-swiper-" + index;

    thumbEl.classList.add(thumbId);
    largeEl.classList.add(largeId);

    const galleryThumb = new Swiper(`.${thumbId}`, {
      direction: "vertical",
      spaceBetween: 8,
      slidesPerView: 4,
      watchSlidesProgress: true,
      watchSlidesVisibility: true,
      slideToClickedSlide: true,
    });

    const galleryLarge = new Swiper(`.${largeId}`, {
      loop: true,
      slidesPerView: 1,
      thumbs: {
        swiper: galleryThumb,
      },
    });
  });
  // Float Gallery
  const container = document.querySelector('#slide-7B');
  const items = container.querySelectorAll('.slide-floating>li');

  container.addEventListener('mousemove', (e) => {
    const rect = container.getBoundingClientRect();
    const offsetX = e.clientX - rect.left;
    const offsetY = e.clientY - rect.top;

    items.forEach((item, index) => {
      const moveFactor = (index % 2 === 0 ? 1 : -1) * 20;
      const translateX = (offsetX - rect.width / 2) / rect.width * moveFactor;
      const translateY = (offsetY - rect.height / 2) / rect.height * moveFactor;

      item.style.transform = `translate(${translateX}px, ${translateY}px)`;
    });
  });

  container.addEventListener('mouseleave', () => {
    items.forEach(item => item.style.transform = 'translate(0, 0)');
  });
  // Collector Swiper
  var swiper = new Swiper('.slide-cta-collector', {
    direction: 'vertical',
    loop: true,
    autoplay: {
      delay: 1,
      disableOnInteraction: false,
    },
    speed: 2000,
    slidesPerView: 3,
    spaceBetween: 0,
    freeMode: true,
    freeModeMomentum: false,
    freeModeMomentumBounce: false,
    freeModeMinimumVelocity: 0.02,
  });
  // Map Rug
  function createMap(mapId, locations, boxId) {
    const map = L.map(mapId, {
      zoomControl: false,
      attributionControl: false,
      dragging: true,
      scrollWheelZoom: false
    }).setView([0, 0], 2);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    fetch('https://d2ad6b4ur7yvpq.cloudfront.net/naturalearth-3.3.0/ne_50m_land.geojson')
      .then(response => response.json())
      .then(data => {
        L.geoJSON(data, {
          style: {
            color: '#AC1F1F',
            fillColor: '#AC1F1F',
            weight: 0,
            fillOpacity: .1
          }
        }).addTo(map);
      });

    const markers = [];

    locations.forEach((loc, index) => {
      const marker = L.marker(loc.coords, {
        icon: L.divIcon({
          className: 'map-pin',
          html: '<span class="map-marker"></span>'
        })
      }).addTo(map);

      marker.on('click', () => {
        document.querySelectorAll('.map-pin').forEach(pin => {
          pin.classList.remove('active');
        });

        const currentPin = marker.getElement();
        if (currentPin) {
          currentPin.classList.add('active');
        }

        const box = document.getElementById(boxId);
        if (box) {
          box.querySelector('.map-box-title').innerText = loc.title;
          box.querySelector('.map-box-desc').innerText = loc.desc;
          box.querySelector('.map-box-img').src = loc.img || '';
          box.querySelector('.map-box-img').alt = loc.title;
        }
      });

      markers.push({ marker, loc });
    });

    // انتخاب تصادفی یکی از نقاط و شبیه‌سازی کلیک
    const randomIndex = Math.floor(Math.random() * markers.length);
    markers[randomIndex].marker.fire('click');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const elRug = document.getElementById('locations-rug');
    const elPaint = document.getElementById('locations-paint');

    if (elRug && elPaint) {
      const locationsRug = JSON.parse(elRug.textContent);
      const locationsPaint = JSON.parse(elPaint.textContent);

      // حالا با آرایه‌ها می‌تونی کار کنی
      console.log(locationsRug);
      console.log(locationsPaint);
    } else {
      console.warn('JSON elements not found in the DOM.');
    }
  });

  createMap('map-1', locationsRug, 'map-box-1');

  let map2Created = false;

  document.querySelector('a[href="#tab-map-2"]').addEventListener('click', function () {

    if (!map2Created) {
      setTimeout(() => {
        createMap('map-2', locationsPaint, 'map-box-2');
      }, 500);
      map2Created = true;
    }

  });







});
