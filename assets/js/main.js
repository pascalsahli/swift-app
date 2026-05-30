/* =================================================================
   Sahli — site behaviour: language switching, nav, footer year
   ================================================================= */
(function () {
  "use strict";

  var DICT = window.SAHLI_I18N || {};
  var STORAGE_KEY = "sahli-lang";
  var DEFAULT_LANG = "de";

  function getLang() {
    var saved = localStorage.getItem(STORAGE_KEY);
    if (saved && DICT[saved]) return saved;
    return DEFAULT_LANG;
  }

  function applyLang(lang) {
    if (!DICT[lang]) lang = DEFAULT_LANG;
    var table = DICT[lang];

    // text content
    document.querySelectorAll("[data-i18n]").forEach(function (el) {
      var key = el.getAttribute("data-i18n");
      if (table[key] != null) el.textContent = table[key];
    });

    // attributes:  data-i18n-attr="placeholder:key,aria-label:key"
    document.querySelectorAll("[data-i18n-attr]").forEach(function (el) {
      el.getAttribute("data-i18n-attr").split(",").forEach(function (pair) {
        var parts = pair.split(":");
        var attr = parts[0].trim();
        var key = parts[1] && parts[1].trim();
        if (key && table[key] != null) el.setAttribute(attr, table[key]);
      });
    });

    // document title
    var titleKey = document.body.getAttribute("data-title-key");
    if (titleKey && table[titleKey]) document.title = table[titleKey];

    // <html lang> + remember choice
    document.documentElement.setAttribute("lang", lang);
    localStorage.setItem(STORAGE_KEY, lang);

    // toggle active state on switch buttons
    document.querySelectorAll(".lang-switch button").forEach(function (btn) {
      btn.classList.toggle("is-active", btn.getAttribute("data-lang") === lang);
    });

    // reveal anything hidden pre-translation
    document.querySelectorAll(".lang-hidden").forEach(function (el) {
      el.classList.remove("lang-hidden");
    });
  }

  function initLangSwitch() {
    document.querySelectorAll(".lang-switch button").forEach(function (btn) {
      btn.addEventListener("click", function () {
        applyLang(btn.getAttribute("data-lang"));
      });
    });
  }

  function initNavToggle() {
    var toggle = document.querySelector(".nav-toggle");
    var links = document.querySelector(".nav-links");
    if (!toggle || !links) return;
    toggle.addEventListener("click", function () {
      var open = links.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    links.querySelectorAll("a").forEach(function (a) {
      a.addEventListener("click", function () {
        links.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
      });
    });
  }

  function markActiveNav() {
    var page = document.body.getAttribute("data-page");
    if (!page) return;
    document.querySelectorAll(".nav-links a[data-nav]").forEach(function (a) {
      if (a.getAttribute("data-nav") === page) a.classList.add("is-active");
    });
  }

  function setFooterYear() {
    var el = document.getElementById("year");
    if (el) el.textContent = new Date().getFullYear();
  }

  document.addEventListener("DOMContentLoaded", function () {
    applyLang(getLang());
    initLangSwitch();
    initNavToggle();
    markActiveNav();
    setFooterYear();
  });
})();
