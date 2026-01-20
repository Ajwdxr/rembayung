<?php
/**
 * Admin Dashboard
 */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/supabase.php';

$supabase = new Supabase();

// Get today's bookings count
$today = date('Y-m-d');
$todayResult = $supabase->get('bookings', 'booking_date=eq.' . $today . '&select=id');
$todayCount = $todayResult['success'] ? count($todayResult['data']) : 0;

// Get pending bookings count
$pendingResult = $supabase->get('bookings', 'status=eq.pending&select=id');
$pendingCount = $pendingResult['success'] ? count($pendingResult['data']) : 0;

// Get recent bookings
$recentResult = $supabase->get('bookings', 'order=created_at.desc&limit=5');
$recentBookings = $recentResult['success'] ? $recentResult['data'] : [];
?>

<div class="mb-8">
    <h1 class="text-2xl font-semibold text-kampung-charcoal">Dashboard</h1>
    <p class="text-gray-500">Welcome back, <?= $_SESSION['admin_name'] ?? 'Admin' ?>!</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="admin-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Today's Bookings</p>
                <p class="text-3xl font-semibold text-kampung-charcoal mt-1"><?= $todayCount ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="admin-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Approval</p>
                <p class="text-3xl font-semibold text-kampung-charcoal mt-1"><?= $pendingCount ?></p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="admin-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Quick Actions</p>
                <a href="<?= BASE_URL ?>/admin/bookings.php" class="text-kampung-brown hover:underline text-sm mt-2 inline-block">View All Bookings →</a>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings -->
<div class="admin-card">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold text-kampung-charcoal">Recent Bookings</h2>
        <a href="<?= BASE_URL ?>/admin/bookings.php" class="text-kampung-brown text-sm hover:underline">View All</a>
    </div>
    
    <?php if (empty($recentBookings)): ?>
    <p class="text-gray-500 text-center py-8">No bookings yet</p>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b">
                    <th class="pb-3 font-medium">Name</th>
                    <th class="pb-3 font-medium">Date</th>
                    <th class="pb-3 font-medium">Time</th>
                    <th class="pb-3 font-medium">Pax</th>
                    <th class="pb-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($recentBookings as $booking): ?>
                <tr class="hover:bg-gray-50">
                    <td class="py-3"><?= htmlspecialchars($booking['name']) ?></td>
                    <td class="py-3"><?= date('d M Y', strtotime($booking['booking_date'])) ?></td>
                    <td class="py-3 capitalize"><?= $booking['time_slot'] ?></td>
                    <td class="py-3"><?= $booking['pax'] ?></td>
                    <td class="py-3">
                        <span class="status-badge status-<?= $booking['status'] ?>"><?= $booking['status'] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
