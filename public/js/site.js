/* Interaksi landing page: navbar, pencarian + filter grup, animasi muncul, tombol ke atas. */
(function () {
  'use strict';

  var nav = document.querySelector('[data-nav]');
  var toTop = document.querySelector('[data-to-top]');

  /* ---------------------------------------------------------- navbar & ke atas */

  function onScroll() {
    var y = window.scrollY;
    if (nav) nav.classList.toggle('is-scrolled', y > 24);
    if (toTop) toTop.hidden = y < 640;
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (toTop) {
    toTop.addEventListener('click', function () {
      window.scrollTo({ top: 0 });
    });
  }

  /* ------------------------------------------------- pencarian + filter grup */

  var input = document.getElementById('appSearch');
  var chips = document.querySelectorAll('[data-filter]');
  var groups = document.querySelectorAll('[data-group]');
  var count = document.querySelector('[data-count]');
  var noResults = document.querySelector('[data-no-results]');
  var activeGroup = '';

  function applyFilter() {
    var q = input ? input.value.trim().toLowerCase() : '';
    var total = 0;

    groups.forEach(function (group) {
      var cards = group.querySelectorAll('.app-card');
      var groupMatch = activeGroup === '' || group.dataset.group === activeGroup;
      var shown = 0;

      cards.forEach(function (card) {
        var match = groupMatch && (q === '' || (card.dataset.name || '').indexOf(q) !== -1);
        card.hidden = !match;
        if (match) shown++;
      });

      total += shown;
      group.hidden = !groupMatch || (q !== '' && shown === 0);
    });

    var filtering = q !== '' || activeGroup !== '';
    if (count) count.textContent = filtering && total > 0 ? total + ' aplikasi ditemukan' : '';
    if (noResults) noResults.hidden = !filtering || total > 0;
  }

  function setGroup(value) {
    activeGroup = value;
    chips.forEach(function (chip) {
      var on = chip.dataset.filter === value;
      chip.classList.toggle('is-active', on);
      chip.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    applyFilter();
  }

  if (input) input.addEventListener('input', applyFilter);

  chips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      setGroup(chip.dataset.filter);
    });
  });

  document.querySelectorAll('[data-reset]').forEach(function (button) {
    button.addEventListener('click', function () {
      if (input) input.value = '';
      setGroup('');
      if (input) input.focus();
    });
  });

  // "/" untuk langsung mencari, Esc untuk mengosongkan pencarian.
  document.addEventListener('keydown', function (event) {
    if (!input) return;
    var typing = /^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName);

    if (event.key === '/' && !typing) {
      event.preventDefault();
      input.focus({ preventScroll: true });
      input.scrollIntoView({ block: 'center' });
    } else if (event.key === 'Escape' && document.activeElement === input) {
      input.value = '';
      applyFilter();
    }
  });

  /* --------------------------------------------------------- animasi muncul */

  var revealables = document.querySelectorAll('[data-reveal]');

  if (!('IntersectionObserver' in window)) {
    revealables.forEach(function (el) { el.classList.add('is-visible'); });
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { rootMargin: '0px 0px -40px 0px', threshold: 0.08 });

  revealables.forEach(function (el) { observer.observe(el); });
})();
