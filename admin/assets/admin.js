/* Club Yatri Admin - shared behaviour: mobile sidebar, toasts, confirmation modal */
(function () {
  'use strict';

  // Mobile sidebar
  var body = document.body;
  var btn = document.getElementById('adminMenuBtn');
  var backdrop = document.getElementById('adminBackdrop');
  if (btn) { btn.addEventListener('click', function () { body.classList.toggle('sidebar-open'); }); }
  if (backdrop) { backdrop.addEventListener('click', function () { body.classList.remove('sidebar-open'); }); }
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { body.classList.remove('sidebar-open'); } });

  // Toasts
  window.adminToast = function (message, type) {
    var box = document.getElementById('adminToasts');
    if (!box || !window.bootstrap) { return; }
    var cls = { success: 'text-bg-success', error: 'text-bg-danger', danger: 'text-bg-danger', warning: 'text-bg-warning', info: 'text-bg-dark' }[type] || 'text-bg-dark';
    var el = document.createElement('div');
    el.className = 'toast align-items-center border-0 ' + cls;
    el.setAttribute('role', type === 'error' || type === 'danger' ? 'alert' : 'status');
    el.innerHTML = '<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
    el.querySelector('.toast-body').textContent = message;
    box.appendChild(el);
    var t = new bootstrap.Toast(el, { delay: type === 'error' ? 7000 : 4500 });
    el.addEventListener('hidden.bs.toast', function () { el.remove(); });
    t.show();
  };
  (window.ADMIN_FLASH || []).forEach(function (f) { window.adminToast(f.message, f.type); });

  // Confirmation modal for destructive actions:
  //   <button data-confirm="Archive this lead?" data-confirm-title="Archive" data-confirm-ok="Archive" type="submit">
  var modalEl = document.getElementById('confirmModal');
  if (modalEl && window.bootstrap) {
    var modal = new bootstrap.Modal(modalEl);
    var pending = null;
    document.addEventListener('click', function (e) {
      var t = e.target.closest('[data-confirm]');
      if (!t) { return; }
      e.preventDefault();
      pending = t;
      document.getElementById('confirmTitle').textContent = t.getAttribute('data-confirm-title') || 'Please confirm';
      document.getElementById('confirmBody').textContent = t.getAttribute('data-confirm');
      document.getElementById('confirmOk').textContent = t.getAttribute('data-confirm-ok') || 'Confirm';
      modal.show();
    });
    document.getElementById('confirmOk').addEventListener('click', function () {
      if (!pending) { return; }
      var t = pending; pending = null; modal.hide();
      var form = t.form || t.closest('form');
      if (form) {
        if (t.name) { var h = document.createElement('input'); h.type = 'hidden'; h.name = t.name; h.value = t.value; form.appendChild(h); }
        form.submit();
      } else if (t.href) { window.location = t.href; }
    });
  }
})();
