"""
Club Yatri - one-off hotel import.

Reads export/hotels.csv + export/hotel-images.zip and produces:
  * assets/images/hotels/<slug>/<n>.webp   resized web copies of each photo
  * database/hotels.sql                    CREATE TABLE + INSERTs (safe to re-run)

Usage (from the project root):  python database/tools/import_hotels.py
"""
import csv
import io
import re
import sys
import zipfile
from pathlib import Path

from PIL import Image, ImageOps

ROOT = Path(__file__).resolve().parents[2]
CSV_PATH = ROOT / 'export' / 'hotels.csv'
ZIP_PATH = ROOT / 'export' / 'hotel-images.zip'
IMG_DIR = ROOT / 'assets' / 'images' / 'hotels'
SQL_PATH = ROOT / 'database' / 'hotels.sql'

MAX_EDGE = 1000
WEBP_QUALITY = 74

INVALID = re.compile(r'[<>:"/\\|?*]')
# Sentences that carry someone else's contact details are dropped from descriptions.
CONTACT = re.compile(r'1800|travel desk|@|\b\d{5}\s?\d{5}\b|https?://|www\.', re.I)


def clean_description(text: str) -> str:
    text = text.replace('\r\n', '\n').strip()
    paragraphs = []
    for para in re.split(r'\n\s*\n', text):
        sentences = re.split(r'(?<=[.!?])\s+', para.strip())
        kept = [s for s in sentences if not CONTACT.search(s)]
        if kept:
            paragraphs.append(' '.join(kept).strip())
    return '\n\n'.join(paragraphs)


def sql_str(value) -> str:
    if value is None or value == '':
        return 'NULL'
    s = str(value)
    s = s.replace('\\', '\\\\').replace("'", "\\'").replace('\n', '\\n').replace('\r', '').replace('\0', '')
    return "'" + s + "'"


def find_folder(names_by_folder, row):
    name, code = row['name'].strip(), row['hotelCode'].strip()
    san = INVALID.sub('', name)
    san = re.sub(r'\s+', ' ', san)
    candidates = []
    for base in (name, INVALID.sub('', name), san):
        candidates += [f'{base} ({code})', base]
    for cand in candidates:
        if cand in names_by_folder:
            return cand
    # Last resort: whitespace-insensitive comparison
    squash = lambda s: re.sub(r'\s+', '', s).lower()
    want = {squash(c): c for c in candidates}
    for folder in names_by_folder:
        if squash(folder) in want:
            return folder
    return None


