<?php
/**
 * Club Yatri - Global Central Configuration
 * Legal Entity: Club Yatri Hotel and Resort Pvt Ltd
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Editable in the admin (Settings); every value below is the default used until something is saved there
require_once __DIR__ . '/settings.php';

// 1. Brand & Corporate Identity
define('SITE_NAME', site_setting('site_name', 'Club Yatri'));
define('SITE_TAGLINE', site_setting('site_tagline', 'Vacation Ownership & Bespoke Timeshare Holidays'));
define('LEGAL_ENTITY_NAME', 'Club Yatri Hotel and Resort Pvt Ltd');
define('CIN_NUMBER', 'U55101MH2026PTC000000 (Placeholder - Pending Client Verification)');
define('GSTIN_NUMBER', '27AAAAA0000A1Z5 (Placeholder)');

// 2. Base URL Auto-Detection for XAMPP / Local / Production
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Derive from the project root (not the running script) so actions/ scripts get the same base path
$docRoot = rtrim(str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
$projectRoot = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));
if ($docRoot !== '' && str_starts_with(strtolower($projectRoot), strtolower($docRoot))) {
    $basePath = rtrim(substr($projectRoot, strlen($docRoot)), '/');
} else {
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
}

// Base URL definition
define('BASE_URL', $protocol . $host . $basePath);
define('BASE_PATH', $basePath);

// 3. Contact & Concierge Information
define('CONTACT_PHONE_TOLL_FREE', site_setting('contact_phone', '1800-209-9287'));
define('CONTACT_PHONE_TEL', site_setting('contact_phone') !== '' ? site_phone_tel(site_setting('contact_phone')) : '+9118002099287');
define('CONTACT_PHONE_ALT', site_setting('contact_phone_alt'));
define('CONTACT_WHATSAPP', site_setting('contact_whatsapp') !== '' ? site_phone_tel(site_setting('contact_whatsapp')) : '+919876543210');
define('CONTACT_WHATSAPP_DISPLAY', site_setting('contact_whatsapp', '+91 98765 43210'));
define('CONTACT_EMAIL', site_setting('contact_email', 'concierge@clubyatri.com'));
define('CONTACT_SUPPORT_EMAIL', site_setting('contact_support_email', 'support@clubyatri.com'));
define('CONTACT_ADDRESS', site_setting('contact_address', 'Level 14, The Imperial Tower, Bandra Kurla Complex (BKC), Mumbai - 400051, Maharashtra, India'));
define('CONTACT_MAP_URL', site_setting('contact_map_url'));

// 4. Brand Palette Tokens (Light Hospitality Theme: Rich Red & Golden Yellow)
define('COLOR_PRIMARY', '#C8102E');   // Primary Red (#C8102E)
define('COLOR_RED', '#C8102E');       // Rich Red
define('COLOR_RED_DARK', '#A50F25');  // Deep Brand Red (#A50F25)
define('COLOR_YELLOW', '#FFB300');    // Brand Golden Yellow (#FFB300)
define('COLOR_BG_LIGHT', '#FFFFFF');  // Crisp White
define('COLOR_BG_WARM', '#FFF6E5');   // Warm Ivory Accent

// 5. Assets Paths
define('ASSETS_CSS_PATH', BASE_PATH . '/assets/css/style.css');
define('ASSETS_CUSTOM_CSS_PATH', BASE_PATH . '/assets/css/custom.css');
define('ASSETS_JS_PATH', BASE_PATH . '/assets/js/main.js');
define('ASSETS_CUSTOM_JS_PATH', BASE_PATH . '/assets/js/custom.js');
define('ASSETS_IMAGES_PATH', BASE_PATH . '/assets/images');

// 6. Navigation Menu Items
$GLOBALS['navMenu'] = [
    'home' => [
        'title' => 'Home',
        'url' => 'index.php',
    ],
    'about' => [
        'title' => 'About Us',
        'url' => 'about.php',
    ],
    'destinations' => [
        'title' => 'Destinations',
        'url' => 'destinations.php',
        'children' => [
            ['title' => 'Domestic Resorts', 'url' => 'hotels-national.php'],
            ['title' => 'International Escapes', 'url' => 'hotels-international.php'],
            ['title' => 'Resort Network Map', 'url' => 'resort-network.php'],
        ]
    ],
    'passes' => [
        'title' => 'Holiday Passes',
        'url' => 'passes-subscriptions.php',
    ],
    'rooms' => [
        'title' => 'Rooms & Suites',
        'url' => 'rooms.php',
    ],
    'amenities' => [
        'title' => 'Amenities',
        'url' => 'amenities.php',
    ],
    'itinerary' => [
        'title' => 'Itineraries',
        'url' => 'itinerary.php',
    ],
    'gallery' => [
        'title' => 'Gallery',
        'url' => 'gallery.php',
    ],
    'faq' => [
        'title' => 'FAQs',
        'url' => 'faq.php',
    ],
    'contact' => [
        'title' => 'Contact',
        'url' => 'contact.php',
    ],
];

// 7. Social Media Channels
$GLOBALS['socialLinks'] = [
    'facebook' => site_setting('social_facebook', 'https://facebook.com/clubyatri'),
    'instagram' => site_setting('social_instagram', 'https://instagram.com/clubyatri'),
    'linkedin' => site_setting('social_linkedin', 'https://linkedin.com/company/clubyatri'),
    'youtube' => site_setting('social_youtube', 'https://youtube.com/@clubyatri'),
];
foreach (['twitter' => 'social_twitter'] as $k => $sk) {   // optional extra network, shown only when filled in
    if (site_setting($sk) !== '') {
        $GLOBALS['socialLinks'][$k] = site_setting($sk);
    }
}

// 8. Default SEO Metadata
$GLOBALS['defaultSeo'] = [
    'title' => site_setting('seo_title', 'Club Yatri | Luxury Vacation Ownership & Bespoke Timeshare Holidays'),
    'description' => site_setting('seo_description', 'Guaranteed 5-star resort weeks, flexible internal & international holiday exchanges, and 24/7 dedicated lifestyle concierge services with Club Yatri Hotel and Resort Pvt Ltd.'),
    'keywords' => 'Club Yatri, vacation ownership, luxury timeshare, holiday club India, holiday passes, Goa luxury villas, Rajasthan palaces, timeshare subscription, holiday asset',
    'og_image' => site_setting('seo_og_image') !== '' ? (preg_match('~^https?://~i', site_setting('seo_og_image')) ? site_setting('seo_og_image') : BASE_URL . '/' . ltrim(site_setting('seo_og_image'), '/')) : 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
];
