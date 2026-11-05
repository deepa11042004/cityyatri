<?php
/**
 * Club Yatri Admin - Lead / booking request detail, status, follow-up, notes.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/leads_repo.php';

$pdo = admin_pdo();
$kind = (($_GET['kind'] ?? '') === 'booking') ? 'booking' : 'inquiry';
$id = (int)($_GET['id'] ?? 0);
$lead = $id > 0 ? lead_get($pdo, $kind, $id) : null;

if (!$lead) {
    admin_flash('error', 'That lead could not be found.');
    admin_redirect('leads/index.php');
}

$admin = require_admin();

// Opening a lead marks it as read
if (!(int)$lead['is_read']) {
    lead_apply($pdo, $kind, $id, 'read');
    $lead['is_read'] = 1;
}

$isBooking = $kind === 'booking';
$name = $isBooking ? $lead['guest_name'] : $lead['full_name'];
$phone = (string)($isBooking ? $lead['guest_phone'] : $lead['phone']);
$email = (string)($isBooking ? $lead['guest_email'] : $lead['email']);
$ref = $isBooking ? (string)$lead['booking_code'] : 'INQ-' . $lead['id'];
$source = $isBooking ? 'booking' : (string)($lead['source'] ?: $lead['type']);
$status = (string)$lead['lead_status'];
$wa = lead_whatsapp_url($phone);
$notes = lead_notes($pdo, $kind, $id);

// Only show what the form actually collected
$fields = [];
if ($isBooking) {
    $fields = [
        'Booking code' => $lead['booking_code'], 'Destination' => $lead['destination_name'], 'Resort' => $lead['resort_name'],
        'Room type' => $lead['room_type'], 'Season' => $lead['season'], 'Check-in date' => $lead['checkin_date'] ? date('j M Y', strtotime((string)$lead['checkin_date'])) : '',
        'Duration' => $lead['duration_nights'] ? $lead['duration_nights'] . ' nights' : '', 'Guests' => $lead['guests_count'],
        'Booking status' => ucfirst((string)$lead['status']), 'Member ID' => $lead['member_id'], 'Special requests' => $lead['special_requests'],
    ];
} else {
    $fields = [
        'Subject' => $lead['subject'], 'City' => $lead['city'], 'Pass of interest' => $lead['pass_tier'],
        'Preferred call time' => $lead['preferred_time'], 'Message' => $lead['notes'],
    ];
    $extra = $lead['extra'] ? json_decode((string)$lead['extra'], true) : null;
    if (is_array($extra)) {
        foreach ($extra as $k => $v) {
            $fields[ucfirst(str_replace('_', ' ', (string)$k))] = is_scalar($v) ? (string)$v : json_encode($v);
        }
    }
    $fields['IP address'] = $lead['ip_address'];
}
$fields = array_filter($fields, static fn($v) => $v !== null && $v !== '');

$self = 'view.php?kind=' . $kind . '&id=' . $id;
$post = static function (string $op, string $label, string $class = 'btn-light border', string $icon = '', string $confirm = '') use ($kind, $id, $self): string {
    $attrs = $confirm !== '' ? ' data-confirm="' . e($confirm) . '" data-confirm-title="' . e($label) . '" data-confirm-ok="' . e($label) . '"' : '';
    return '<form method="post" action="action.php" class="d-inline">' . admin_csrf_field()
        . '<input type="hidden" name="kind" value="' . e($kind) . '"><input type="hidden" name="id" value="' . $id . '">'
        . '<input type="hidden" name="op" value="' . e($op) . '"><input type="hidden" name="back" value="' . e($self) . '">'
        . '<button class="btn btn-sm ' . $class . '" type="submit"' . $attrs . '>' . ($icon ? '<i class="bi ' . $icon . '"></i> ' : '') . e($label) . '</button></form>';
};

admin_header($ref, 'leads', [['Leads & Enquiries', 'leads/index.php'], [$ref, null]], '');
?>
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
  <a class="btn btn-sm btn-light border" href="index.php"><i class="bi bi-arrow-left"></i> All leads</a>
  <span class="src src-<?php echo e($source); ?>"><?php echo e(lead_source_label($source)); ?></span>
  <span class="pill pill-<?php echo e($status); ?>"><?php echo e(lead_status_label($status)); ?></span>
  <?php if ((int)$lead['is_archived']): ?><span class="pill pill-lost"><?php echo (int)$lead['is_spam'] ? 'Spam' : 'Archived'; ?></span><?php endif; ?>
  <span class="text-muted small ms-sm-auto">Received <?php echo e(date('j M Y, g:i a', strtotime((string)$lead['created_at']))); ?> (<?php echo e(admin_ago((string)$lead['created_at'])); ?>)</span>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="cy-card mb-3">
      <div class="cy-card-head">
        <h2><?php echo e($name !== '' ? $name : '(no name)'); ?></h2>
        <div class="d-flex gap-2 flex-wrap">
          <?php if ($phone !== ''): ?><a class="btn btn-sm btn-cy" href="tel:<?php echo e(preg_replace('/[^\d+]/', '', $phone)); ?>"><i class="bi bi-telephone"></i> Call</a><?php endif; ?>
          <?php if ($wa !== ''): ?><a class="btn btn-sm btn-success" href="<?php echo e($wa); ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> WhatsApp</a><?php endif; ?>
          <?php if ($email !== ''): ?><a class="btn btn-sm btn-light border" href="mailto:<?php echo e($email); ?>"><i class="bi bi-envelope"></i> Email</a><?php endif; ?>
        </div>
      </div>
      <div class="cy-card-body">
        <dl class="detail-list">
          <dt>Name</dt><dd><?php echo e($name !== '' ? $name : '—'); ?></dd>
          <dt>Phone</dt><dd><?php echo $phone !== '' ? e($phone) : '—'; ?></dd>
          <dt>Email</dt><dd><?php echo $email !== '' ? e($email) : '—'; ?></dd>
          <?php foreach ($fields as $label => $value): ?>
            <dt><?php echo e((string)$label); ?></dt><dd><?php echo nl2br(e((string)$value)); ?></dd>
          <?php endforeach; ?>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="cy-card mb-3">
      <div class="cy-card-head"><h2>Manage</h2>
        <?php echo (int)$lead['is_read'] ? $post('unread', 'Mark unread', 'btn-light border', 'bi-envelope') : ''; ?>
      </div>
      <div class="cy-card-body">
        <form method="post" action="action.php" class="mb-3">
          <?php echo admin_csrf_field(); ?>
          <input type="hidden" name="kind" value="<?php echo e($kind); ?>"><input type="hidden" name="id" value="<?php echo $id; ?>">
          <input type="hidden" name="op" value="status"><input type="hidden" name="back" value="<?php echo e($self); ?>">
          <label class="form-label fw-bold small text-uppercase text-muted" for="statusSel">Lead status</label>
          <div class="input-group">
            <select class="form-select" id="statusSel" name="value">
              <?php foreach (LEAD_STATUSES as $k => $label): ?><option value="<?php echo e($k); ?>" <?php echo $status === $k ? 'selected' : ''; ?>><?php echo e($label); ?></option><?php endforeach; ?>
            </select>
            <button class="btn btn-cy" type="submit">Update</button>
          </div>
        </form>

        <form method="post" action="action.php" class="mb-3">
          <?php echo admin_csrf_field(); ?>
          <input type="hidden" name="kind" value="<?php echo e($kind); ?>"><input type="hidden" name="id" value="<?php echo $id; ?>">
          <input type="hidden" name="op" value="follow_up"><input type="hidden" name="back" value="<?php echo e($self); ?>">
          <label class="form-label fw-bold small text-uppercase text-muted" for="fu">Follow-up date &amp; time</label>
          <div class="input-group">
            <input class="form-control" type="datetime-local" id="fu" name="value" value="<?php echo $lead['follow_up_at'] ? e(date('Y-m-d\TH:i', strtotime((string)$lead['follow_up_at']))) : ''; ?>">
            <button class="btn btn-cy-gold" type="submit">Set</button>
          </div>
          <div class="form-text">Leave empty and press Set to clear the reminder.</div>
        </form>

        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
          <?php if ((int)$lead['is_archived'] || (int)$lead['is_spam']): ?>
            <?php echo $post('unarchive', 'Restore', 'btn-light border', 'bi-arrow-counterclockwise'); ?>
          <?php else: ?>
            <?php echo $post('archive', 'Archive', 'btn-light border', 'bi-archive', 'Archive this lead? It moves out of the active list but is not deleted.'); ?>
            <?php echo $post('spam', 'Mark as spam', 'btn-outline-danger', 'bi-shield-exclamation', 'Mark this lead as spam and archive it? You can restore it later from the Spam tab.'); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="cy-card">
      <div class="cy-card-head"><h2>Internal notes</h2><span class="text-muted small"><?php echo count($notes); ?></span></div>
      <div class="cy-card-body">
        <form method="post" action="action.php" class="mb-3">
          <?php echo admin_csrf_field(); ?>
          <input type="hidden" name="kind" value="<?php echo e($kind); ?>"><input type="hidden" name="id" value="<?php echo $id; ?>">
          <input type="hidden" name="op" value="note"><input type="hidden" name="back" value="<?php echo e($self); ?>">
          <textarea class="form-control mb-2" name="value" rows="3" maxlength="4000" placeholder="Add a note for your team (call outcome, next step…)" required></textarea>
          <button class="btn btn-cy btn-sm" type="submit"><i class="bi bi-plus-lg"></i> Add note</button>
        </form>
        <?php if (!$notes): ?>
          <p class="text-muted small mb-0">No notes yet.</p>
        <?php else: foreach ($notes as $n): ?>
          <div class="note-item">
            <div><?php echo nl2br(e((string)$n['note'])); ?></div>
            <small><?php echo e($n['admin_name'] ?? 'Admin'); ?> &middot; <?php echo e(date('j M Y, g:i a', strtotime((string)$n['created_at']))); ?></small>
          </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>
<?php admin_footer(); ?>
