<?php

/**
 * Admin Table Management
 */
$pageTitle = 'Tables';
require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-semibold text-kampung-charcoal">Table Management</h1>
        <p class="text-gray-500">Manage cafe seating and capacity</p>
    </div>

    <button onclick="openModal()" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90 transition-all flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Add Table
    </button>
</div>

<!-- Tables Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Floor 1 -->
    <div class="admin-card">
        <h2 class="text-lg font-semibold text-kampung-charcoal mb-4 flex items-center">
            <span class="w-8 h-8 rounded-full bg-kampung-gold text-white flex items-center justify-center mr-3 text-sm">G</span>
            Ground Floor
        </h2>
        <div id="floor1-list" class="space-y-3">
            <div class="animate-pulse flex space-x-4">
                <div class="h-12 bg-gray-200 rounded w-full"></div>
            </div>
        </div>
    </div>

    <!-- Floor 2 -->
    <div class="admin-card">
        <h2 class="text-lg font-semibold text-kampung-charcoal mb-4 flex items-center">
            <span class="w-8 h-8 rounded-full bg-kampung-gold text-white flex items-center justify-center mr-3 text-sm">1</span>
            First Floor (Rooftop)
        </h2>
        <div id="floor2-list" class="space-y-3">
            <div class="animate-pulse flex space-x-4">
                <div class="h-12 bg-gray-200 rounded w-full"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="tableModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl transform transition-all">
        <h3 id="modalTitle" class="text-lg font-semibold text-gray-800 mb-6">Add New Table</h3>

        <form id="tableForm" class="space-y-4">
            <input type="hidden" id="tableId" name="id">

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Table Name/Number</label>
                <input type="text" id="tableName" name="name" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"
                    placeholder="e.g. T1, G-05">
            </div>

            <!-- Floor -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                <select id="tableFloor" name="floor" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                    <option value="1">Ground Floor</option>
                    <option value="2">First Floor (Rooftop)</option>
                </select>
            </div>

            <!-- Capacity Row -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Pax</label>
                    <input type="number" id="minPax" name="min_pax" min="1" value="2" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Pax</label>
                    <input type="number" id="maxPax" name="max_pax" min="1" value="4" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="tableStatus" name="status"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                    <option value="active">Active</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="flex gap-3 justify-end pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-opacity-90">Save Table</button>
            </div>
        </form>
    </div>
</div>

<script>
    let tables = [];

    document.addEventListener('DOMContentLoaded', loadTables);

    async function loadTables() {
        try {
            const response = await fetch('<?= BASE_URL ?>/admin/api/tables.php');
            const result = await response.json();

            if (result.success) {
                tables = result.data;
                renderTables();
            }
        } catch (error) {
            console.error('Error loading tables:', error);
        }
    }

    function renderTables() {
        const floor1 = document.getElementById('floor1-list');
        const floor2 = document.getElementById('floor2-list');

        floor1.innerHTML = '';
        floor2.innerHTML = '';

        const f1Tables = tables.filter(t => t.floor === 1);
        const f2Tables = tables.filter(t => t.floor === 2);

        if (f1Tables.length === 0) floor1.innerHTML = '<p class="text-gray-400 text-sm italic">No tables yet.</p>';
        if (f2Tables.length === 0) floor2.innerHTML = '<p class="text-gray-400 text-sm italic">No tables yet.</p>';

        f1Tables.forEach(t => floor1.appendChild(createTableCard(t)));
        f2Tables.forEach(t => floor2.appendChild(createTableCard(t)));
    }

    function createTableCard(table) {
        const div = document.createElement('div');
        div.className = `p-4 border rounded-lg flex items-center justify-between hover:shadow-md transition-shadow ${table.status !== 'active' ? 'bg-gray-50 opacity-75' : 'bg-white'}`;

        let statusBadge = '';
        if (table.status !== 'active') {
            const color = table.status === 'maintenance' ? 'text-orange-600 bg-orange-100' : 'text-gray-600 bg-gray-200';
            statusBadge = `<span class="text-xs px-2 py-0.5 rounded-full ${color} ml-2 capitalize">${table.status}</span>`;
        }

        div.innerHTML = `
        <div>
            <div class="flex items-center">
                <span class="font-semibold text-gray-800">${table.name}</span>
                ${statusBadge}
            </div>
            <div class="text-sm text-gray-500 mt-1">
                Capacity: <span class="text-kampung-brown font-medium">${table.min_pax}-${table.max_pax} pax</span>
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick='editTable(${JSON.stringify(table)})' class="p-2 text-gray-400 hover:text-kampung-brown transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
            </button>
            <button onclick="deleteTable(${table.id})" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    `;
        return div;
    }

    // Modal Functions
    const modal = document.getElementById('tableModal');
    const form = document.getElementById('tableForm');

    function openModal() {
        form.reset();
        document.getElementById('tableId').value = '';
        document.getElementById('modalTitle').textContent = 'Add New Table';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editTable(table) {
        document.getElementById('tableId').value = table.id;
        document.getElementById('tableName').value = table.name;
        document.getElementById('tableFloor').value = table.floor;
        document.getElementById('minPax').value = table.min_pax;
        document.getElementById('maxPax').value = table.max_pax;
        document.getElementById('tableStatus').value = table.status;

        document.getElementById('modalTitle').textContent = 'Edit Table';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Form Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('tableId').value;
        const url = '<?= BASE_URL ?>/admin/api/tables.php';
        const method = id ? 'PUT' : 'POST';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Convert to numbers
        data.floor = parseInt(data.floor);
        data.min_pax = parseInt(data.min_pax);
        data.max_pax = parseInt(data.max_pax);
        if (id) data.id = parseInt(id);

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                closeModal();
                loadTables(); // Refresh list
            } else {
                alert('Error: ' + result.error);
            }
        } catch (error) {
            console.error('Error saving table:', error);
            alert('Failed to save table');
        }
    });

    // Delete Table
    async function deleteTable(id) {
        if (!confirm('Are you sure you want to delete this table?')) return;

        try {
            const response = await fetch(`<?= BASE_URL ?>/admin/api/tables.php?id=${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                loadTables();
            } else {
                alert('Error: ' + (result.error || 'Failed to delete'));
            }
        } catch (error) {
            console.error('Error deleting table:', error);
            alert('Failed to delete table');
        }
    }

    // Close modal on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>