(function () {
  const html = document.documentElement;

  document.addEventListener('change', function (ev) {
    const themeInput = ev.target;

    if (themeInput.matches('input.toggle-theme')) {
      const theme = themeInput.value;
      const d = new Date();

      d.setTime(d.getTime() + (150 * 24 * 60 * 60 * 1000));

      html.setAttribute('data-bs-theme', theme);

      document.cookie = 'theme=' + theme + '; expires=' + d.toUTCString() + ';path=/;sameSite=lax;';
    }
  });
})();