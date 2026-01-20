<?php
/**
 * Admin Sessions Management
 */
$pageTitle = 'Sessions';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/supabase.php';
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-kampung-charcoal">Booking Sessions</h1>
        <p class="text-gray-500">Manage time slots for reservations</p>
    </div>
    
    <button onclick="showAddSessionModal()" 
        class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Session
    </button>
</div>

<!-- Sessions Container -->
<div id="sessionsContainer" class="space-y-6">
    <div class="text-center py-12 text-gray-500">Loading sessions...</div>
</div>

<!-- Add/Edit Session Modal -->
<div id="sessionModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <h3 id="sessionModalTitle" class="text-lg font-semibold text-gray-800 mb-4">Add Session</h3>
        <form id="sessionForm" class="space-y-4">
            <input type="hidden" id="sessionId" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Session Name</label>
                <input type="text" id="sessionName" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" placeholder="e.g., Lunch, Dinner" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="sessionDescription" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" placeholder="Optional description"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Capacity (Pax)</label>
                <input type="number" id="sessionMaxPax" value="225" min="1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" required>
                <p class="text-xs text-gray-500 mt-1">Maximum number of guests allowed per session</p>
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="sessionActive" checked class="w-4 h-4 text-kampung-gold focus:ring-kampung-gold">
                <label for="sessionActive" class="ml-2 text-sm text-gray-700">Active</label>
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="closeSessionModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">Save Session</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Time Slot Modal -->
<div id="timeSlotModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <h3 id="timeSlotModalTitle" class="text-lg font-semibold text-gray-800 mb-4">Add Time Slot</h3>
        <form id="timeSlotForm" class="space-y-4">
            <input type="hidden" id="timeSlotId" value="">
            <input type="hidden" id="timeSlotSessionId" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                <input type="time" id="timeSlotValue" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Display Label</label>
                <input type="text" id="timeSlotLabel" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" placeholder="e.g., 11:30 AM" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Bookings</label>
                <input type="number" id="timeSlotMaxBookings" value="10" min="1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" required>
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="timeSlotActive" checked class="w-4 h-4 text-kampung-gold focus:ring-kampung-gold">
                <label for="timeSlotActive" class="ml-2 text-sm text-gray-700">Active</label>
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="closeTimeSlotModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">Save Time Slot</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-2xl">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Confirm Delete</h3>
        <p id="deleteMessage" class="text-gray-600 mb-6"></p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

<script>
const API_URL = '<?= BASE_URL ?>/admin/api/session_api.php';
let sessions = [];
let pendingDelete = null;

// Load sessions on page load
document.addEventListener('DOMContentLoaded', loadSessions);

async function loadSessions() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        
        if (result.success) {
            sessions = result.data;
            renderSessions();
        } else {
            showError('Failed to load sessions');
        }
    } catch (error) {
        console.error('Error loading sessions:', error);
        showError('Error loading sessions');
    }
}

