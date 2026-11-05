<?php
/**
 * Club Yatri Admin - Leads: CSV export of the currently filtered list (opens in Excel).
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/leads_repo.php';

require_admin();

$data = leads_fetch(admin_pdo(), $_GET, 0, 0);

// Stop spreadsheet formula injection from user-supplied text
$cell = static function ($v): string {
    $v = (string)$v;
    return ($v !== '' && strpos("=+-@\t\r", $v[0]) !== false) ? "'" . $v : $v;
};

$filename = 'club-yatri-leads-' . date('Ymd-His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");   // UTF-8 BOM so Excel shows rupee signs and accents correctly
fputcsv($out, ['Reference', 'Type', 'Name', 'Phone', 'Email', 'Source', 'Subject', 'Status', 'Read', 'Follow-up', 'Received', 'Details'], ',', '"', '');
foreach ($data['rows'] as $r) {
    fputcsv($out, array_map($cell, [
        $r['ref'], $r['kind'] === 'booking' ? 'Booking request' : 'Enquiry', $r['name'], $r['phone'], $r['email'],
        lead_source_label((string)$r['source']), $r['subject'], lead_status_label((string)$r['status']),
        (int)$r['is_read'] ? 'Read' : 'Unread', $r['follow_up_at'] ?? '', $r['created_at'], $r['details'],
    ]), ',', '"', '');
}
fclose($out);
exit;
