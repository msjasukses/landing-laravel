/* Panel admin: konfirmasi aksi destruktif & pratinjau gambar sebelum diunggah. */
(function () {
  'use strict';

  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('input[type="file"][data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var box = document.querySelector(input.dataset.preview);
      var file = input.files && input.files[0];
      if (!box || !file || file.type.indexOf('image/') !== 0) return;

      var img = box.querySelector('img');
      if (img.dataset.objectUrl) URL.revokeObjectURL(img.dataset.objectUrl);
      img.dataset.objectUrl = URL.createObjectURL(file);
      img.src = img.dataset.objectUrl;
      box.hidden = false;
      box.classList.add('preview--new');

      var empty = box.parentElement.querySelector('[data-preview-empty]');
      if (empty) empty.hidden = true;
    });
  });
})();
