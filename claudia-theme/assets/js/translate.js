/*
 * Free language switch (German ⇄ English) via Google Website Translate.
 *
 * Privacy-friendly: Google is only contacted once a visitor actively chooses
 * English. German visitors never load any Google script.
 */
(function () {
  function getCookie(name) {
    var m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
    return m ? m.pop() : '';
  }

  function isEnglish() {
    return /\/en$/.test(getCookie('googtrans'));
  }

  function markActive() {
    var en = isEnglish();
    document.querySelectorAll('.site-lang [data-lang]').forEach(function (a) {
      a.classList.toggle('active', (a.getAttribute('data-lang') === 'en') === en);
    });
  }

  // Called by the Google script once it has loaded.
  window.googleTranslateElementInit = function () {
    if (window.google && google.translate) {
      new google.translate.TranslateElement(
        { pageLanguage: 'de', includedLanguages: 'de,en', autoDisplay: false },
        'google_translate_element'
      );
    }
  };

  function loadGoogle() {
    if (document.getElementById('claudia-gt-script')) return;
    var s = document.createElement('script');
    s.id = 'claudia-gt-script';
    s.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
    document.body.appendChild(s);
  }

  function setLanguage(lang) {
    if (lang === 'en') {
      document.cookie = 'googtrans=/de/en; path=/';
    } else {
      // Remove the cookie (host and dot-domain variants) to restore German.
      var expire = '; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
      document.cookie = 'googtrans=' + expire;
      document.cookie = 'googtrans=' + expire + '; domain=' + location.hostname;
      document.cookie = 'googtrans=' + expire + '; domain=.' + location.hostname;
    }
    location.reload();
  }

  document.addEventListener('click', function (e) {
    var el = e.target.closest('.site-lang [data-lang]');
    if (!el) return;
    e.preventDefault();
    setLanguage(el.getAttribute('data-lang'));
  });

  // If English is selected, load Google so the page is translated on arrival.
  if (isEnglish()) loadGoogle();
  markActive();
})();
