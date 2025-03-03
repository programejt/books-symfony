(function () {
  const html = document.documentElement;
  const attribute = 'data-bs-theme';

  document.addEventListener('click', function (ev) {
    let themeButton = ev.target.closest('button.toggle-theme');

    if (themeButton) {
      let theme = html.getAttribute(attribute) === 'dark' ? 'light' : 'dark';

      html.setAttribute(attribute, theme);

      const d = new Date();
      d.setTime(d.getTime() + (150 * 24 * 60 * 60 * 1000));

      document.cookie = 'theme=' + theme + '; expires=' + d.toUTCString() + ';path=/;sameSite=lax;';
    }
  });
})();