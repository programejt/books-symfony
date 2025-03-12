(function () {
  const html = document.documentElement;

  document.addEventListener('change', function (ev) {
    let localeInput = ev.target;

    if (localeInput.matches('input.locale-input')) {
      let locale = localeInput.value;

      html.lang = locale;

      const d = new Date();
      d.setTime(d.getTime() + (150 * 24 * 60 * 60 * 1000));

      document.cookie = 'locale=' + locale + '; expires=' + d.toUTCString() + ';path=/;sameSite=lax;';

      this.location.reload();
    }
  });
})();