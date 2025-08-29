(function () {
  const html = document.documentElement;

  document.addEventListener('change', function (ev) {
    const localeInput = ev.target;

    if (localeInput.matches('input.locale-input')) {
      const locale = localeInput.value;
      const d = new Date();

      d.setTime(d.getTime() + (150 * 24 * 60 * 60 * 1000));

      html.lang = locale;

      document.cookie = 'locale=' + locale + '; expires=' + d.toUTCString() + ';path=/;sameSite=lax;';

      this.location.reload();
    }
  });
})();