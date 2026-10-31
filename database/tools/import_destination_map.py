"""
Club Yatri - destination map pins.

Turns real-world coordinates (latitude/longitude) into pixel positions on the globe artwork
(images/dest-globe.png, drawn at 727 x 479 px) and writes database/migrations/003_destination_map.sql.

The lon/lat -> artwork mapping is a smooth polynomial warp fitted to the dotted land shapes of the
artwork (database/tools/globe_warp.json), because the artwork is stylised rather than a true projection.
Places that fall outside the visible part of the artwork (the Americas) get no pin.

Usage (from the project root):  python database/tools/import_destination_map.py
"""
import csv
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from import_hotels import sql_str  # noqa: E402

ROOT = Path(__file__).resolve().parents[2]
CSV_PATH = ROOT / 'export' / 'destinations.csv'
WARP = json.loads((Path(__file__).resolve().parent / 'globe_warp.json').read_text())
SQL_PATH = ROOT / 'database' / 'migrations' / '003_destination_map.sql'

ART_W, ART_H = 727, 479

# slug: (latitude, longitude)
COORDS = {
    'agra': (27.18, 78.02), 'ahmedabad': (23.02, 72.57), 'ajmer': (26.45, 74.64), 'alleppey': (9.50, 76.33),
    'amritsar': (31.63, 74.87), 'auli': (30.53, 79.57), 'aurangabad': (19.88, 75.34), 'ayodhya': (26.80, 82.20),
    'bali': (-8.50, 115.20), 'banaras': (25.33, 83.00), 'bandipur': (11.67, 76.63), 'bangalore': (12.97, 77.59),
    'bangkok': (13.75, 100.50), 'bhimtal': (29.35, 79.56), 'bhopal': (23.26, 77.41), 'bhuj': (23.25, 69.67),
    'bhutan': (27.47, 89.64), 'bikaner': (28.02, 73.31), 'canary-islands': (28.29, -16.63), 'chandigarh': (30.73, 76.78),
    'chennai': (13.08, 80.27), 'cochin': (9.96, 76.24), 'coimbatore': (11.02, 76.96), 'coorg': (12.42, 75.74),
    'dalhousie': (32.54, 75.97), 'dehradun': (30.32, 78.03), 'dhanaulti': (30.42, 78.25), 'dharamshala': (32.22, 76.32),
    'dubai': (25.20, 55.27), 'gangtok': (27.33, 88.61), 'gaya': (24.80, 85.00), 'georgia': (41.72, 44.79),
    'goa': (15.50, 73.83), 'gurgaon': (28.46, 77.03), 'gwalior': (26.22, 78.18), 'haridwar': (29.95, 78.16),
    'hyderabad': (17.39, 78.49), 'indonesia': (-6.20, 106.85), 'indore': (22.72, 75.86), 'istanbul': (41.01, 28.98),
    'jaipur': (26.91, 75.79), 'jaisalmer': (26.92, 70.91), 'jammu-and-kashmir': (34.08, 74.80), 'jim-corbett': (29.53, 78.77),
    'jodhpur': (26.24, 73.02), 'kandaghat': (30.97, 77.12), 'kanha-national-park': (22.33, 80.63), 'kasauli': (30.90, 76.97),
    'katra': (32.99, 74.93), 'kausani': (29.84, 79.60), 'kerala': (10.85, 76.27), 'khandala': (18.77, 73.38),
    'kochi': (9.93, 76.27), 'kodaikanal': (10.24, 77.49), 'kolkata': (22.57, 88.36), 'kosi': (29.64, 79.62),
    'krabi': (8.09, 98.91), 'kufri': (31.10, 77.27), 'kumarakom': (9.62, 76.43), 'lansdowne': (29.84, 78.68),
    'las-vegas': (36.17, -115.14), 'lonavla': (18.75, 73.41), 'london': (51.51, -0.13), 'lucknow': (26.85, 80.95),
    'ludhiana': (30.90, 75.86), 'mahabaleshwar': (17.92, 73.66), 'malaysia': (3.14, 101.69), 'maldives': (4.18, 73.51),
    'manali': (32.24, 77.19), 'manesar': (28.35, 76.94), 'mathura': (27.49, 77.67), 'moga': (30.82, 75.17),
    'mohali': (30.70, 76.72), 'mumbai': (19.08, 72.88), 'munnar': (10.09, 77.06), 'mussoorie': (30.46, 78.07),
    'mysore': (12.30, 76.65), 'nagpur': (21.15, 79.09), 'nainital': (29.39, 79.45), 'nepal': (27.72, 85.32),
    'ooty': (11.41, 76.70), 'panchgani': (17.92, 73.82), 'panchkula': (30.69, 76.85), 'paris': (48.86, 2.35),
    'parwanoo': (30.84, 76.96), 'pattaya': (12.93, 100.88), 'phuket': (7.88, 98.39), 'pondicherry': (11.93, 79.83),
    'pune': (18.52, 73.86), 'puri': (19.81, 85.83), 'pushkar': (26.49, 74.55), 'ranthambore': (26.02, 76.50),
    'rishikesh': (30.09, 78.27), 'rome': (41.90, 12.50), 'shimla': (31.10, 77.17), 'shirdi': (19.77, 74.48),
    'singapore': (1.35, 103.82), 'spain': (40.42, -3.70), 'sri-lanka': (7.87, 80.77), 'surat': (21.17, 72.83),
    'switzerland': (46.82, 8.23), 'thekkady': (9.60, 77.17), 'thrissur': (10.53, 76.21), 'udaipur': (24.59, 73.71),
    'united-states-of-america': (39.80, -98.60), 'vadodara': (22.31, 73.18), 'varanasi': (25.30, 83.03),
    'visakhapatnam': (17.69, 83.22), 'vrindavan': (27.58, 77.70), 'zirakpur': (30.64, 76.82),
}


