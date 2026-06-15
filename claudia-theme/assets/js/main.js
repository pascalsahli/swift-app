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
      msg.textContent = 'Vielen Dank! Du bist jetzt angemeldet. (Demo-Vorschau)';
      form.parentNode.insertBefore(msg, form);
      form.reset();
      form.style.display = 'none';
    });
  });
})();
