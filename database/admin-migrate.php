<?php
/**
 * Club Yatri - Admin panel database migration (portable: MySQL 8 and MariaDB).
 *
 * Idempotent and non-destructive: it only creates missing tables and adds missing columns/indexes.
 * It never drops, renames or deletes anything, and existing rows are kept.
 *
 *   php database/admin-migrate.php                 apply (CLI)
 *   require it and call admin_migrate($pdo)        apply (from code)
 *
 * Column changes are guarded with information_schema checks because
 * "ADD COLUMN IF NOT EXISTS" is not supported by MySQL 8.
 */

declare(strict_types=1);

if (!function_exists('am_has_table')) {
    function am_has_table(PDO $pdo, string $table): bool
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
        $st->execute([$table]);
        return (int)$st->fetchColumn() > 0;
    }

    function am_has_column(PDO $pdo, string $table, string $column): bool
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $st->execute([$table, $column]);
        return (int)$st->fetchColumn() > 0;
    }

    function am_has_index(PDO $pdo, string $table, string $index): bool
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?');
        $st->execute([$table, $index]);
        return (int)$st->fetchColumn() > 0;
    }

    function am_has_constraint(PDO $pdo, string $table, string $name): bool
    {
        $st = $pdo->prepare('SELECT COUNT(*) FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ?');
        $st->execute([$table, $name]);
        return (int)$st->fetchColumn() > 0;
    }

    function am_is_auto_increment(PDO $pdo, string $table, string $column): bool
    {
        $st = $pdo->prepare('SELECT extra FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $st->execute([$table, $column]);
        return str_contains(strtolower((string)$st->fetchColumn()), 'auto_increment');
    }

    function am_column_type(PDO $pdo, string $table, string $column): string
    {
        $st = $pdo->prepare('SELECT column_type FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
        $st->execute([$table, $column]);
        return (string)$st->fetchColumn();
    }
}

/**
 * Apply the admin migration. Returns a list of what was done (empty list = already up to date).
 *
 * @return string[]
 */
function admin_migrate(PDO $pdo): array
{
    $done = [];
    $table = static function (string $name, string $ddl) use ($pdo, &$done): void {
        if (!am_has_table($pdo, $name)) {
            $pdo->exec($ddl);
            $done[] = "created table $name";
        }
    };
    $column = static function (string $tbl, string $col, string $def) use ($pdo, &$done): void {
        if (!am_has_column($pdo, $tbl, $col)) {
            $pdo->exec("ALTER TABLE `$tbl` ADD COLUMN `$col` $def");
            $done[] = "added column $tbl.$col";
        }
    };
    $index = static function (string $tbl, string $idx, string $cols) use ($pdo, &$done): void {
        if (!am_has_index($pdo, $tbl, $idx)) {
            $pdo->exec("ALTER TABLE `$tbl` ADD INDEX `$idx` ($cols)");
            $done[] = "added index $tbl.$idx";
        }
    };

    $engine = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    // ---- Authentication --------------------------------------------------------------
    $table('admin_users', "CREATE TABLE `admin_users` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(120) NOT NULL,
        `email` VARCHAR(190) NOT NULL,
        `password_hash` VARCHAR(255) NOT NULL,
        `role` VARCHAR(32) NOT NULL DEFAULT 'super_admin',
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `last_login_at` DATETIME NULL DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_admin_users_email` (`email`)
    ) $engine");

    $table('admin_login_attempts', "CREATE TABLE `admin_login_attempts` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `ip` VARCHAR(45) NOT NULL,
        `email` VARCHAR(190) NOT NULL DEFAULT '',
        `succeeded` TINYINT(1) NOT NULL DEFAULT 0,
        `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_login_attempts_ip` (`ip`, `attempted_at`),
        KEY `idx_login_attempts_email` (`email`, `attempted_at`)
    ) $engine");

    // ---- Public form rate limiting ---------------------------------------------------
    $table('form_submissions', "CREATE TABLE `form_submissions` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `ip` VARCHAR(45) NOT NULL,
        `form_key` VARCHAR(40) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_form_submissions_ip` (`ip`, `created_at`)
    ) $engine");

    // ---- Leads: extend inquiries (the existing leads table) --------------------------
    $column('inquiries', 'source', 'VARCHAR(40) NULL DEFAULT NULL');
    $column('inquiries', 'subject', 'VARCHAR(190) NULL DEFAULT NULL');
    $column('inquiries', 'extra', 'TEXT NULL');
    $column('inquiries', 'is_read', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('inquiries', 'is_archived', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('inquiries', 'is_spam', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('inquiries', 'follow_up_at', 'DATETIME NULL DEFAULT NULL');
    $column('inquiries', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');

    // Lead statuses: new / contacted / follow_up / converted / lost (existing 'closed' is kept for old rows)
    if (!str_contains(am_column_type($pdo, 'inquiries', 'status'), 'follow_up')) {
        $pdo->exec("ALTER TABLE `inquiries` MODIFY COLUMN `status` ENUM('new','contacted','follow_up','converted','lost','closed') NULL DEFAULT 'new'");
        $done[] = 'extended inquiries.status values';
    }

    // Give existing rows a source label derived from their old type
    $n = $pdo->exec("UPDATE `inquiries` SET `source` = CASE `type`
            WHEN 'callback_request' THEN 'callback'
            WHEN 'pass_inquiry' THEN 'pass_enquiry'
            ELSE `type` END
        WHERE `source` IS NULL");
    if ($n) {
        $done[] = "labelled $n existing inquiries with a source";
    }

    $index('inquiries', 'idx_inquiries_created', '`created_at`');
    $index('inquiries', 'idx_inquiries_state', '`is_archived`, `status`');

    // ---- Leads: state for booking requests (bookings itself stays untouched) ---------
    $table('booking_lead_state', "CREATE TABLE `booking_lead_state` (
        `booking_id` INT UNSIGNED NOT NULL,
        `lead_status` ENUM('new','contacted','follow_up','converted','lost') NOT NULL DEFAULT 'new',
        `is_read` TINYINT(1) NOT NULL DEFAULT 0,
        `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
        `is_spam` TINYINT(1) NOT NULL DEFAULT 0,
        `follow_up_at` DATETIME NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`booking_id`),
        CONSTRAINT `fk_booking_lead_state_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
    ) $engine");

    // ---- Leads: internal notes (for both inquiries and bookings) ---------------------
    $table('lead_notes', "CREATE TABLE `lead_notes` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `lead_type` ENUM('inquiry','booking') NOT NULL,
        `lead_id` INT UNSIGNED NOT NULL,
        `admin_id` BIGINT UNSIGNED NULL DEFAULT NULL,
        `note` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_lead_notes_lead` (`lead_type`, `lead_id`)
    ) $engine");

    // ---- New rows need real ids ----------------------------------------------------------
    // hotels, destinations and itineraries were imported with explicit ids and have no AUTO_INCREMENT,
    // so a row added from the admin would get id 0. Add it (type and values are unchanged).
    foreach (['hotels', 'destinations', 'itineraries'] as $tbl) {
        if (!am_is_auto_increment($pdo, $tbl, 'id')) {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $pdo->exec("ALTER TABLE `$tbl` MODIFY COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            $done[] = "made $tbl.id AUTO_INCREMENT";
        }
    }

    // =================================================================================
    // Content modules: Hotels, Destinations, Itineraries
    // =================================================================================
    $fk = static function (string $tbl, string $name, string $ddl) use ($pdo, &$done): void {
        if (!am_has_constraint($pdo, $tbl, $name)) {
            $pdo->exec("ALTER TABLE `$tbl` ADD CONSTRAINT `$name` $ddl");
            $done[] = "added foreign key $tbl.$name";
        }
    };

    // ---- Hotels --------------------------------------------------------------------
    $column('hotels', 'featured', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('hotels', 'display_order', 'INT NOT NULL DEFAULT 0');
    $column('hotels', 'short_description', 'VARCHAR(500) NULL DEFAULT NULL');
    $column('hotels', 'state', 'VARCHAR(80) NULL DEFAULT NULL');
    $column('hotels', 'city', 'VARCHAR(80) NULL DEFAULT NULL');
    $column('hotels', 'latitude', 'DECIMAL(9,6) NULL DEFAULT NULL');
    $column('hotels', 'longitude', 'DECIMAL(9,6) NULL DEFAULT NULL');
    $column('hotels', 'meta_title', 'VARCHAR(255) NULL DEFAULT NULL');
    $column('hotels', 'meta_description', 'VARCHAR(500) NULL DEFAULT NULL');
    $column('hotels', 'destination_id', 'INT UNSIGNED NULL DEFAULT NULL');
    $column('hotels', 'is_archived', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('hotels', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    $index('hotels', 'idx_hotels_destination', '`destination_id`');
    $index('hotels', 'idx_hotels_featured', '`featured`, `is_published`');
    $column('hotel_images', 'alt_text', 'VARCHAR(190) NULL DEFAULT NULL');

    // ---- Destinations ----------------------------------------------------------------
    $column('destinations', 'display_order', 'INT NOT NULL DEFAULT 0');
    $column('destinations', 'is_archived', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('destinations', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');

    // Link hotels to destinations by id (they were matched by name text before; the name is kept in sync)
    $n = $pdo->exec('UPDATE hotels h JOIN destinations d ON d.name = h.location_name SET h.destination_id = d.id WHERE h.destination_id IS NULL');
    if ($n) {
        $done[] = "linked $n hotels to their destination";
    }
    $fk('hotels', 'fk_hotels_destination', 'FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL');

    $table('destination_images', "CREATE TABLE `destination_images` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `destination_id` INT UNSIGNED NOT NULL,
        `path` VARCHAR(255) NOT NULL,
        `alt_text` VARCHAR(190) NULL DEFAULT NULL,
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `idx_destination_images` (`destination_id`, `sort_order`),
        CONSTRAINT `fk_destination_images_dest` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
    ) $engine");

    // ---- Itineraries -----------------------------------------------------------------
    $column('itineraries', 'display_order', 'INT NOT NULL DEFAULT 0');
    $column('itineraries', 'meta_title', 'VARCHAR(255) NULL DEFAULT NULL');
    $column('itineraries', 'meta_description', 'VARCHAR(500) NULL DEFAULT NULL');
    $column('itineraries', 'exclusions', 'TEXT NULL');
    $column('itineraries', 'activities', 'TEXT NULL');
    $column('itineraries', 'important_info', 'TEXT NULL');
    $column('itineraries', 'is_archived', 'TINYINT(1) NOT NULL DEFAULT 0');
    $column('itineraries', 'updated_at', 'TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP');
    $column('itinerary_days', 'image_path', 'VARCHAR(255) NULL DEFAULT NULL');
    $column('itinerary_images', 'alt_text', 'VARCHAR(190) NULL DEFAULT NULL');

    $table('itinerary_destinations', "CREATE TABLE `itinerary_destinations` (
        `itinerary_id` INT UNSIGNED NOT NULL,
        `destination_id` INT UNSIGNED NOT NULL,
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`itinerary_id`, `destination_id`),
        KEY `idx_itinerary_destinations_dest` (`destination_id`),
        CONSTRAINT `fk_itin_dest_itin` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_itin_dest_dest` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE
    ) $engine");
    $table('itinerary_hotels', "CREATE TABLE `itinerary_hotels` (
        `itinerary_id` INT UNSIGNED NOT NULL,
        `hotel_id` INT UNSIGNED NOT NULL,
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`itinerary_id`, `hotel_id`),
        KEY `idx_itinerary_hotels_hotel` (`hotel_id`),
        CONSTRAINT `fk_itin_hotel_itin` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_itin_hotel_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
    ) $engine");
    // The existing single destination of each itinerary becomes its first linked destination
    $n = $pdo->exec('INSERT IGNORE INTO itinerary_destinations (itinerary_id, destination_id, sort_order)
                     SELECT i.id, d.id, 0 FROM itineraries i JOIN destinations d ON d.slug = i.destination_slug');
    if ($n) {
        $done[] = "linked $n itineraries to their destination";
    }

    // =================================================================================
    // Passes, rooms, testimonials, settings
    // =================================================================================
    $table('passes', "CREATE TABLE `passes` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slug` VARCHAR(80) NOT NULL,
        `name` VARCHAR(190) NOT NULL,
        `short_name` VARCHAR(120) NOT NULL DEFAULT '',
        `category` VARCHAR(80) NOT NULL DEFAULT '',
        `duration_years` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        `tenure_label` VARCHAR(80) NOT NULL DEFAULT '',
        `display_price` VARCHAR(80) NULL DEFAULT NULL,
        `short_description` TEXT NULL,
        `full_description` TEXT NULL,
        `annual_nights` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `annual_points` INT UNSIGNED NULL DEFAULT NULL,
        `banking_years` VARCHAR(40) NULL DEFAULT NULL,
        `resorts_access` VARCHAR(190) NULL DEFAULT NULL,
        `dining_discount` VARCHAR(60) NULL DEFAULT NULL,
        `concierge` VARCHAR(120) NULL DEFAULT NULL,
        `advance_booking` VARCHAR(190) NULL DEFAULT NULL,
        `image_path` VARCHAR(255) NULL DEFAULT NULL,
        `image_alt` VARCHAR(190) NULL DEFAULT NULL,
        `terms` TEXT NULL,
        `featured` TINYINT(1) NOT NULL DEFAULT 0,
        `display_order` INT NOT NULL DEFAULT 0,
        `meta_title` VARCHAR(255) NULL DEFAULT NULL,
        `meta_description` VARCHAR(500) NULL DEFAULT NULL,
        `is_published` TINYINT(1) NOT NULL DEFAULT 1,
        `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_passes_slug` (`slug`)
    ) $engine");
    $table('pass_benefits', "CREATE TABLE `pass_benefits` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `pass_id` INT UNSIGNED NOT NULL,
        `title` VARCHAR(190) NOT NULL,
        `description` TEXT NULL,
        `icon` VARCHAR(40) NOT NULL DEFAULT 'check-circle',
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `idx_pass_benefits` (`pass_id`, `sort_order`),
        CONSTRAINT `fk_pass_benefits_pass` FOREIGN KEY (`pass_id`) REFERENCES `passes` (`id`) ON DELETE CASCADE
    ) $engine");
    $table('pass_seasons', "CREATE TABLE `pass_seasons` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slug` VARCHAR(40) NOT NULL,
        `name` VARCHAR(120) NOT NULL,
        `season_label` VARCHAR(120) NOT NULL DEFAULT '',
        `price_display` VARCHAR(60) NOT NULL DEFAULT '',
        `access_text` TEXT NULL,
        `experience_text` TEXT NULL,
        `blackout_text` VARCHAR(255) NULL DEFAULT NULL,
        `color` VARCHAR(9) NOT NULL DEFAULT '#EA2224',
        `image_path` VARCHAR(255) NULL DEFAULT NULL,
        `image_alt` VARCHAR(190) NULL DEFAULT NULL,
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        `is_published` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_pass_seasons_slug` (`slug`)
    ) $engine");

    $table('room_categories', "CREATE TABLE `room_categories` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `slug` VARCHAR(120) NOT NULL,
        `title` VARCHAR(190) NOT NULL,
        `short_description` VARCHAR(500) NULL DEFAULT NULL,
        `description` TEXT NULL,
        `area` VARCHAR(80) NULL DEFAULT NULL,
        `bed_type` VARCHAR(120) NULL DEFAULT NULL,
        `capacity` VARCHAR(120) NOT NULL DEFAULT '',
        `max_adults` TINYINT UNSIGNED NOT NULL DEFAULT 0,
        `max_children` TINYINT UNSIGNED NOT NULL DEFAULT 0,
        `points_night` INT UNSIGNED NULL DEFAULT NULL,
        `image_path` VARCHAR(500) NULL DEFAULT NULL,
        `featured` TINYINT(1) NOT NULL DEFAULT 0,
        `display_order` INT NOT NULL DEFAULT 0,
        `is_published` TINYINT(1) NOT NULL DEFAULT 1,
        `is_archived` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_room_categories_slug` (`slug`)
    ) $engine");
    $table('room_images', "CREATE TABLE `room_images` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `room_id` INT UNSIGNED NOT NULL,
        `path` VARCHAR(500) NOT NULL,
        `alt_text` VARCHAR(190) NULL DEFAULT NULL,
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `idx_room_images` (`room_id`, `sort_order`),
        CONSTRAINT `fk_room_images_room` FOREIGN KEY (`room_id`) REFERENCES `room_categories` (`id`) ON DELETE CASCADE
    ) $engine");
    $table('room_amenities', "CREATE TABLE `room_amenities` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `room_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(120) NOT NULL,
        `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`),
        KEY `idx_room_amenities` (`room_id`, `sort_order`),
        CONSTRAINT `fk_room_amenities_room` FOREIGN KEY (`room_id`) REFERENCES `room_categories` (`id`) ON DELETE CASCADE
    ) $engine");

    $table('testimonials', "CREATE TABLE `testimonials` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `type` ENUM('text','video') NOT NULL DEFAULT 'text',
        `name` VARCHAR(160) NOT NULL,
        `location` VARCHAR(120) NULL DEFAULT NULL,
        `detail` VARCHAR(190) NULL DEFAULT NULL,
        `profile_image` VARCHAR(255) NULL DEFAULT NULL,
        `rating` TINYINT UNSIGNED NULL DEFAULT NULL,
        `body` TEXT NULL,
        `video_title` VARCHAR(190) NULL DEFAULT NULL,
        `video_source` ENUM('youtube','vimeo','upload') NULL DEFAULT NULL,
        `video_url` VARCHAR(500) NULL DEFAULT NULL,
        `video_ref` VARCHAR(40) NULL DEFAULT NULL,
        `video_path` VARCHAR(255) NULL DEFAULT NULL,
        `thumbnail_path` VARCHAR(255) NULL DEFAULT NULL,
        `description` TEXT NULL,
        `featured` TINYINT(1) NOT NULL DEFAULT 0,
        `display_order` INT NOT NULL DEFAULT 0,
        `is_published` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_testimonials_type` (`type`, `is_published`)
    ) $engine");

    $table('site_settings', "CREATE TABLE `site_settings` (
        `setting_key` VARCHAR(80) NOT NULL,
        `setting_value` TEXT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`setting_key`)
    ) $engine");

    // Start with the content the website already shows, so nothing changes until it is edited in the admin
    foreach (admin_seed_content($pdo) as $line) {
        $done[] = $line;
    }

    return $done;
}

/** Copy the built-in passes, seasonal passes, rooms, testimonials and pass terms into empty tables. @return string[] */
function admin_seed_content(PDO $pdo): array
{
    $done = [];
    $root = dirname(__DIR__);
    $count = static fn(string $t): int => (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    // data files return their built-in arrays (these tables are empty, so they do not read the database)
    $load = static function (string $file, string $var) use ($root) {
        return (static function () use ($root, $file, $var) {
            require $root . '/data/' . $file;
            return $$var;
        })();
    };

    if ($count('passes') === 0) {
        $plans = $load('subscriptions.php', 'subscriptionPlansStatic');
        $i = 0;
        foreach ($plans as $slug => $p) {
            $i++;
            preg_match('/(\d+)/', (string)$p['title'], $m);
            $years = (int)($m[1] ?? 1);
            $pdo->prepare('INSERT INTO passes (slug, name, short_name, category, duration_years, tenure_label, short_description, annual_nights, annual_points,
                           banking_years, resorts_access, dining_discount, concierge, image_path, featured, display_order, is_published)
                           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)')
                ->execute([$slug, $p['title'], $p['short_title'], $p['badge'], $years, $p['tenure'], $p['description'], $p['annual_nights'], $p['points_annual'],
                    (string)$p['banking_years'], $p['resorts_access'], $p['dining_discount'], $p['concierge'], 'images/passes/pass-' . $years . '-year.jpg',
                    $p['featured'] ? 1 : 0, $i]);
            $pid = (int)$pdo->lastInsertId();
            foreach ($p['highlight_features'] as $n => $feat) {
                $pdo->prepare('INSERT INTO pass_benefits (pass_id, title, icon, sort_order) VALUES (?,?,?,?)')->execute([$pid, $feat, 'check-circle', $n + 1]);
            }
        }
        $done[] = "seeded $i passes with their benefits";
    }

    if ($count('pass_seasons') === 0) {
        $seasons = [
            ['green', 'Yatri Green Pass', 'Off-Peak / Monsoon Season', '₹6,66,450', 'Valid exclusively for off-peak and monsoon season travel with zero seasonal surcharges.',
                'Full Access • Serene wellness retreats and quiet nature getaways with zero seasonal surcharges.', 'Christmas Week & New Year Week Excluded.', '#10B981'],
            ['blue', 'Yatri Blue Pass', 'Mid Season', '₹8,33,100', 'Valid for mid-season travel, ensuring smooth reservations away from peak crowds.',
                'Full Access • Pleasant autumn and spring weeks across heritage palaces and coastal escapes.', 'None.', '#2F7BF5'],
            ['yellow', 'Yatri Yellow Pass', 'Peak Season', '₹13,33,050', 'Valid for peak-season travel, offering priority privileges and guaranteed holiday access during high-demand periods.',
                'Priority Access • Extended holiday weekends and prime summer family vacations.', 'None.', '#FFB300'],
            ['red', 'Yatri Red Pass', 'Super Peak Season', '₹14,99,850', 'Valid for super peak season travel, delivering ultimate year-round flexibility and elite holiday placements.',
                'Guaranteed Priority • Diwali, Christmas, New Year celebrations, and premium snow weeks.', 'None (Guaranteed priority access).', '#EA2224'],
        ];
        foreach ($seasons as $n => $r) {
            $pdo->prepare('INSERT INTO pass_seasons (slug, name, season_label, price_display, access_text, experience_text, blackout_text, color, image_path, sort_order)
                           VALUES (?,?,?,?,?,?,?,?,?,?)')->execute([$r[0], $r[1], $r[2], $r[3], $r[4], $r[5], $r[6], $r[7], 'images/passes/pc-' . $r[0] . '.jpg', $n + 1]);
        }
        $done[] = 'seeded 4 seasonal passes';
    }

    $has = static fn(string $k): bool => (int)$pdo->query("SELECT COUNT(*) FROM site_settings WHERE setting_key = " . $pdo->quote($k))->fetchColumn() > 0;
    if (!$has('pass_core_terms')) {
        $terms = [
            ['Exclusive Network Access', 'Stays, reservations, and pass privileges are strictly restricted and applicable only to properties owned, managed, or directly tied-up within the Club Yatri network, governed by authorized management control.'],
            ['Validity & Usage', 'Valid for 25 Years with a total cumulative allocation of 150 Nights (6 Nights / 7 Days per year; unused nights lapse annually).'],
            ['Inflation-Free Stays', 'Covers accommodation costs exclusively (taxes, food, beverages, and incidentals are excluded).'],
            ['Inheritance Policy', 'Fully transferrable as a family asset once per lifetime upon official notification.'],
            ['Advanced Priority Booking', 'Exclusive 60-day advance booking window ahead of the general public.'],
            ['Points Banking', 'Lifetime points banking enabled, allowing structured carryover for a limited validity period.'],
            ['Annual Compliance', 'Timely payment of the Annual Subscription & Concierge Fee (ACF) is mandatory to maintain active pass benefits and booking rights.'],
        ];
        $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)')->execute(['pass_core_terms', json_encode($terms, JSON_UNESCAPED_UNICODE)]);
        $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)')->execute(['pass_checklist', "100% Inflation-Free Stays\nFully Inheritable Family Asset\nExclusive Network Privileges"]);
        $done[] = 'seeded the pass core terms';
    }

    if ($count('room_categories') === 0) {
        $rooms = $load('rooms.php', 'roomCategoriesStatic');
        $i = 0;
        foreach ($rooms as $slug => $r) {
            $i++;
            $pdo->prepare('INSERT INTO room_categories (slug, title, short_description, description, area, bed_type, capacity, points_night, image_path, display_order, is_published)
                           VALUES (?,?,?,?,?,?,?,?,?,?,1)')
                ->execute([$slug, $r['title'], $r['vibe'] ?? null, $r['description'] ?? null, $r['area'] ?? null, $r['bed_type'] ?? null, $r['capacity'] ?? '',
                    $r['points_night'] ?? null, $r['image'] ?? null, $i]);
            $rid = (int)$pdo->lastInsertId();
            foreach (($r['amenities'] ?? []) as $n => $a) {
                $pdo->prepare('INSERT INTO room_amenities (room_id, name, sort_order) VALUES (?,?,?)')->execute([$rid, $a, $n + 1]);
            }
        }
        $done[] = "seeded $i room categories";
    }

    if ($count('testimonials') === 0) {
        $list = $load('testimonials.php', 'testimonialsListStatic');
        foreach ($list as $n => $r) {
            $pdo->prepare("INSERT INTO testimonials (type, name, location, detail, rating, body, display_order, is_published) VALUES ('text',?,?,?,?,?,?,1)")
                ->execute([$r['author'], $r['city'] ?? null, $r['tier'] ?? null, $r['rating'] ?? 5, $r['quote'], $n + 1]);
        }
        $done[] = 'seeded ' . count($list) . ' written testimonials';
    }

    return $done;
}

/** Create or update an admin user (password stored only as a hash). */
function admin_seed_user(PDO $pdo, string $name, string $email, string $password): string
{
    $email = strtolower(trim($email));
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $st = $pdo->prepare('SELECT id FROM admin_users WHERE email = ?');
    $st->execute([$email]);
    if ($id = $st->fetchColumn()) {
        $pdo->prepare('UPDATE admin_users SET name = ?, password_hash = ?, is_active = 1 WHERE id = ?')->execute([$name, $hash, $id]);
        return "updated admin $email";
    }
    $pdo->prepare('INSERT INTO admin_users (name, email, password_hash, role) VALUES (?,?,?,?)')->execute([$name, $email, $hash, 'super_admin']);
    return "created admin $email";
}

// ---- CLI entry point ------------------------------------------------------------------
if (PHP_SAPI === 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    require_once __DIR__ . '/../includes/db.php';
    $pdo = db();
    if (!$pdo) {
        fwrite(STDERR, "Database connection failed\n");
        exit(1);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $log = admin_migrate($pdo);
    echo $log ? implode("\n", $log) . "\n" : "Already up to date.\n";

    // Optional: create the Super Admin from environment variables (the password is never written to a file)
    $email = getenv('ADMIN_EMAIL');
    $pass = getenv('ADMIN_PASSWORD');
    if ($email && $pass) {
        echo admin_seed_user($pdo, getenv('ADMIN_NAME') ?: 'Super Admin', $email, $pass) . "\n";
    }
}
