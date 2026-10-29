<?php
/**
 * Club Yatri - Reusable Booking / Inquiry Search Form Component
 */

declare(strict_types=1);
?>
<div class="search-widget" id="searchWidget">
  <form class="search-form" id="heroSearchForm" action="<?php echo url('booking-request.php'); ?>" method="GET">
    <div class="search-field">
      <label for="searchDestination">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
        Destination
      </label>
      <select id="searchDestination" name="destination">
        <option value="all">All 200+ Destinations</option>
        <option value="goa">Goa (Varca & Coastal Sands)</option>
        <option value="udaipur">Udaipur (Lake Pichola Haveli)</option>
        <option value="manali">Manali (Solang Valley Sanctuary)</option>
        <option value="jaipur">Jaipur (Amber Fort Palace)</option>
        <option value="thailand">Thailand (Phuket & Koh Samui)</option>
        <option value="bali">Bali (Ubud & Ayung River)</option>
        <option value="dubai">Dubai, UAE (Palm Jumeirah)</option>
      </select>
    </div>

    <div class="search-field">
      <label for="searchDates">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        Holiday Season
      </label>
      <select id="searchDates" name="season">
        <option value="green">Green Studio (Off-Peak/Monsoon)</option>
        <option value="purple" selected>Purple Studio (Regular Season)</option>
        <option value="yellow">Yellow Studio (Peak/Weekend)</option>
        <option value="red">Red Studio (Festive/Diwali/New Year)</option>
      </select>
    </div>

    <div class="search-field">
      <label for="searchGuests">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        Travelers
      </label>
      <select id="searchGuests" name="guests">
        <option value="2">Couple (2 Adults)</option>
        <option value="4">Family (2 Adults + 2 Kids)</option>
        <option value="6">Executive Suite (4-6 Guests)</option>
        <option value="8">Crown Villa (6-8 Guests)</option>
      </select>
    </div>

    <div class="search-field">
      <label for="searchRoomType">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        Room Category
      </label>
      <select id="searchRoomType" name="room_category">
        <option value="deluxe">Deluxe Room</option>
        <option value="super-deluxe">Super Deluxe / Executive Room</option>
        <option value="luxury-suite" selected>Luxury Suite / 1-BHK</option>
        <option value="privilege-crown-villa">Privilege Crown Villa</option>
      </select>
    </div>

    <button type="submit" class="btn btn-gold btn-lg">
      <span>Check Allocations</span>
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
    </button>
  </form>
</div>
