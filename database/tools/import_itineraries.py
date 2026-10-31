"""
Club Yatri - one-off itineraries import.

Reads export/itineraries.csv + export/itinerary-images.zip and produces:
  * assets/images/itineraries/<slug>/<n>.webp    resized photos
  * database/migrations/004_itineraries.sql      tables + data (safe to re-run)

Usage (from the project root):  python database/tools/import_itineraries.py
"""
import csv
import io
import re
import sys
import zipfile
from pathlib import Path

from PIL import Image, ImageOps

sys.path.insert(0, str(Path(__file__).resolve().parent))
from import_hotels import sql_str  # noqa: E402

ROOT = Path(__file__).resolve().parents[2]
CSV_PATH = ROOT / 'export' / 'itineraries.csv'
DEST_CSV = ROOT / 'export' / 'destinations.csv'
ZIP_PATH = ROOT / 'export' / 'itinerary-images.zip'
IMG_DIR = ROOT / 'assets' / 'images' / 'itineraries'
SQL_PATH = ROOT / 'database' / 'migrations' / '004_itineraries.sql'

MAX_EDGE = 1600
WEBP_QUALITY = 76
INVALID = re.compile(r'[<>:"/\\|?*]')

# The CSV has no destination for this itinerary; it covers Srinagar, Gulmarg and Pahalgam.
DESTINATION_OVERRIDES = {'kashmir-paradise-valley': 'jammu-and-kashmir'}

DAY_RE = re.compile(r'^Day\s+(\d+):\s*(.+?)\s+-\s+(.+)$')

DDL = """CREATE TABLE IF NOT EXISTS `itineraries` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `slug` VARCHAR(160) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `badge_text` VARCHAR(120) DEFAULT NULL,
  `location_label` VARCHAR(255) DEFAULT NULL,
  `destination_slug` VARCHAR(160) DEFAULT NULL,
  `duration_days` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `duration_nights` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `category` VARCHAR(80) DEFAULT NULL,
  `location_type` ENUM('DOMESTIC', 'INTERNATIONAL') NOT NULL,
  `best_time` VARCHAR(255) DEFAULT NULL,
  `summary` TEXT DEFAULT NULL,
  `overview` TEXT DEFAULT NULL,
  `highlights` TEXT DEFAULT NULL,
  `inclusions` TEXT DEFAULT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `hero_image` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_itineraries_type` (`location_type`, `is_published`),
  KEY `idx_itineraries_featured` (`featured`, `is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `itinerary_days` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `itinerary_id` INT UNSIGNED NOT NULL,
  `day_number` TINYINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  KEY `idx_itinerary_days` (`itinerary_id`, `day_number`),
  CONSTRAINT `fk_itinerary_days` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `itinerary_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `itinerary_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  KEY `idx_itinerary_images` (`itinerary_id`, `sort_order`),
  CONSTRAINT `fk_itinerary_images` FOREIGN KEY (`itinerary_id`) REFERENCES `itineraries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
"""


def flag(v):
    return '1' if str(v).strip().lower() == 'true' else '0'


def clean_list(raw):
    return '; '.join(p.strip() for p in raw.split(';') if p.strip())


