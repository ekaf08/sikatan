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

  /* ---------------------------------------------------------------- */
  /* Pratinjau pengeluaran                                             */
  /* ---------------------------------------------------------------- */

  function parseRupiah(value) {
    var digits = String(value || "").replace(/[^\d]/g, "");
    return digits ? Number(digits) : 0;
  }

  function formatRupiah(value) {
    var negative = value < 0;
    return (negative ? "-Rp" : "Rp") + Math.abs(value).toLocaleString("id-ID");
  }

  function saldoOf(select) {
    if (!select) {
      return 0;
    }

    var option = select.options[select.selectedIndex];
    return option ? Number(option.getAttribute("data-saldo")) || 0 : 0;
  }

  document.querySelectorAll("[data-expense-preview]").forEach(function (root) {
    var posSelect = root.querySelector("[data-expense-pos]");
    var amountInput = root.querySelector("[data-expense-amount]");
    var descInput = root.querySelector("[data-expense-desc]");
    var saldoEl = root.querySelector("[data-expense-saldo]");
    var remainingEl = root.querySelector("[data-expense-remaining]");
    var publicEl = root.querySelector("[data-expense-public]");
    var publicPosEl = root.querySelector("[data-expense-public-pos]");
    var publicDescEl = root.querySelector("[data-expense-public-desc]");

    function render() {
      var saldo = saldoOf(posSelect);
      var amount = parseRupiah(amountInput ? amountInput.value : 0);

      if (saldoEl) {
        saldoEl.textContent = formatRupiah(saldo);
      }

      if (remainingEl) {
        remainingEl.textContent = formatRupiah(saldo - amount);
      }

      if (publicEl) {
        publicEl.textContent = formatRupiah(amount);
      }

      if (publicPosEl && posSelect) {
        publicPosEl.textContent = posSelect.value;
      }

      if (publicDescEl && descInput) {
        publicDescEl.textContent = descInput.value || "Catatan publik belum diisi";
      }
    }

    [posSelect, amountInput, descInput].forEach(function (element) {
      if (element) {
        element.addEventListener("input", render);
        element.addEventListener("change", render);
      }
    });

    render();
  });

  /* ---------------------------------------------------------------- */
  /* Pratinjau mutasi dana                                             */
  /* ---------------------------------------------------------------- */

  document.querySelectorAll("[data-mutation]").forEach(function (root) {
    var fromSelect = root.querySelector("[data-mutation-from]");
    var toSelect = root.querySelector("[data-mutation-to]");
    var amountInput = root.querySelector("[data-mutation-amount]");
    var fromLabel = root.querySelector("[data-mutation-from-label]");
    var toLabel = root.querySelector("[data-mutation-to-label]");
    var fromBefore = root.querySelector("[data-mutation-from-before]");
    var fromAfter = root.querySelector("[data-mutation-from-after]");
    var toBefore = root.querySelector("[data-mutation-to-before]");
    var toAfter = root.querySelector("[data-mutation-to-after]");

    function render() {
      var amount = parseRupiah(amountInput ? amountInput.value : 0);
      var fromSaldo = saldoOf(fromSelect);
      var toSaldo = saldoOf(toSelect);

      if (fromLabel && fromSelect) {
        fromLabel.textContent = fromSelect.value;
      }

      if (toLabel && toSelect) {
        toLabel.textContent = toSelect.value;
      }

      if (fromBefore) {
        fromBefore.textContent = formatRupiah(fromSaldo);
      }

      if (fromAfter) {
        fromAfter.textContent = formatRupiah(fromSaldo - amount);
      }

      if (toBefore) {
        toBefore.textContent = formatRupiah(toSaldo);
      }

      if (toAfter) {
        toAfter.textContent = formatRupiah(toSaldo + amount);
      }
    }

    [fromSelect, toSelect, amountInput].forEach(function (element) {
      if (element) {
        element.addEventListener("input", render);
        element.addEventListener("change", render);
      }
    });

    render();
  });

  /* ---------------------------------------------------------------- */
  /* Pratinjau periode laporan                                         */
  /* ---------------------------------------------------------------- */

  var reportPeriod = document.querySelector("[data-report-period]");
  var reportPeriodLabel = document.querySelector("[data-report-period-label]");

  if (reportPeriod && reportPeriodLabel) {
    var syncReportPeriod = function () {
      reportPeriodLabel.textContent = reportPeriod.value;
    };

    reportPeriod.addEventListener("change", syncReportPeriod);
    syncReportPeriod();
  }

  /* ---------------------------------------------------------------- */
  /* Ganti acara sedekah bumi                                          */
  /* ---------------------------------------------------------------- */

  document.querySelectorAll("[data-acara]").forEach(function (root) {
    var select = root.querySelector("[data-acara-select]");
    var titleEl = root.querySelector("[data-acara-title]");
    var subtitleEl = root.querySelector("[data-acara-subtitle]");

    if (!select) {
      return;
    }

    function renderAcara() {
      var option = select.options[select.selectedIndex];

      if (!option) {
        return;
      }

      if (titleEl && option.getAttribute("data-title")) {
        titleEl.textContent = option.getAttribute("data-title");
      }

      if (subtitleEl && option.getAttribute("data-subtitle")) {
        subtitleEl.textContent = option.getAttribute("data-subtitle");
      }
    }

    select.addEventListener("change", renderAcara);
  });

  /* ---------------------------------------------------------------- */
  /* Form contoh dengan umpan balik                                    */
  /* ---------------------------------------------------------------- */

  document.querySelectorAll("[data-demo-form]").forEach(function (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      var feedback = form.querySelector("[data-demo-feedback]");

      if (!feedback) {
        return;
      }

      feedback.hidden = false;
      feedback.textContent =
        form.getAttribute("data-demo-message") || "Perubahan disimpan (contoh).";

      window.clearTimeout(form._demoTimer);
      form._demoTimer = window.setTimeout(function () {
        feedback.hidden = true;
      }, 4000);
    });
  });

  /* ---------------------------------------------------------------- */
  /* Penyaring log audit                                               */
  /* ---------------------------------------------------------------- */

  document.querySelectorAll("[data-audit]").forEach(function (root) {
    var rows = Array.prototype.slice.call(root.querySelectorAll("tbody tr"));
    var filters = Array.prototype.slice.call(root.querySelectorAll("[data-audit-filter]"));
    var countEl = root.querySelector("[data-audit-count]");

    function applyAuditFilter() {
      var visible = 0;

      rows.forEach(function (row) {
        var match = filters.every(function (select) {
          var value = select.value;
          var attr = select.getAttribute("data-audit-filter");

          if (!value || value.indexOf("Semua") === 0) {
            return true;
          }

          return row.getAttribute(attr) === value;
        });

        row.hidden = !match;

        if (match) {
          visible += 1;
        }
      });

      if (countEl) {
        countEl.textContent =
          "Menampilkan " + visible + " dari " + rows.length + " catatan";
      }
    }

    filters.forEach(function (select) {
      select.addEventListener("change", applyAuditFilter);
    });
  });
})();
