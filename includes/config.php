<?php

/**
 * Rembayung Configuration
 */

// Supabase Configuration
define('SUPABASE_URL', 'https://jerfuliapxfvszssvkdg.supabase.co');
define('SUPABASE_KEY', 'sb_publishable_MPG8byYzYYQZ4TurM9H1og_EhM7c0N2');

// Site Configuration
define('SITE_NAME', 'Rembayung');
define('SITE_TAGLINE', 'Authentic Kampung Cuisine by Khairul Aming');
define('BASE_URL', '/rembayung');

// Restaurant Info
define('RESTAURANT_ADDRESS', 'Lot 2791, Jalan Daud, Off, Jalan Raja Muda Abdul Aziz, Kampung Baru, 50300 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur');
define('RESTAURANT_PHONE', '+60 3-1234 5678');
define('RESTAURANT_EMAIL', 'hello@rembayung.my');

// Operating Hours
define('OPERATING_HOURS', [
    'open' => ['start' => '11:00', 'end' => '23:00'],
]);
define('CLOSED_DAY', 'Friday'); // Day of the week restaurant is closed

// Booking Settings
define('MIN_PAX', 2);
define('MAX_PAX', 8);
define('ADVANCE_BOOKING_DAYS', 14);

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

/**
 * Sanitize input
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Redirect helper
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}
