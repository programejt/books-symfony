(function () {
  const html = document.documentElement;

  document.addEventListener('change', function (ev) {
    let themeInput = ev.target;

    if (themeInput.matches('input.toggle-theme')) {
      let theme = themeInput.value;

      html.setAttribute('data-bs-theme', theme);

      const d = new Date();
      d.setTime(d.getTime() + (150 * 24 * 60 * 60 * 1000));

      document.cookie = 'theme=' + theme + '; expires=' + d.toUTCString() + ';path=/;sameSite=lax;';
    }
  });
})();