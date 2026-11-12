(function () {
  "use strict";

  var body = document.body;
  var sidebar = document.getElementById("appSidebar");
  var toggle = document.getElementById("sidebarToggle");
  var backdrop = document.getElementById("sidebarBackdrop");
  var desktopQuery = window.matchMedia("(min-width: 992px)");
  var storageKey = "sikatan.sidebar.collapsed";
  var storedCollapsed = false;

  try {
    storedCollapsed = window.localStorage.getItem(storageKey) === "1";
  } catch (error) {
    storedCollapsed = false;
  }

  function isDesktop() {
    return desktopQuery.matches;
  }

  function updateToggleIcon() {
    if (!toggle) {
      return;
    }

    var collapsed = body.classList.contains("sidebar-collapsed");
    var icon = toggle.querySelector("i");
    var expanded = isDesktop() ? !collapsed : body.classList.contains("sidebar-open");

    toggle.setAttribute("aria-expanded", String(expanded));
    toggle.setAttribute(
      "aria-label",
      isDesktop()
        ? collapsed
          ? "Perluas sidebar"
          : "Ciutkan sidebar"
        : "Buka menu navigasi"
    );

    if (icon) {
      icon.className = collapsed ? "bi bi-layout-sidebar" : "bi bi-layout-sidebar-inset";
    }
  }

  function updateNavTooltips() {
    if (!sidebar) {
      return;
    }

    var collapsed = isDesktop() && body.classList.contains("sidebar-collapsed");

    sidebar.querySelectorAll(".nav-item-app[data-label]").forEach(function (item) {
      if (collapsed) {
        item.setAttribute("title", item.getAttribute("data-label"));
      } else {
        item.removeAttribute("title");
      }
    });
  }

  function setCollapsed(collapsed, persist) {
    body.classList.toggle("sidebar-collapsed", collapsed);
    storedCollapsed = collapsed;

    if (persist !== false) {
      try {
        window.localStorage.setItem(storageKey, collapsed ? "1" : "0");
      } catch (error) {
        /* localStorage tidak tersedia */
      }
    }

    updateToggleIcon();
    updateNavTooltips();
  }

  function setMobileOpen(open) {
    body.classList.toggle("sidebar-open", open);

    if (backdrop) {
      backdrop.classList.toggle("show", open);
    }

    if (open) {
      body.style.overflow = "hidden";
    } else {
      body.style.overflow = "";
    }

    updateToggleIcon();
  }

  if (isDesktop() && storedCollapsed) {
    setCollapsed(true, false);
  } else {
    updateToggleIcon();
  }

  if (toggle) {
    toggle.addEventListener("click", function () {
      if (isDesktop()) {
        setCollapsed(!body.classList.contains("sidebar-collapsed"));
      } else {
        setMobileOpen(!body.classList.contains("sidebar-open"));
      }
    });
  }

  if (backdrop) {
    backdrop.addEventListener("click", function () {
      setMobileOpen(false);
    });
  }

  var closeButton = document.getElementById("sidebarClose");

  if (closeButton) {
    closeButton.addEventListener("click", function () {
      setMobileOpen(false);
    });
  }

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && body.classList.contains("sidebar-open")) {
      setMobileOpen(false);
    }
  });

  if (sidebar) {
    sidebar.querySelectorAll("a.nav-item-app").forEach(function (link) {
      link.addEventListener("click", function () {
        if (!isDesktop()) {
          setMobileOpen(false);
        }
      });
    });
  }

  var onBreakpointChange = function () {
    setMobileOpen(false);

    if (isDesktop()) {
      setCollapsed(storedCollapsed, false);
    } else {
      body.classList.remove("sidebar-collapsed");
      updateNavTooltips();
      updateToggleIcon();
    }
  };

  if (typeof desktopQuery.addEventListener === "function") {
    desktopQuery.addEventListener("change", onBreakpointChange);
  } else if (typeof desktopQuery.addListener === "function") {
    desktopQuery.addListener(onBreakpointChange);
  }

  /* ---------------------------------------------------------------- */
  /* Stepper jumlah KK + nominal otomatis                              */
  /* ---------------------------------------------------------------- */

  document.querySelectorAll("[data-stepper]").forEach(function (stepper) {
    var minus = stepper.querySelector("[data-step-minus]");
    var plus = stepper.querySelector("[data-step-plus]");
    var valueEl = stepper.querySelector("[data-step-value]");
    var totalEl = stepper.querySelector("[data-step-total]");
    var inputEl = stepper.querySelector("[data-step-input]");
    var price = Number(stepper.getAttribute("data-price")) || 0;
    var min = Number(stepper.getAttribute("data-min")) || 0;
    var max = Number(stepper.getAttribute("data-max")) || 9999;

    function formatNumber(value) {
      return value.toLocaleString("id-ID");
    }

    function render() {
      var value = Number(valueEl ? valueEl.textContent : 0) || 0;

      if (value < min) {
        value = min;
      }

      if (value > max) {
        value = max;
      }

      if (valueEl) {
        valueEl.textContent = String(value);
      }

      if (totalEl) {
        totalEl.textContent = "Rp" + formatNumber(value * price);
      }

      if (inputEl && inputEl.dataset.synced !== "false") {
        inputEl.value = formatNumber(value * price);
      }
    }

    if (minus) {
      minus.addEventListener("click", function () {
        var value = Number(valueEl ? valueEl.textContent : 0) - 1;
        if (valueEl) {
          valueEl.textContent = String(value);
        }
        render();
      });
    }

    if (plus) {
      plus.addEventListener("click", function () {
        var value = Number(valueEl ? valueEl.textContent : 0) + 1;
        if (valueEl) {
          valueEl.textContent = String(value);
        }
        render();
      });
    }

    render();
  });
})();
