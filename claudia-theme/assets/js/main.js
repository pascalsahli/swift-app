/* Mobile navigation toggle */
(function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.site-nav');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', function () {
    var open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.textContent = open ? '✕' : '☰';
  });

  nav.addEventListener('click', function (e) {
    if (e.target.tagName === 'A') {
      nav.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.textContent = '☰';
    }
  });
})();

/* Demo-only: simulate the newsletter signup in the static preview.
   On the real WordPress site the form posts to the server instead. */
(function () {
  document.querySelectorAll('form.subscribe[data-demo]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = form.querySelector('input[type="email"]');
      if (input && !input.value) { input.focus(); return; }
      var msg = document.createElement('p');
      msg.className = 'form-message is-success';
      msg.textContent = 'Danke! Wir haben dir eine Bestätigungs-E-Mail geschickt – bitte klicke auf den Link darin. (Demo-Vorschau)';
      form.parentNode.insertBefore(msg, form);
      form.reset();
      form.style.display = 'none';
    });
  });
})();

/* Newsletter signup on the live site: submit via AJAX so the thank-you
   message appears instantly, without reloading the page. */
(function () {
  if (typeof ClaudiaNL === 'undefined') return; // only on WordPress

  function showMessage(form, text, ok) {
    var prev = form.parentNode.querySelector('.form-message');
    if (prev) prev.remove();
    var msg = document.createElement('p');
    msg.className = 'form-message ' + (ok ? 'is-success' : 'is-error');
    msg.textContent = text;
    form.parentNode.insertBefore(msg, form);
  }

  document.querySelectorAll('form.subscribe:not([data-demo])').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var email = form.querySelector('input[type="email"]');
      if (email && !email.value) { email.focus(); return; }

      var data = new FormData(form);
      data.set('action', 'claudia_ajax_subscribe');
      var btn = form.querySelector('button');
      if (btn) btn.disabled = true;

      fetch(ClaudiaNL.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          showMessage(form, res.message, res.ok);
          if (res.ok) { form.reset(); form.style.display = 'none'; }
          else if (btn) { btn.disabled = false; }
        })
        .catch(function () {
          if (btn) btn.disabled = false;
          showMessage(form, 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.', false);
        });
    });
  });
})();
