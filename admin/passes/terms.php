<?php
/**
 * Club Yatri Admin - Core terms that apply to every pass (shown under the Exclusive Pass Collection).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/crud.php';
require_once __DIR__ . '/../../includes/content-db.php';
require_once __DIR__ . '/_tabs.php';

$pdo = admin_pdo();
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $terms = [];
        foreach ((array)($_POST['term_title'] ?? []) as $i => $tt) {
            $tt = mb_substr(trim((string)$tt), 0, 160);
            $tx = mb_substr(trim((string)($_POST['term_text'][$i] ?? '')), 0, 2000);
            if ($tt !== '' || $tx !== '') {
                $terms[] = [$tt, $tx];
            }
        }
        $checks = array_values(array_filter(array_map(static fn($l) => mb_substr(trim($l), 0, 120), preg_split('/[\r\n]+/', (string)($_POST['checklist'] ?? '')) ?: [])));
        site_settings_save($pdo, ['pass_core_terms' => json_encode($terms, JSON_UNESCAPED_UNICODE), 'pass_checklist' => implode("\n", $checks)]);
        admin_flash('success', 'Core terms saved.');
        admin_redirect('passes/terms.php');
    }
}

$terms = [];
$raw = (string)($pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'pass_core_terms'")->fetchColumn() ?: '');
$decoded = $raw !== '' ? json_decode($raw, true) : [];
foreach ((array)$decoded as $t) {
    $terms[] = ['title' => (string)($t[0] ?? ''), 'text' => (string)($t[1] ?? '')];
}
if (!$terms) {
    $terms = [['title' => '', 'text' => '']];
}
$checks = (string)($pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'pass_checklist'")->fetchColumn() ?: '');

$row = static fn(array $t): string => '<div class="border rounded-3 p-3 mb-2 bg-white" data-term>'
    . '<input class="form-control mb-2" name="term_title[]" maxlength="160" placeholder="Heading, e.g. Validity &amp; Usage" value="' . e($t['title'] ?? '') . '" aria-label="Term heading">'
    . '<textarea class="form-control" name="term_text[]" rows="2" maxlength="2000" placeholder="The wording" aria-label="Term text">' . e($t['text'] ?? '') . '</textarea>'
    . '<div class="d-flex gap-1 justify-content-end mt-2"><button class="btn btn-sm btn-light border" type="button" data-up title="Move up"><i class="bi bi-arrow-up"></i></button>'
    . '<button class="btn btn-sm btn-light border" type="button" data-down title="Move down"><i class="bi bi-arrow-down"></i></button>'
    . '<button class="btn btn-sm btn-outline-danger" type="button" data-remove title="Remove"><i class="bi bi-trash"></i></button></div></div>';

foreach ($errors as $err) {
    admin_flash('error', $err);
}
admin_header('Core terms', 'passes', [['Passes & Subscriptions', 'passes/index.php'], ['Core terms', null]], 'Terms that apply to every pass, shown under the Exclusive Pass Collection on the homepage.');
passes_tabs('terms');
?>
<form method="post" action="" novalidate>
  <?php echo admin_csrf_field(); ?>
  <div class="row g-3">
    <div class="col-xl-8">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Core terms &amp; conditions</h2><button class="btn btn-sm btn-cy" type="button" id="addTerm"><i class="bi bi-plus-lg"></i> Add term</button></div><div class="cy-card-body">
        <div id="terms"><?php foreach ($terms as $t) { echo $row($t); } ?></div>
        <template id="termTpl"><?php echo $row([]); ?></template>
        <div class="form-text">Empty rows are ignored. With no terms at all, the terms box is hidden on the website.</div>
      </div></div>
    </div>
    <div class="col-xl-4">
      <div class="cy-card mb-3"><div class="cy-card-head"><h2>Highlight chips</h2></div><div class="cy-card-body">
        <label class="form-label small fw-bold" for="checklist">Short promises under the terms</label>
        <textarea class="form-control" id="checklist" name="checklist" rows="5" placeholder="One per line"><?php echo e($checks); ?></textarea>
        <div class="form-text mb-3">Shown as ticked pills, e.g. "100% Inflation-Free Stays".</div>
        <button class="btn btn-cy w-100" type="submit"><i class="bi bi-check2-circle"></i> Save</button>
      </div></div>
    </div>
  </div>
</form>
<?php
admin_footer('<script>
(function () {
  var list = document.getElementById("terms"), tpl = document.getElementById("termTpl");
  document.getElementById("addTerm").addEventListener("click", function () { var n = tpl.content.firstElementChild.cloneNode(true); list.appendChild(n); n.querySelector("input").focus(); });
  list.addEventListener("click", function (e) {
    var row = e.target.closest("[data-term]"); if (!row) { return; }
    if (e.target.closest("[data-remove]")) { if (list.querySelectorAll("[data-term]").length > 1) { row.remove(); } else { row.querySelectorAll("input,textarea").forEach(function (i) { i.value = ""; }); } }
    else if (e.target.closest("[data-up]") && row.previousElementSibling) { list.insertBefore(row, row.previousElementSibling); }
    else if (e.target.closest("[data-down]") && row.nextElementSibling) { list.insertBefore(row.nextElementSibling, row); }
  });
})();
</script>');
