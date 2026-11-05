<?php
/**
 * Club Yatri Admin - video upload helper (MP4 only, 5 MB limit).
 */

declare(strict_types=1);

const ADMIN_MAX_VIDEO_BYTES = 5 * 1024 * 1024;   // video testimonials only; images stay at 2 MB

/**
 * Validate and store one uploaded MP4 under assets/videos/<dir>/.
 *
 * @return array{ok:bool,path?:string,error?:string}
 */
function admin_save_video(array $file, string $dir, string $label = 'Video'): array
{
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'error' => ''];
    }
    $name = (string)($file['name'] ?? 'file');
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE || (int)($file['size'] ?? 0) > ADMIN_MAX_VIDEO_BYTES) {
        return ['ok' => false, 'error' => "$label \"$name\" is larger than 5 MB. Upload it to YouTube or Vimeo and paste the link instead."];
    }
    if ($err !== UPLOAD_ERR_OK || !is_uploaded_file((string)$file['tmp_name'])) {
        return ['ok' => false, 'error' => "$label \"$name\" could not be uploaded (error $err)."];
    }
    $mime = (string)(new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
    $head = (string)file_get_contents((string)$file['tmp_name'], false, null, 4, 8);
    if ($mime !== 'video/mp4' || !str_starts_with($head, 'ftyp')) {
        return ['ok' => false, 'error' => "$label \"$name\" must be an MP4 video."];
    }

    $dir = trim(str_replace('\\', '/', $dir), '/');
    $base = dirname(__DIR__, 2) . '/assets/videos';
    $abs = $base . '/' . $dir;
    if (!is_dir($abs) && !@mkdir($abs, 0755, true) && !is_dir($abs)) {
        return ['ok' => false, 'error' => 'The upload folder could not be created. Check folder permissions.'];
    }
    if (!is_file($base . '/.htaccess')) {
        @file_put_contents($base . '/.htaccess', "# Uploaded videos: never execute scripts from this folder\n<FilesMatch \"\\.(php|phtml|phar|php[0-9]|pl|py|cgi|sh)$\">\n  Require all denied\n</FilesMatch>\nOptions -Indexes\n");
    }
    $fname = 'u-' . date('ymd-His') . '-' . bin2hex(random_bytes(3)) . '.mp4';
    if (!move_uploaded_file((string)$file['tmp_name'], $abs . '/' . $fname)) {
        return ['ok' => false, 'error' => "$label \"$name\" could not be saved. Check folder permissions."];
    }
    @chmod($abs . '/' . $fname, 0644);
    return ['ok' => true, 'path' => 'assets/videos/' . $dir . '/' . $fname];
}