def main():
    rows = [r for r in csv.DictReader(open(CSV_PATH, encoding='utf-8-sig', newline='')) if r['slug'].strip()]
    dest_by_id = {r['id']: r['slug'] for r in csv.DictReader(open(DEST_CSV, encoding='utf-8-sig', newline='')) if r['slug']}

    z = zipfile.ZipFile(ZIP_PATH)
    folders = {}
    for n in z.namelist():
        if not n.endswith('/'):
            folder, _, fn = n.rpartition('/')
            folders.setdefault(folder, {})[fn] = n

    IMG_DIR.mkdir(parents=True, exist_ok=True)
    it_sql, day_sql, img_sql = [], [], []
    problems, total = [], 0

    for idx, r in enumerate(rows, start=1):
        slug, title = r['slug'].strip(), r['title'].strip()

        # ---- photos: hero first, then the gallery, named 1..n in the zip folder of the same title
        folder = next((f for f in (title, INVALID.sub('', title)) if f in folders), None)
        names = [p.strip().rsplit('/', 1)[-1] for p in [r['heroImageUrl']] + r['gallery'].split(';') if p.strip()]
        saved = []
        if folder is None:
            problems.append(f'{slug}: no image folder')
        else:
            out = IMG_DIR / slug
            out.mkdir(exist_ok=True)
            for order, fn in enumerate(names, start=1):
                member = folders[folder].get(fn)
                if member is None:
                    problems.append(f'{slug}: missing {fn}')
                    continue
                dest = out / f'{order}.webp'
                if not dest.exists():
                    with Image.open(io.BytesIO(z.read(member))) as im:
                        im = ImageOps.exif_transpose(im).convert('RGB')
                        im.thumbnail((MAX_EDGE, MAX_EDGE), Image.LANCZOS)
                        im.save(dest, 'WEBP', quality=WEBP_QUALITY, method=4)
                total += dest.stat().st_size
                saved.append(f'assets/images/itineraries/{slug}/{order}.webp')

        # ---- day-by-day plan: "Day N: Title - Description" per line
        days = [l.strip() for l in r['dayWisePlan'].replace('\r', '').split('\n') if l.strip()]
        for line in days:
            m = DAY_RE.match(line)
            if not m:
                problems.append(f'{slug}: cannot parse "{line[:50]}"')
                continue
            day_sql.append(f'({idx}, {m.group(1)}, {sql_str(m.group(2))}, {sql_str(m.group(3))})')

        overview = r['overview'].strip()
        summary = r['summary'].strip()
        dest_slug = DESTINATION_OVERRIDES.get(slug) or dest_by_id.get(r['destinationId'].strip())
        it_sql.append('(' + ', '.join([
            str(idx), sql_str(slug), sql_str(title), sql_str(r['badgeText'].strip()), sql_str(r['locationLabel'].strip()),
            sql_str(dest_slug), r['durationDays'].strip() or '1', r['durationNights'].strip() or '0',
            sql_str(r['category'].strip()), sql_str(r['locationType'].strip()), sql_str(r['bestTime'].strip()),
            sql_str(summary), sql_str(overview if overview != summary else None),
            sql_str(clean_list(r['highlights'])), sql_str(clean_list(r['inclusions'])),
            flag(r['featured']), flag(r['isPublished']), sql_str(saved[0] if saved else None),
        ]) + ')')
        for order, path in enumerate(saved, start=1):
            img_sql.append(f'({idx}, {sql_str(path)}, {order})')

    cols = ('id, slug, title, badge_text, location_label, destination_slug, duration_days, duration_nights, category, '
            'location_type, best_time, summary, overview, highlights, inclusions, featured, is_published, hero_image')
    upd = ', '.join(f'{c}=VALUES({c})' for c in cols.split(', ')[1:])

    with open(SQL_PATH, 'w', encoding='utf-8', newline='\n') as f:
        f.write('-- Club Yatri itineraries import (generated by database/tools/import_itineraries.py)\n')
        f.write('-- Safe to re-run. Applied automatically on deploy by deploy-migrate.php.\n')
        f.write('SET NAMES utf8mb4;\n\n' + DDL + '\n')
        f.write(f'INSERT INTO `itineraries` ({cols}) VALUES\n' + ',\n'.join(it_sql) + f'\nON DUPLICATE KEY UPDATE {upd};\n\n')
        f.write('DELETE FROM `itinerary_days`;\n')
        f.write('INSERT INTO `itinerary_days` (itinerary_id, day_number, title, description) VALUES\n' + ',\n'.join(day_sql) + ';\n\n')
        f.write('DELETE FROM `itinerary_images`;\n')
        f.write('INSERT INTO `itinerary_images` (itinerary_id, path, sort_order) VALUES\n' + ',\n'.join(img_sql) + ';\n')

    print(f'itineraries: {len(rows)}  days: {len(day_sql)}  photos: {len(img_sql)}  size: {total / 1e6:.1f} MB')
    print('problems:', problems or 'none')


if __name__ == '__main__':
    main()