def main():
    rows = list(csv.DictReader(open(CSV_PATH, encoding='utf-8-sig', newline='')))
    z = zipfile.ZipFile(ZIP_PATH)
    by_folder = {}
    for n in z.namelist():
        if n.endswith('/'):
            continue
        folder, _, fn = n.rpartition('/')
        by_folder.setdefault(folder, {})[fn] = n

    IMG_DIR.mkdir(parents=True, exist_ok=True)
    hotel_sql, image_sql = [], []
    missing_folder, missing_file, failed = [], [], []
    total_bytes = 0

    for idx, r in enumerate(rows, start=1):
        slug = r['slug'].strip()
        gallery = [g.strip() for g in r['gallery'].split(';') if g.strip()]
        saved = []
        if gallery:
            folder = find_folder(by_folder, r)
            if folder is None:
                missing_folder.append(r['name'])
            else:
                out = IMG_DIR / slug
                out.mkdir(exist_ok=True)
                for order, g in enumerate(gallery, start=1):
                    fn = g.rsplit('/', 1)[-1]
                    member = by_folder[folder].get(fn)
                    if member is None:
                        missing_file.append(f'{r["name"]}/{fn}')
                        continue
                    dest = out / f'{order}.webp'
                    try:
                        if not dest.exists():
                            with Image.open(io.BytesIO(z.read(member))) as im:
                                im = ImageOps.exif_transpose(im)
                                if im.mode not in ('RGB', 'L'):
                                    im = im.convert('RGBA') if 'A' in im.mode else im.convert('RGB')
                                if im.mode == 'RGBA':
                                    bg = Image.new('RGB', im.size, (255, 255, 255))
                                    bg.paste(im, mask=im.split()[-1])
                                    im = bg
                                im.thumbnail((MAX_EDGE, MAX_EDGE), Image.LANCZOS)
                                im.save(dest, 'WEBP', quality=WEBP_QUALITY, method=4)
                        total_bytes += dest.stat().st_size
                        saved.append(f'assets/images/hotels/{slug}/{order}.webp')
                    except Exception as exc:  # corrupt image etc.
                        failed.append(f'{r["name"]}/{fn}: {exc}')

        desc = clean_description(r['description'])
        hotel_sql.append('(' + ', '.join([
            str(idx), sql_str(r['hotelCode']), sql_str(slug), sql_str(r['name'].strip()),
            sql_str(r['locationName'].strip()), sql_str(r['country'].strip()),
            sql_str(r['locationType'].strip()), sql_str(r['hotelType'].strip()),
            sql_str(r['category'].strip()), sql_str(r['stayTypes'].strip()),
            sql_str(r['address'].strip()), sql_str(r['mapLink'].strip()), sql_str(desc),
            sql_str(saved[0] if saved else None),
            '1' if r['isPublished'].strip().lower() == 'true' else '0',
        ]) + ')')
        for order, path in enumerate(saved, start=1):
            image_sql.append(f'({idx}, {sql_str(path)}, {order})')

    cols = ('id, hotel_code, slug, name, location_name, country, location_type, hotel_type, category, '
            'stay_types, address, map_link, description, image_path, is_published')
    upd = ', '.join(f'{c}=VALUES({c})' for c in cols.split(', ')[1:])

    def chunks(items, n):
        for i in range(0, len(items), n):
            yield items[i:i + n]

    with open(SQL_PATH, 'w', encoding='utf-8', newline='\n') as f:
        f.write('-- Club Yatri hotel network import (generated by database/tools/import_hotels.py)\n')
        f.write('-- Safe to re-run. cPanel: select your database in phpMyAdmin, then Import this file.\n')
        f.write('SET NAMES utf8mb4;\n\n')
        f.write(HOTELS_DDL + '\n')
        for part in chunks(hotel_sql, 40):
            f.write(f'INSERT INTO `hotels` ({cols}) VALUES\n' + ',\n'.join(part) + f'\nON DUPLICATE KEY UPDATE {upd};\n\n')
        f.write('DELETE FROM `hotel_images`;\n')
        for part in chunks(image_sql, 200):
            f.write('INSERT INTO `hotel_images` (hotel_id, path, sort_order) VALUES\n' + ',\n'.join(part) + ';\n')

    print(f'hotels: {len(rows)}  images saved: {len(image_sql)}  size: {total_bytes / 1e6:.1f} MB')
    print('hotels with no matching image folder:', missing_folder)
    print('missing image files:', missing_file)
    print('failed images:', failed)


HOTELS_DDL = """CREATE TABLE IF NOT EXISTS `hotels` (
  `id` INT UNSIGNED NOT NULL PRIMARY KEY,
  `hotel_code` VARCHAR(16) NOT NULL UNIQUE,
  `slug` VARCHAR(190) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `location_name` VARCHAR(128) NOT NULL,
  `country` VARCHAR(64) NOT NULL,
  `location_type` ENUM('DOMESTIC', 'INTERNATIONAL') NOT NULL,
  `hotel_type` VARCHAR(32) NOT NULL DEFAULT 'HOTEL',
  `category` ENUM('INTERNAL', 'ASSOCIATED') NOT NULL DEFAULT 'ASSOCIATED',
  `stay_types` VARCHAR(255) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `map_link` TEXT DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_hotels_type` (`location_type`, `is_published`),
  KEY `idx_hotels_country` (`country`),
  KEY `idx_hotels_location` (`location_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `hotel_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `hotel_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  KEY `idx_hotel_images_hotel` (`hotel_id`, `sort_order`),
  CONSTRAINT `fk_hotel_images_hotel` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
"""

if __name__ == '__main__':
    sys.exit(main())