def warp(lon, lat):
    u, v = (lon - WARP['lon0']) / 50.0, (lat - WARP['lat0']) / 50.0
    t = [1, u, v, u * u, u * v, v * v, u ** 3, u * u * v, u * v * v, v ** 3]
    n = len(t)
    p = WARP['p']
    return sum(a * b for a, b in zip(t, p[:n])), sum(a * b for a, b in zip(t, p[n:2 * n]))


DDL = """CREATE TABLE IF NOT EXISTS `destination_map` (
  `slug` VARCHAR(160) NOT NULL PRIMARY KEY,
  `latitude` DECIMAL(8,5) NOT NULL,
  `longitude` DECIMAL(8,5) NOT NULL,
  `map_x` DECIMAL(6,2) DEFAULT NULL,
  `map_y` DECIMAL(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
"""


def main():
    slugs = [r['slug'].strip() for r in csv.DictReader(open(CSV_PATH, encoding='utf-8-sig', newline='')) if r['slug'].strip()]
    missing = [s for s in slugs if s not in COORDS]
    extra = [s for s in COORDS if s not in slugs]
    if missing or extra:
        sys.exit(f'coordinate list out of sync with destinations.csv. missing={missing} extra={extra}')

    rows, hidden = [], []
    for slug in slugs:
        lat, lon = COORDS[slug]
        x, y = warp(lon, lat)
        # visible = on the artwork, on the dotted globe (below the skyline) and not in the far west (Americas)
        visible = lon > -25 and 8 < x < ART_W - 8 and 255 < y < ART_H - 4
        if not visible:
            hidden.append(slug)
        rows.append('(' + ', '.join([sql_str(slug), f'{lat:.5f}', f'{lon:.5f}',
                                     f'{x:.2f}' if visible else 'NULL', f'{y:.2f}' if visible else 'NULL']) + ')')

    with open(SQL_PATH, 'w', encoding='utf-8', newline='\n') as f:
        f.write('-- Destination coordinates and pin positions on images/dest-globe.png (727 x 479)\n')
        f.write('-- (generated by database/tools/import_destination_map.py). Safe to re-run.\n')
        f.write('SET NAMES utf8mb4;\n\n' + DDL + '\n')
        f.write('INSERT INTO `destination_map` (slug, latitude, longitude, map_x, map_y) VALUES\n'
                + ',\n'.join(rows)
                + '\nON DUPLICATE KEY UPDATE latitude=VALUES(latitude), longitude=VALUES(longitude), map_x=VALUES(map_x), map_y=VALUES(map_y);\n')
    print(f'{len(rows)} destinations, {len(rows) - len(hidden)} pinned, not on the map: {hidden}')


if __name__ == '__main__':
    main()
