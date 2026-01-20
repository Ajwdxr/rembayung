<?php
/**
 * Admin Closures Management
 * Manage restaurant holidays and closed dates
 */
$pageTitle = 'Closures';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/supabase.php';
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-kampung-charcoal">Restaurant Closures</h1>
        <p class="text-gray-500">Manage holidays and closed dates</p>
    </div>
    
    <button onclick="showAddClosureModal()" 
        class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Closure
    </button>
</div>

<!-- Fixed Weekly Closure Day -->
<div class="admin-card mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="font-semibold text-kampung-charcoal">Fixed Weekly Closure Day</h3>
            <p class="text-sm text-gray-500">Restaurant is closed every week on this day</p>
        </div>
        <div class="flex items-center gap-3">
            <select id="weeklyClosureDay" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent min-w-[160px]">
                <option value="">No fixed day</option>
                <option value="0">Sunday</option>
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
            </select>
            <button onclick="saveWeeklyClosureDay()" class="px-4 py-2 bg-kampung-gold text-white rounded-lg hover:bg-opacity-90">
                Save
            </button>
        </div>
    </div>
</div>

<!-- Specific Closure Dates -->
<div class="mb-4">
    <h3 class="font-semibold text-kampung-charcoal">Specific Closure Dates</h3>
    <p class="text-sm text-gray-500">Add individual holidays or special closure dates</p>
</div>

<!-- Closures List -->
<div id="closuresContainer" class="admin-card">
    <div class="text-center py-12 text-gray-500">Loading closures...</div>
</div>

<!-- Add/Edit Closure Modal -->
<div id="closureModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 shadow-2xl">
        <h3 id="closureModalTitle" class="text-lg font-semibold text-gray-800 mb-4">Add Closure</h3>
        <form id="closureForm" class="space-y-4">
            <input type="hidden" id="closureId" value="">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="closureDate" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <input type="text" id="closureReason" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-gold focus:border-transparent" placeholder="e.g., Chinese New Year, Renovation">
            </div>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="closeClosureModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">Save</button>
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
const API_URL = '<?= BASE_URL ?>/admin/api/closures_api.php';
const SETTINGS_API_URL = '<?= BASE_URL ?>/admin/api/settings_api.php';
let closures = [];
let pendingDeleteId = null;

document.addEventListener('DOMContentLoaded', () => {
    loadClosures();
    loadWeeklyClosureDay();
});

// ============ WEEKLY CLOSURE DAY ============

async function loadWeeklyClosureDay() {
    try {
        const response = await fetch(`${SETTINGS_API_URL}?key=weekly_closure_day`);
        const result = await response.json();
        
        if (result.success && result.data) {
            document.getElementById('weeklyClosureDay').value = result.data.setting_value || '';
        }
    } catch (error) {
        console.error('Error loading weekly closure:', error);
    }
}

async function saveWeeklyClosureDay() {
    const value = document.getElementById('weeklyClosureDay').value;
    
    try {
        const response = await fetch(SETTINGS_API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                setting_key: 'weekly_closure_day',
                setting_value: value
            })
        });
        
        const result = await response.json();
        if (result.success) {
            alert('Weekly closure day saved!');
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save setting');
    }
}

// ============ CLOSURES ============

async function loadClosures() {
    try {
        const response = await fetch(API_URL);
        const result = await response.json();
        
        if (result.success) {
            closures = result.data;
            renderClosures();
        } else {
            showError('Failed to load closures');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error loading closures');
    }
}

function renderClosures() {
    const container = document.getElementById('closuresContainer');
    
    if (closures.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No Closures Scheduled</h3>
                <p class="text-gray-500 mb-4">Add holidays or closure dates here.</p>
                <button onclick="showAddClosureModal()" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">
                    Add First Closure
                </button>
            </div>
        `;
        return;
    }
    
    // Sort by date
    closures.sort((a, b) => new Date(a.closure_date) - new Date(b.closure_date));
    
    container.innerHTML = `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="pb-3 font-semibold text-gray-700">Date</th>
                        <th class="pb-3 font-semibold text-gray-700">Reason</th>
                        <th class="pb-3 font-semibold text-gray-700 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${closures.map(closure => {
                        const date = new Date(closure.closure_date + 'T00:00:00');
                        const formattedDate = date.toLocaleDateString('en-MY', { 
                            weekday: 'short', 
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric' 
                        });
                        const isPast = date < new Date();
                        
                        return `
                            <tr class="border-b last:border-b-0 ${isPast ? 'opacity-50' : ''}">
                                <td class="py-4">
                                    <span class="font-medium">${formattedDate}</span>
                                    ${isPast ? '<span class="ml-2 text-xs text-gray-500">(Past)</span>' : ''}
                                </td>
                                <td class="py-4">
                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-sm">
                                        ${escapeHtml(closure.reason || 'Closed')}
                                    </span>
                                </td>
                                <td class="py-4 text-right">
                                    <button onclick="showEditClosureModal('${closure.id}')" class="p-2 text-gray-500 hover:text-kampung-brown">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDelete('${closure.id}', '${closure.closure_date}')" class="p-2 text-gray-500 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function showAddClosureModal() {
    document.getElementById('closureModalTitle').textContent = 'Add Closure';
    document.getElementById('closureId').value = '';
    document.getElementById('closureDate').value = '';
    document.getElementById('closureReason').value = '';
    openModal('closureModal');
}

function showEditClosureModal(id) {
    const closure = closures.find(c => c.id === id);
    if (!closure) return;
    
    document.getElementById('closureModalTitle').textContent = 'Edit Closure';
    document.getElementById('closureId').value = closure.id;
    document.getElementById('closureDate').value = closure.closure_date;
    document.getElementById('closureReason').value = closure.reason || '';
    openModal('closureModal');
}

function closeClosureModal() {
    closeModal('closureModal');
}

function confirmDelete(id, date) {
    pendingDeleteId = id;
    document.getElementById('deleteMessage').textContent = `Are you sure you want to remove the closure on ${date}?`;
    openModal('deleteModal');
}

function closeDeleteModal() {
    closeModal('deleteModal');
    pendingDeleteId = null;
}

// Form handler
document.getElementById('closureForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('closureId').value;
    const data = {
        closure_date: document.getElementById('closureDate').value,
        reason: document.getElementById('closureReason').value
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
            response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
        }
        
        const result = await response.json();
        if (result.success) {
            closeClosureModal();
            loadClosures();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to save closure');
    }
});

// Delete handler
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
    if (!pendingDeleteId) return;
    
    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: pendingDeleteId })
        });
        
        const result = await response.json();
        if (result.success) {
            closeDeleteModal();
            loadClosures();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to delete');
    }
});

// Utility functions
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
    document.getElementById('closuresContainer').innerHTML = `
        <div class="text-center py-12">
            <p class="text-red-500 mb-4">${escapeHtml(message)}</p>
            <button onclick="loadClosures()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Retry</button>
        </div>
    `;
}

// Close modals on backdrop click
['closureModal', 'deleteModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(id);
            if (id === 'deleteModal') pendingDeleteId = null;
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
