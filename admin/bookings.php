<?php

/**
 * Admin Bookings Management
 */
$pageTitle = 'Bookings';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/supabase.php';

$supabase = new Supabase();

// Filter params
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Build query
$query = 'order=booking_date.desc,created_at.desc';
if ($statusFilter) $query .= '&status=eq.' . urlencode($statusFilter);
if ($dateFilter) $query .= '&booking_date=eq.' . urlencode($dateFilter);

$result = $supabase->get('bookings', $query);
$bookings = $result['success'] ? $result['data'] : [];

// Fetch sessions for lookup
$sessionsResult = $supabase->get('booking_sessions', '');
$sessionsMap = [];
if ($sessionsResult['success'] && !empty($sessionsResult['data'])) {
    foreach ($sessionsResult['data'] as $s) {
        $sessionsMap[$s['id']] = $s['name'];
    }
}

// Fetch tables for lookup
$tablesResult = $supabase->get('restaurant_tables', '');
$tablesMap = [];
if ($tablesResult['success'] && !empty($tablesResult['data'])) {
    foreach ($tablesResult['data'] as $t) {
        $tablesMap[$t['id']] = $t['name'] . ' (' . ($t['floor'] == 1 ? 'G' : 'F2') . ')';
    }
}
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-kampung-charcoal">Bookings</h1>
        <p class="text-gray-500">Manage all reservations</p>
    </div>

    <!-- Filters -->
    <form class="flex flex-wrap gap-3" method="GET">
        <select name="status" class="px-4 py-2 border rounded-lg text-sm" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <input type="date" name="date" value="<?= $dateFilter ?>"
            class="px-4 py-2 border rounded-lg text-sm" onchange="this.form.submit()">
        <?php if ($statusFilter || $dateFilter): ?>
            <a href="<?= BASE_URL ?>/admin/bookings.php" class="px-4 py-2 text-sm text-gray-500 hover:text-red-500">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Bookings Table -->
<div class="admin-card overflow-hidden">
    <?php if (empty($bookings)): ?>
        <p class="text-gray-500 text-center py-12">No bookings found</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-500 text-sm border-b bg-gray-50">
                        <th class="p-4 font-medium">Date</th>
                        <th class="p-4 font-medium">Session / Time</th>
                        <th class="p-4 font-medium">Table</th>
                        <th class="p-4 font-medium">Name</th>
                        <th class="p-4 font-medium">Contact</th>
                        <th class="p-4 font-medium">Pax</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($bookings as $b): ?>
                        <tr class="hover:bg-gray-50" id="booking-<?= $b['id'] ?>">
                            <td class="p-4"><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
                            <td class="p-4">
                                <?php if (!empty($b['session_id']) && isset($sessionsMap[$b['session_id']])): ?>
                                    <div class="font-medium text-kampung-brown"><?= htmlspecialchars($sessionsMap[$b['session_id']]) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($b['time_slot'] ?? '') ?></div>
                                <?php else: ?>
                                    <span class="capitalize"><?= htmlspecialchars($b['time_slot'] ?? 'N/A') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <?php if (!empty($b['table_id']) && isset($tablesMap[$b['table_id']])): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 h-6">
                                        <?= htmlspecialchars($tablesMap[$b['table_id']]) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs italic">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 font-medium"><?= htmlspecialchars($b['name']) ?></td>
                            <td class="p-4">
                                <div class="text-sm"><?= htmlspecialchars($b['phone']) ?></div>
                                <div class="text-xs text-gray-400"><?= htmlspecialchars($b['email']) ?></div>
                            </td>
                            <td class="p-4"><?= $b['pax'] ?></td>
                            <td class="p-4">
                                <span class="status-badge status-<?= $b['status'] ?>" id="status-<?= $b['id'] ?>"><?= $b['status'] ?></span>
                            </td>
                            <td class="p-4">
                                <?php if ($b['status'] === 'pending'): ?>
                                    <button onclick="updateStatus('<?= $b['id'] ?>', 'confirmed')"
                                        class="text-green-600 hover:text-green-800 mr-3 text-sm font-medium">Confirm</button>
                                    <button onclick="updateStatus('<?= $b['id'] ?>', 'cancelled')"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">Cancel</button>
                                <?php elseif ($b['status'] === 'confirmed'): ?>
                                    <button onclick="updateStatus('<?= $b['id'] ?>', 'cancelled')"
                                        class="text-red-600 hover:text-red-800 text-sm font-medium">Cancel</button>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-2xl">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Confirm Action</h3>
        <p id="confirmMessage" class="text-gray-600 mb-6"></p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeConfirmModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button id="confirmBtn" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">Confirm</button>
        </div>
    </div>
</div>

<script>
    let pendingAction = null;

    function showConfirmModal(id, status) {
        const modal = document.getElementById('confirmModal');
        const message = document.getElementById('confirmMessage');
        const btn = document.getElementById('confirmBtn');

        message.textContent = `Are you sure you want to mark this booking as ${status}?`;

        // Set button color based on action
        if (status === 'confirmed') {
            btn.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
            btn.textContent = 'Confirm Booking';
        } else if (status === 'cancelled') {
            btn.className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
            btn.textContent = 'Cancel Booking';
        }

        pendingAction = {
            id,
            status
        };
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingAction = null;
    }

    async function executeUpdate() {
        if (!pendingAction) return;

        const {
            id,
            status
        } = pendingAction;
        closeConfirmModal();

        try {
            const response = await fetch('<?= BASE_URL ?>/admin/api/update_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    id,
                    status
                })
            });

            const result = await response.json();

            if (result.success) {
                location.reload();
            } else {
                alert('Failed to update: ' + result.message);
            }
        } catch (error) {
            console.error('Update error:', error);
            alert('Error updating booking: ' + error.message);
        }
    }

    function updateStatus(id, status) {
        showConfirmModal(id, status);
    }

    // Attach click handler to confirm button
    document.getElementById('confirmBtn').addEventListener('click', executeUpdate);

    // Close modal on backdrop click
    document.getElementById('confirmModal').addEventListener('click', function(e) {
        if (e.target === this) closeConfirmModal();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>