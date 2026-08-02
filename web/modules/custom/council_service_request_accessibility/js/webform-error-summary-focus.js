((Drupal, once) => {
  function focusErrorTarget(link) {
    const href = link.getAttribute('href');
    if (!href || !href.startsWith('#')) {
      return;
    }

    const target = document.getElementById(href.slice(1));
    if (!target || !target.closest('.webform-submission-form')) {
      return;
    }

    window.setTimeout(() => {
      target.focus();
      target.classList.add('webform-error-summary-focus');

      window.setTimeout(() => {
        target.classList.remove('webform-error-summary-focus');
      }, 2000);
    }, 350);
  }

  Drupal.behaviors.councilServiceRequestErrorSummaryFocus = {
    attach(context) {
      once(
        'council-service-request-error-summary-focus',
        '.webform-submission-form [data-drupal-messages] .messages__content a[href^="#edit-"]',
        context,
      ).forEach((link) => {
        link.addEventListener('click', () => {
          focusErrorTarget(link);
        });
      });
    },
  };
})(Drupal, once);
