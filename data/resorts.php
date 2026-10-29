<?php
/**
 * Club Yatri - Resorts Portfolio Data
 */

declare(strict_types=1);

$resortsList = [
    'azure-sands-goa' => [
        'id' => 'azure-sands-goa',
        'name' => 'The Azure Sands Villa & Spa',
        'destination_id' => 'goa',
        'location' => 'Varca Beach, South Goa',
        'category' => 'Beach & Coastal',
        'region' => 'National',
        'points_night' => 1200,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'summary' => 'Direct private beach cabanas, Portuguese heritage architecture, infinity plunge pool suites, and fresh seafood sommelier dining.',
        'allotment' => '7 to 21 Nights / Year',
        'amenities' => ['Private Beach', 'Ayurveda Spa', 'Butler Service', 'Yacht Charter', 'Fine Dining'],
        'details' => 'Set on the peaceful shores of Varca Beach in South Goa, The Azure Sands features 45 luxury pool villas, an authentic Ayurvedic spa clinic, and direct white-sand beach access reserved exclusively for Club Yatri vacation owners.'
    ],
    'udaivilas-haveli' => [
        'id' => 'udaivilas-haveli',
        'name' => 'Udaivilas Haveli & Royal Retreat',
        'destination_id' => 'udaipur',
        'location' => 'Lake Pichola, Udaipur, Rajasthan',
        'category' => 'Heritage Palaces',
        'region' => 'National',
        'points_night' => 1500,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
        'summary' => 'A restored 18th-century Rajput palace offering sunset lake vistas, hand-carved stone arches, and candlelit royal banquets.',
        'allotment' => '7 to 14 Nights / Year',
        'amenities' => ['Lakefront Suites', 'Heritage Courtyards', 'Royal Dining', 'Helipad Access', 'Sunset Cruise'],
        'details' => 'Overlooking the waters of Lake Pichola and the City Palace, this heritage retreat combines royal Rajput hospitality with world-class wellness, private solar boat transfers, and royal banqueting.'
    ],
    'cedar-mist-manali' => [
        'id' => 'cedar-mist-manali',
        'name' => 'The Cedar Mist Sanctuary',
        'destination_id' => 'manali',
        'location' => 'Solang Valley, Himachal Pradesh',
        'category' => 'Himalayan Retreats',
        'region' => 'National',
        'points_night' => 1100,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=800&q=80',
        'summary' => 'Pine-scented mountain air and glacier panoramas. Timber chalets with wood-burning fireplaces and heated outdoor glass pool.',
        'allotment' => '7 to 21 Nights / Year',
        'amenities' => ['Fireplace Suites', 'Heated Glass Pool', 'Trekking Butler', 'Snow Lounge', 'Alpine Spa'],
        'details' => 'Nestled in an untouched pine forest in the Solang Valley, Cedar Mist offers cozy, Scandinavian-inspired timber chalets with personal fireplaces, floor-to-ceiling snow views, and guided Himalayan trekking.'
    ],
    'amber-heritage-jaipur' => [
        'id' => 'amber-heritage-jaipur',
        'name' => 'The Amber Fort Palace & Polo Club',
        'destination_id' => 'jaipur',
        'location' => 'Amer, Jaipur, Rajasthan',
        'category' => 'Heritage Palaces',
        'region' => 'National',
        'points_night' => 1400,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1599661046827-dacff0c0f09a?auto=format&fit=crop&w=800&q=80',
        'summary' => 'Aristocratic Jaipur luxury with peacock gardens, private polo field access, frescoed suites, and royal afternoon high teas.',
        'allotment' => '7 to 14 Nights / Year',
        'amenities' => ['Polo Club Access', 'Heritage Suites', 'Royal High Tea', 'Vintage Car Transfers'],
        'details' => 'Set against the Aravali hills near Amer Fort, this aristocratic retreat features authentic royal suites, private polo exhibitions, and bespoke royal dining.'
    ],
    'phuket-coral-haven' => [
        'id' => 'phuket-coral-haven',
        'name' => 'Phuket Cliffside Ocean Estate',
        'destination_id' => 'thailand',
        'location' => 'Kamala Beach (Millionaires Mile), Phuket, Thailand',
        'category' => 'International Escapes',
        'region' => 'International',
        'points_night' => 1750,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?auto=format&fit=crop&w=800&q=80',
        'summary' => 'Perched high on the granite headlands of Kamala, overlooking the sapphire Andaman Sea with private infinity plunge pools.',
        'allotment' => 'Global Exchange',
        'amenities' => ['Cliffside Infinity Pool', 'Thai Herb Spa', 'Private Yacht Berth', 'Private Chef'],
        'details' => 'Directly overlooking the Andaman Sea along Phuket’s famed Millionaires’ Mile, offering Thai teakwood design, personal infinity pools, and private catamaran day cruises to the Phi Phi Islands.'
    ],
    'ayung-rainforest-bali' => [
        'id' => 'ayung-rainforest-bali',
        'name' => 'Ayung River Rainforest Estate',
        'destination_id' => 'bali',
        'location' => 'Ubud Valley, Bali, Indonesia',
        'category' => 'International Escapes',
        'region' => 'International',
        'points_night' => 1600,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80',
        'summary' => 'Cantilevered plunge pools over jungle canopies, sacred Balinese cleansing rituals, and private river waterfall dining.',
        'allotment' => 'Global Exchange',
        'amenities' => ['Jungle Plunge Pool', 'Balinese Healers', 'Organic Cuisine', 'Waterfall Deck'],
        'details' => 'An award-winning wellness hideaway in the sacred Ubud Valley, featuring private plunge pool villas hanging dramatically over the Ayung River gorge.'
    ],
    'dubai-palm-mirage' => [
        'id' => 'dubai-palm-mirage',
        'name' => 'The Palm Mirage Sky Penthouse',
        'destination_id' => 'dubai',
        'location' => 'Palm Jumeirah, Dubai, UAE',
        'category' => 'International Escapes',
        'region' => 'International',
        'points_night' => 1850,
        'rating' => 5,
        'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=800&q=80',
        'summary' => 'Floor-to-ceiling Arabian Gulf and Dubai Marina skyline views, private infinity terrace, and luxury chauffeur transfers.',
        'allotment' => 'Global Exchange',
        'amenities' => ['Sky Infinity Pool', 'Michelin Chefs', 'Chauffeur Service', 'Private Marina'],
        'details' => 'Ultra-modern private residences situated on the crescent of Palm Jumeirah with panoramic city skyline views, private beach clubs, and 24/7 dedicated lifestyle concierges.'
    ]
];
