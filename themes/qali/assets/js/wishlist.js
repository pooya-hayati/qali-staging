jQuery(document).ready(function ($) {
  initWishlist();

  function initWishlist() {
    updateWishlistCountBadge();
    syncGuestWishlistToCookie();
    mergeGuestWishlistIfLoggedIn();
    setActiveButtons();
    bindEvents();
    fetchAndRenderWishlist();
  }

  function getGuestWishlist() {
    return JSON.parse(localStorage.getItem("guest_wishlist") || "[]");
  }

  function setGuestWishlist(data) {
    localStorage.setItem("guest_wishlist", JSON.stringify(data));
    syncGuestWishlistToCookie();
  }

  function syncGuestWishlistToCookie() {
    if (!wishlist_params.isLoggedIn) {
      document.cookie = "guest_wishlist_transfer=" +
        encodeURIComponent(JSON.stringify(getGuestWishlist())) +
        "; max-age=2592000; path=/";
    }
  }

  function mergeGuestWishlistIfLoggedIn() {
    if (!wishlist_params.isLoggedIn) return;
    const guestWishlist = getGuestWishlist();
    if (!guestWishlist.length) return;

    $.post(URL_AJAX, {
      action: "merge_guest_wishlist",
      nonce: wishlist_params.nonce,
      ids: guestWishlist
    }, function (response) {
      if (response.success) {
        localStorage.removeItem("guest_wishlist");
        document.cookie = "guest_wishlist_transfer=; max-age=0; path=/";
        updateWishlistCountBadge();
      }
    });
  }

  function updateGuestWishlist(productId) {
    let wishlist = getGuestWishlist();
    const index = wishlist.indexOf(productId);

    if (index !== -1) {
      wishlist.splice(index, 1);
      showNotification("Removed", "Product removed from wishlist.", "error");
    } else {
      wishlist.push(productId);
      showNotification("Added", "Product added to wishlist.", "success");
    }

    setGuestWishlist(wishlist);
    updateWishlistCountBadge();
  }

  function updateWishlistCountBadge() {
    if (wishlist_params.isLoggedIn) {
      $.post(URL_AJAX, { action: 'get_wishlist_count', nonce: wishlist_params.nonce }, function (res) {
        if (res.success) {
          $(".wishlist-toggle strong").text(res.data.count);
        }
      });
    } else {
      $(".wishlist-toggle strong").text(getGuestWishlist().length);
    }
  }

  function setActiveButtons() {
    if (!wishlist_params.isLoggedIn) {
      const wishlist = getGuestWishlist();
      $(".wishlist-button").each(function () {
        const productId = parseInt($(this).data("product-id"));
        if (wishlist.includes(productId)) $(this).addClass("active");
      });
    }
  }

  function bindEvents() {
    $(document).on("click", ".wishlist-button", handleWishlistButton);
    $(document).on("click", ".remove-from-wishlist", handleRemoveFromWishlist);
    $(document).on("click", "#revoke-share-link", revokeShareLink);
    $(document).on("click", "#generate-share-link", generateShareLink);
  }

  function handleWishlistButton() {
    const $btn = $(this);
    const productId = parseInt($btn.data("product-id"));
    // Same product can have more than one wishlist-button on screen at once
    // (e.g. the product-card hover badge and another instance of the same
    // card elsewhere) — keep every instance of it in sync, not just the one
    // that was clicked.
    const $allForProduct = $(`.wishlist-button[data-product-id="${productId}"]`);

    if (!wishlist_params.isLoggedIn) {
      updateGuestWishlist(productId);
      $allForProduct.toggleClass("active");
      return;
    }

    $.post(URL_AJAX, {
      action: "toggle_wishlist",
      nonce: wishlist_params.nonce,
      product_id: productId
    }, function (response) {
      if (response.success) {
        $allForProduct.toggleClass("active");
        updateWishlistCountBadge();
        showNotification("Wishlist updated", response.data.status === "added" ? "Added to your wishlist." : "Removed from your wishlist.", "info");
      }
    });
  }

  function handleRemoveFromWishlist() {
    const productId = $(this).data("product-id");
    const $row = $(this).closest("tr, .wishlist-product");

    if (!wishlist_params.isLoggedIn) {
      updateGuestWishlist(productId);
      $row.fadeOut(300, function () {
        $(this).remove();
        updateWishlistCountBadge();
        if (!$(".wishlist-product, .wishlist_table tbody tr").length) showEmptyMessage();
      });
    } else {
      $.post(URL_AJAX, {
        action: "toggle_wishlist",
        nonce: wishlist_params.nonce,
        product_id: productId
      }, function (response) {
        if (response.success) {
          showNotification("Removed", "Product removed from wishlist.", "error");
          $row.fadeOut(300, function () {
            $(this).remove();
            updateWishlistCountBadge();
            if (!$(".wishlist-product, .wishlist_table tbody tr").length) showEmptyMessage();
          });
        }
      });
    }
  }

  function revokeShareLink(e) {
    e.preventDefault();
    if (!confirm("Are you sure you want to revoke the share link?")) return;

    $.post(URL_AJAX, {
      action: "revoke_wishlist_token",
      nonce: wishlist_params.nonce
    }, function (response) {
      if (response.success) {
        showNotification("Success", response.data.message || "Share link revoked.", "info");
        $(".wishlist-share-box").html('<p><strong>No share link available.</strong></p><button id="generate-share-link" class="button">Generate New Share Link</button>');
      }
    });
  }

  function generateShareLink(e) {
    e.preventDefault();

    $.post(URL_AJAX, {
      action: "generate_wishlist_token",
      nonce: wishlist_params.nonce
    }, function (response) {
      if (response.success) {
        showNotification("Success", response.data.message, "success");
        $(".wishlist-share-box").html('<p><strong>Share your wishlist:</strong></p><input type="text" value="' + response.data.url + '" readonly onclick="this.select();" /><button id="revoke-share-link" class="button">Revoke Share Link</button>');
      }
    });
  }

  function fetchAndRenderWishlist() {
    const container = document.getElementById("wishlist-grid");
    if (!container) return;
    container.innerHTML = '<div class="alert alert-info">⏳ Loading wishlist...</div>';

    const isLoggedIn = wishlist_params.isLoggedIn;
    const action = isLoggedIn ? "get_wishlist_products" : "get_guest_wishlist_products";
    const data = isLoggedIn ? { action, nonce: wishlist_params.nonce } : { action, ids: getGuestWishlist() };

    if (!isLoggedIn && data.ids.length === 0) return container.innerHTML = '<div class="alert alert-info">Your wishlist is empty.</div>';

    $.post(URL_AJAX, data, function (res) {
      if (res.success) {
        renderWishlistProducts(res.data, container);
      } else {
        container.innerHTML = '<div class="alert alert-warning">There was an error loading your wishlist.</div>';
      }
    });
  }

  function renderWishlistProducts(data, container) {
    if (!data.products || !data.products.length) return showEmptyMessage(container);

    const html = data.products.map(product => `<div class="col-sm-6 col-md-4 col-lg-4 col-xl-4">
    <div class="product-card" data-product-id="${product.id}">
      <div class="product-card-header">
        <a href="${product.url}" title="${product.name}">
          <img src="${product.image}" alt="${product.name}" class="product-card-img">
        </a>
      </div>
      <div class="product-card-body">
        <h3 class="product-card-title">
          <a href="${product.url}" title="${product.name}">${product.name}</a>
        </h3>
        <div class="product-card-meta">
          <span class="product-card-price">${product.price}</span>
          <button class="wishlist-button active" data-product-id="${product.id}"><span>❤</span></button>
        </div>
      </div>
      <div class="product-card-footer">
        <a href="${product.url}?add-to-cart=${product.id}" class="product-card-btn">Add to Cart</a>
      </div>
    </div>
  </div>`).join("");


    container.innerHTML = html;
  }

  function showEmptyMessage(container = document.getElementById("wishlist-grid")) {
    container.innerHTML = '<div class="alert alert-info">Your wishlist is empty.</div>';
  }

  function showNotification(title, message, type) {
    butterup.toast({
      title: title,
      message: message,
      type: type,
      location: "top-" + !directionConfig.endSide,
      dismissable: true,
    });
  }
});
