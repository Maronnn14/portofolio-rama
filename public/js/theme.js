/* ============================================
   THEME.JS — Pengalih mode terang / gelap
   Tema awal sudah dipasang inline di <head>; file ini
   menyambungkan tombol, menyimpan pilihan, dan menjaga aria.
   ============================================ */

(function () {
  'use strict';

  var root = document.documentElement;
  var STORAGE_KEY = 'portfolio_theme';
  var btn = null;
  var extras = [];

  function current() {
    return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
  }

  function syncButton(theme) {
    var dark = theme === 'dark';
    var label = dark ? 'Switch to light mode' : 'Switch to dark mode';
    if (btn) {
      btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
      btn.setAttribute('aria-label', label);
      btn.setAttribute('title', label);
    }
    extras.forEach(function (el) {
      el.setAttribute('aria-pressed', dark ? 'true' : 'false');
      var text = el.querySelector('[data-theme-label]');
      if (text) text.textContent = dark ? 'Switch to light mode' : 'Switch to dark mode';
    });
  }

  function apply(theme, persist) {
    root.setAttribute('data-theme', theme);
    syncButton(theme);
    if (persist) {
      try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
    }
    document.dispatchEvent(new CustomEvent('themechange', { detail: { theme: theme } }));
  }

  function init() {
    btn = document.getElementById('theme-toggle');
    extras = Array.prototype.slice.call(document.querySelectorAll('[data-theme-toggle]'));
    syncButton(current());

    var toggle = function () { apply(current() === 'dark' ? 'light' : 'dark', true); };

    if (btn) btn.addEventListener('click', toggle);
    extras.forEach(function (el) { el.addEventListener('click', toggle); });

    // Ikuti preferensi sistem selama pengguna belum memilih sendiri.
    if (window.matchMedia) {
      var mq = window.matchMedia('(prefers-color-scheme: dark)');
      var onChange = function (e) {
        var saved = null;
        try { saved = localStorage.getItem(STORAGE_KEY); } catch (err) {}
        if (!saved) apply(e.matches ? 'dark' : 'light', false);
      };
      if (mq.addEventListener) mq.addEventListener('change', onChange);
      else if (mq.addListener) mq.addListener(onChange);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  window.PortfolioTheme = {
    get: current,
    set: function (theme) { apply(theme, true); },
    toggle: function () { apply(current() === 'dark' ? 'light' : 'dark', true); }
  };
})();