function renderSessions() {
    const container = document.getElementById('sessionsContainer');
    
    if (sessions.length === 0) {
        container.innerHTML = `
            <div class="admin-card py-12">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Sessions Found</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        To get started, run the database migration in Supabase SQL Editor, 
                        or create your first session manually.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="/rembayung/database/session_tables.sql" target="_blank" 
                           class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50">
                            View SQL Migration
                        </a>
                        <button onclick="showAddSessionModal()" 
                            class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">
                            Add Session Manually
                        </button>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    container.innerHTML = sessions.map(session => `
        <div class="admin-card" data-session-id="${session.id}">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-kampung-charcoal">${escapeHtml(session.name)}</h3>
                    <span class="px-2 py-1 text-xs bg-kampung-gold/20 text-kampung-brown rounded">Capacity: ${session.max_pax || 225} pax</span>
                    ${!session.is_active ? '<span class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded">Inactive</span>' : ''}
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="showAddTimeSlotModal('${session.id}')" class="text-sm text-kampung-gold hover:text-kampung-brown flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Time
                    </button>
                    <button onclick="showEditSessionModal('${session.id}')" class="p-1 text-gray-500 hover:text-kampung-brown">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="confirmDeleteSession('${session.id}', '${escapeHtml(session.name)}')" class="p-1 text-gray-500 hover:text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            ${session.description ? `<p class="text-sm text-gray-500 mb-4">${escapeHtml(session.description)}</p>` : ''}
            
            <div class="space-y-2">
                ${session.time_slots && session.time_slots.length > 0 ? session.time_slots.map(slot => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg ${!slot.is_active ? 'opacity-50' : ''}">
                        <div class="flex items-center gap-4">
                            <span class="font-medium text-kampung-charcoal">${escapeHtml(slot.time_label)}</span>
                            <span class="text-sm text-gray-500">Max: ${slot.max_bookings} bookings</span>
                            ${!slot.is_active ? '<span class="text-xs text-gray-500">(Inactive)</span>' : ''}
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="showEditTimeSlotModal('${slot.id}', '${session.id}')" class="p-1 text-gray-400 hover:text-kampung-brown">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="confirmDeleteTimeSlot('${slot.id}', '${escapeHtml(slot.time_label)}')" class="p-1 text-gray-400 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                `).join('') : '<p class="text-sm text-gray-400 italic">No time slots yet</p>'}
            </div>
        </div>
    `).join('');
}

// Session Modal Functions
function showAddSessionModal() {
    document.getElementById('sessionModalTitle').textContent = 'Add Session';
    document.getElementById('sessionId').value = '';
    document.getElementById('sessionName').value = '';
    document.getElementById('sessionDescription').value = '';
    document.getElementById('sessionMaxPax').value = '225';
    document.getElementById('sessionActive').checked = true;
    openModal('sessionModal');
}

function showEditSessionModal(id) {
    const session = sessions.find(s => s.id === id);
    if (!session) return;
    
    document.getElementById('sessionModalTitle').textContent = 'Edit Session';
    document.getElementById('sessionId').value = session.id;
    document.getElementById('sessionName').value = session.name;
    document.getElementById('sessionDescription').value = session.description || '';
    document.getElementById('sessionMaxPax').value = session.max_pax || 225;
    document.getElementById('sessionActive').checked = session.is_active;
    openModal('sessionModal');
}

function closeSessionModal() {
    closeModal('sessionModal');
}

// Time Slot Modal Functions
function showAddTimeSlotModal(sessionId) {
    document.getElementById('timeSlotModalTitle').textContent = 'Add Time Slot';
    document.getElementById('timeSlotId').value = '';
    document.getElementById('timeSlotSessionId').value = sessionId;
    document.getElementById('timeSlotValue').value = '';
    document.getElementById('timeSlotLabel').value = '';
    document.getElementById('timeSlotMaxBookings').value = '10';
    document.getElementById('timeSlotActive').checked = true;
    openModal('timeSlotModal');
}

function showEditTimeSlotModal(slotId, sessionId) {
    const session = sessions.find(s => s.id === sessionId);
    const slot = session?.time_slots?.find(s => s.id === slotId);
    if (!slot) return;
    
    document.getElementById('timeSlotModalTitle').textContent = 'Edit Time Slot';
    document.getElementById('timeSlotId').value = slot.id;
    document.getElementById('timeSlotSessionId').value = sessionId;
    document.getElementById('timeSlotValue').value = slot.time_value;
    document.getElementById('timeSlotLabel').value = slot.time_label;
    document.getElementById('timeSlotMaxBookings').value = slot.max_bookings;
    document.getElementById('timeSlotActive').checked = slot.is_active;
    openModal('timeSlotModal');
}

function closeTimeSlotModal() {
    closeModal('timeSlotModal');
}

// Delete Modal Functions
function confirmDeleteSession(id, name) {
    pendingDelete = { type: 'session', id };
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete "${name}"? This will also delete all time slots in this session.`;
    openModal('deleteModal');
}

function confirmDeleteTimeSlot(id, label) {
    pendingDelete = { type: 'time_slot', id };
    document.getElementById('deleteMessage').textContent = `Are you sure you want to delete the time slot "${label}"?`;
    openModal('deleteModal');
}

function closeDeleteModal() {
    closeModal('deleteModal');
    pendingDelete = null;
}

// Form Handlers
document.getElementById('sessionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('sessionId').value;
    const data = {
        type: 'session',
        name: document.getElementById('sessionName').value,
        description: document.getElementById('sessionDescription').value,
        max_pax: parseInt(document.getElementById('sessionMaxPax').value) || 225,
        is_active: document.getElementById('sessionActive').checked
    };
    
    try {
        let response;
        if (id) {
            data.id = id;
            response = await fetch(API_URL, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        } else {
            data.display_order = sessions.length;
            response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        }
        
        const result = await response.json();
        if (result.success) {
            closeSessionModal();
            loadSessions();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving session:', error);
        alert('Failed to save session');
    }
});

document.getElementById('timeSlotForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('timeSlotId').value;
    const data = {
        type: 'time_slot',
        session_id: document.getElementById('timeSlotSessionId').value,
        time_value: document.getElementById('timeSlotValue').value,
        time_label: document.getElementById('timeSlotLabel').value,
        max_bookings: parseInt(document.getElementById('timeSlotMaxBookings').value),
        is_active: document.getElementById('timeSlotActive').checked
    };
    
    try {
        let response;
        if (id) {
            data.id = id;
            response = await fetch(API_URL, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        } else {
            const session = sessions.find(s => s.id === data.session_id);
            data.display_order = session?.time_slots?.length || 0;
            response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        }
        
        const result = await response.json();
        if (result.success) {
            closeTimeSlotModal();
            loadSessions();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error saving time slot:', error);
        alert('Failed to save time slot');
    }
});

document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    if (!pendingDelete) return;
    
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(pendingDelete)
        });
        
        const result = await response.json();
        if (result.success) {
            closeDeleteModal();
            loadSessions();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting:', error);
        alert('Failed to delete');
    }
});

// Auto-fill time label when time value changes
document.getElementById('timeSlotValue').addEventListener('change', function() {
    const timeLabel = document.getElementById('timeSlotLabel');
    if (!timeLabel.value) {
        const [hours, minutes] = this.value.split(':');
        const h = parseInt(hours);
        const ampm = h >= 12 ? 'PM' : 'AM';
        const h12 = h % 12 || 12;
        timeLabel.value = `${h12}:${minutes} ${ampm}`;
    }
});

// Utility Functions
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    document.getElementById('sessionsContainer').innerHTML = `
        <div class="admin-card text-center py-12">
            <p class="text-red-500 mb-4">${escapeHtml(message)}</p>
            <button onclick="loadSessions()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Retry</button>
        </div>
    `;
}

// Close modals on backdrop click
['sessionModal', 'timeSlotModal', 'deleteModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(id);
            if (id === 'deleteModal') pendingDelete = null;
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
