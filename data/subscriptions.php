<?php
/**
 * Club Yatri - Subscription & Holiday Pass Data
 * Plans & Seasonal Studio Categories
 */

declare(strict_types=1);

$subscriptionPlans = [
    'royal-bharat' => [
        'id' => 'royal-bharat',
        'title' => '3-Year — Yatri Royal Bharat Pass',
        'short_title' => 'Yatri Royal Bharat Pass',
        'tenure' => '3 Years',
        'annual_nights' => 7,
        'points_annual' => 10000,
        'badge' => 'Introductory Voyager',
        'featured' => false,
        'description' => 'Ideal for couples and boutique travelers wanting guaranteed annual luxury escapes across premier Indian destinations.',
        'eligible_seasons' => ['Green Studio', 'Purple Studio'],
        'eligible_rooms' => ['Deluxe Room', 'Super Deluxe / Executive Room'],
        'resorts_access' => '85+ Handpicked Indian Resorts',
        'banking_years' => 2,
        'dining_discount' => '10%',
        'concierge' => 'Dedicated Subscriber Desk',
        'exchange' => 'Domestic Exchange Network',
        'highlight_features' => [
            '7 Vacation Nights per subscription year',
            'Access across 85+ Indian resort destinations',
            'Complimentary points accumulation up to 2 years',
            '10% privileged dining and spa savings',
            'Zero off-peak blackout constraints'
        ]
    ],
    'asian-signature' => [
        'id' => 'asian-signature',
        'title' => '5-Year — Yatri Asian Signature Pass',
        'short_title' => 'Yatri Asian Signature Pass',
        'tenure' => '5 Years',
        'annual_nights' => 14,
        'points_annual' => 22000,
        'badge' => 'Family Favorite',
        'featured' => false,
        'description' => 'A comprehensive 5-year holiday program offering domestic freedom plus privileged access to Thailand, Bali, and Southeast Asia.',
        'eligible_seasons' => ['Green Studio', 'Purple Studio', 'Yellow Studio'],
        'eligible_rooms' => ['Super Deluxe / Executive Room', 'Luxury Suite / 1-BHK'],
        'resorts_access' => '140+ Resorts (India + Southeast Asia)',
        'banking_years' => 3,
        'dining_discount' => '15%',
        'concierge' => 'Dedicated Relationship Manager',
        'exchange' => 'Domestic + Southeast Asia Exchange',
        'highlight_features' => [
            '14 Vacation Nights per subscription year',
            '1-BHK & Luxury Suites eligibility',
            'International access to Thailand, Bali & Sri Lanka',
            'Priority 120-day advance booking window',
            '15% savings across all resort dining & spa treatments'
        ]
    ],
    'imperial-world' => [
        'id' => 'imperial-world',
        'title' => '10-Year — Yatri Imperial World Pass',
        'short_title' => 'Yatri Imperial World Pass',
        'tenure' => '10 Years',
        'annual_nights' => 21,
        'points_annual' => 38000,
        'badge' => 'Most Popular Tier ★',
        'featured' => true,
        'description' => 'The ultimate decadal holiday solution for discerning travelers, featuring worldwide exchange rights to Dubai, Maldives, and Europe.',
        'eligible_seasons' => ['Green Studio', 'Purple Studio', 'Yellow Studio', 'Red Studio'],
        'eligible_rooms' => ['Luxury Suite / 1-BHK', 'Privilege Crown Villa / Presidential Suite'],
        'resorts_access' => '200+ Resorts Worldwide',
        'banking_years' => 4,
        'dining_discount' => '20%',
        'concierge' => '24/7 Senior Lifestyle Butler',
        'exchange' => 'Worldwide Exchange in 100+ Countries',
        'highlight_features' => [
            '21 Vacation Nights per subscription year',
            '2-Bedroom Royal Pool Villas & Havelis',
            'Guaranteed festive season holiday booking rights',
            'Global exchange to Maldives, Dubai, Europe & Bali',
            'Dedicated 24/7 Senior Lifestyle Butler & airport transfers',
            '20% dining, experiences & wellness savings'
        ]
    ],
    'privilege-crown' => [
        'id' => 'privilege-crown',
        'title' => '25-Year — Yatri Privilege Crown Asset',
        'short_title' => 'Yatri Privilege Crown Asset',
        'tenure' => '25 Years (Generational)',
        'annual_nights' => 35,
        'points_annual' => 65000,
        'badge' => 'Ultra-Luxury Asset',
        'featured' => false,
        'description' => 'A perpetual 25-year inheritable holiday deed ensuring multi-generational 5-star vacation security with presidential suite allocations.',
        'eligible_seasons' => ['Green Studio', 'Purple Studio', 'Yellow Studio', 'Red Studio (Unrestricted)'],
        'eligible_rooms' => ['Privilege Crown Villa / Presidential Suite', 'Royal Havelis & Private Islands'],
        'resorts_access' => 'Unrestricted Worldwide Global Network',
        'banking_years' => 'Lifetime Rolling',
        'dining_discount' => '25% + Chef Table',
        'concierge' => 'Private Travel Director',
        'exchange' => 'Worldwide Top-Tier Priority Access',
        'highlight_features' => [
            '35+ Vacation Nights per subscription year',
            'Presidential suites & private luxury pool villas',
            'Perpetual family inheritance with zero transfer charges',
            'Private aviation and yacht charter privileges',
            '25% savings + private chef dinners & sommelier tastings'
        ]
    ]
];

$seasonalStudioCategories = [
    'green' => [
        'name' => 'Green Studio',
        'period' => 'Off-Peak & Monsoon',
        'badge_color' => '#10b981',
        'description' => 'Serene, tranquil holidays in lush monsoon seasons, rejuvenation wellness escapes, and quiet nature retreats.'
    ],
    'purple' => [
        'name' => 'Purple Studio',
        'period' => 'Regular Season',
        'badge_color' => '#8b5cf6',
        'description' => 'Pleasant autumn, winter, and spring weeks ideal for cultural tours, palace explorations, and family gatherings.'
    ],
    'yellow' => [
        'name' => 'Yellow Studio',
        'period' => 'Peak & Weekend Holidays',
        'badge_color' => '#f59e0b',
        'description' => 'Extended holiday weekends, national holidays, and prime summer holiday getaways across beach and hill station resorts.'
    ],
    'red' => [
        'name' => 'Red Studio',
        'period' => 'Super Peak / Festive & New Year',
        'badge_color' => '#ef4444',
        'description' => 'High-demand festive seasons including Diwali, Christmas, New Year celebrations, and premium high-altitude winter snow weeks.'
    ]
];
