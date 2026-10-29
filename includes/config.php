<?php
/**
 * Club Yatri - Global Central Configuration
 * Legal Entity: Club Yatri Hotel and Resort Pvt Ltd
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Brand & Corporate Identity
define('SITE_NAME', 'Club Yatri');
define('SITE_TAGLINE', 'Vacation Ownership & Bespoke Timeshare Holidays');
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
define('CONTACT_PHONE_TOLL_FREE', '1800-209-9287');
define('CONTACT_PHONE_TEL', '+9118002099287');
define('CONTACT_WHATSAPP', '+919876543210');
define('CONTACT_WHATSAPP_DISPLAY', '+91 98765 43210');
define('CONTACT_EMAIL', 'concierge@clubyatri.com');
define('CONTACT_SUPPORT_EMAIL', 'support@clubyatri.com');
define('CONTACT_ADDRESS', 'Level 14, The Imperial Tower, Bandra Kurla Complex (BKC), Mumbai - 400051, Maharashtra, India');

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
    'facebook' => 'https://facebook.com/clubyatri',
    'instagram' => 'https://instagram.com/clubyatri',
    'linkedin' => 'https://linkedin.com/company/clubyatri',
    'youtube' => 'https://youtube.com/@clubyatri',
];

// 8. Default SEO Metadata
$GLOBALS['defaultSeo'] = [
    'title' => 'Club Yatri | Luxury Vacation Ownership & Bespoke Timeshare Holidays',
    'description' => 'Guaranteed 5-star resort weeks, flexible internal & international holiday exchanges, and 24/7 dedicated lifestyle concierge services with Club Yatri Hotel and Resort Pvt Ltd.',
    'keywords' => 'Club Yatri, vacation ownership, luxury timeshare, holiday club India, holiday passes, Goa luxury villas, Rajasthan palaces, timeshare subscription, holiday asset',
    'og_image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
];
