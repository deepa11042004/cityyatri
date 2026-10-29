<?php
/**
 * Club Yatri - Room & Suite Categories Data
 */

declare(strict_types=1);

$roomCategories = [
    'deluxe' => [
        'id' => 'deluxe',
        'title' => 'Deluxe Room',
        'capacity' => '2 Adults + 1 Child',
        'area' => '420 - 500 sq.ft.',
        'bed_type' => 'King Bed or Twin Beds',
        'points_night' => 1000,
        'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
        'description' => 'Elegantly appointed sanctuary featuring modern aesthetic furnishings, handcrafted teakwood accents, luxury marble en-suite bathroom, and garden or courtyard views.',
        'amenities' => [
            'High-Speed Wi-Fi',
            '4K Smart Television',
            'Premium Coffee Bar',
            'Italian Marble Bathroom',
            'Rain Shower & Bathrobe',
            'Private Balcony'
        ]
    ],
    'super-deluxe' => [
        'id' => 'super-deluxe',
        'title' => 'Super Deluxe / Executive Room',
        'capacity' => '2 Adults + 2 Children',
        'area' => '580 - 680 sq.ft.',
        'bed_type' => 'Grand King Bed + Daybed',
        'points_night' => 1400,
        'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
        'description' => 'Spacious executive accommodation designed for elevated family luxury. Features panoramic valley or coastal sea views, dedicated lounge seating, and bespoke turndown service.',
        'amenities' => [
            'Panoramic Sea / Mountain Views',
            'Walk-in Wardrobe',
            'Executive Work Desk',
            'Deep Soaking Bathtub',
            '24/7 In-Room Dining',
            'Pillow Menu'
        ]
    ],
    'luxury-suite' => [
        'id' => 'luxury-suite',
        'title' => 'Luxury Suite / 1-BHK',
        'capacity' => '4 Guests',
        'area' => '850 - 1,100 sq.ft.',
        'bed_type' => 'Master King Suite + Sofa Bed',
        'points_night' => 2000,
        'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
        'description' => 'Expansive one-bedroom suite featuring a separate grand living room, dining salon, private sundeck, and powder room. Blends royal heritage detailing with contemporary opulence.',
        'amenities' => [
            'Separate Master Bedroom & Living Hall',
            'Private Dining Area',
            'Panoramic Sun Terrace',
            'Dual Vanity En-Suite',
            'Butler Call System',
            'Espresso Machine & Wine Fridge'
        ]
    ],
    'privilege-crown-villa' => [
        'id' => 'privilege-crown-villa',
        'title' => 'Privilege Crown Villa / Presidential Suite',
        'capacity' => '6 - 8 Guests',
        'area' => '1,800 - 3,200 sq.ft.',
        'bed_type' => '2-3 Master King En-Suites',
        'points_night' => 3200,
        'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=800&q=80',
        'description' => 'The absolute pinnacle of luxury vacation living. Private temperature-controlled plunge pool, dedicated private butler, private chef dining terrace, and sweeping private vistas.',
        'amenities' => [
            'Private Temperature-Controlled Pool',
            'Dedicated 24/7 Private Butler',
            'Personal Chef & Kitchen Bar',
            'Private Jacuzzi on Terrace',
            'Private Garden / Beach Access',
            'Chauffeur VIP Transfers'
        ]
    ]
];
