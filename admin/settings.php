<?php

/**
 * Admin Settings - Password Change
 */
$pageTitle = 'Settings';
require_once __DIR__ . '/includes/header.php';
?>

<div class="mb-8">
    <h1 class="text-2xl font-semibold text-kampung-charcoal">Settings</h1>
    <p class="text-gray-500">Manage your account settings</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Change Password Card -->
    <div class="admin-card">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-kampung-charcoal flex items-center">
                <svg class="w-5 h-5 mr-2 text-kampung-brown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                Change Password
            </h2>
            <p class="text-gray-500 text-sm mt-1">Update your account password</p>
        </div>

        <!-- Alert Messages -->
        <div id="alertSuccess" class="hidden bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span id="successMessage">Password changed successfully!</span>
            </div>
        </div>

        <div id="alertError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span id="errorMessage"></span>
            </div>
        </div>

        <form id="passwordForm" class="space-y-5">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="current_password" name="current_password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent pr-10"
                        placeholder="Enter current password">
                    <button type="button" onclick="togglePassword('current_password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                    New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="new_password" name="new_password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent pr-10"
                        placeholder="Enter new password (min 6 characters)">
                    <button type="button" onclick="togglePassword('new_password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent pr-10"
                        placeholder="Confirm new password">
                    <button type="button" onclick="togglePassword('confirm_password')"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" id="submitBtn"
                    class="w-full py-3 bg-kampung-brown text-white rounded-lg font-semibold hover:bg-opacity-90 transition-all flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- Account Info Card -->
    <div class="admin-card h-fit">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-kampung-charcoal flex items-center">
                <svg class="w-5 h-5 mr-2 text-kampung-brown" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Account Information
            </h2>
        </div>

        <div class="space-y-4">
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 rounded-full bg-kampung-gold flex items-center justify-center text-white font-bold text-xl">
                    <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="ml-4">
                    <p class="font-semibold text-kampung-charcoal"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></p>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?></p>
                </div>
            </div>

            <div class="border-t pt-4">
                <p class="text-sm text-gray-500 mb-2">Security Tips:</p>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Use a strong, unique password
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Include numbers and special characters
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Change your password periodically
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === 'password' ? 'text' : 'password';
    }

    // Form submission
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const alertSuccess = document.getElementById('alertSuccess');
        const alertError = document.getElementById('alertError');
        const errorMessage = document.getElementById('errorMessage');

        // Hide previous alerts
        alertSuccess.classList.add('hidden');
        alertError.classList.add('hidden');

        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Client-side validation
        if (newPassword !== confirmPassword) {
            errorMessage.textContent = 'New passwords do not match';
            alertError.classList.remove('hidden');
            return;
        }

        if (newPassword.length < 6) {
            errorMessage.textContent = 'Password must be at least 6 characters';
            alertError.classList.remove('hidden');
            return;
        }

        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
        <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Updating...
    `;

        try {
            const response = await fetch('<?= BASE_URL ?>/admin/api/change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                })
            });

            const data = await response.json();

            if (data.success) {
                alertSuccess.classList.remove('hidden');
                document.getElementById('passwordForm').reset();
            } else {
                errorMessage.textContent = data.error || 'Failed to change password';
                alertError.classList.remove('hidden');
            }
        } catch (error) {
            errorMessage.textContent = 'Network error. Please try again.';
            alertError.classList.remove('hidden');
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Update Password
        `;
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>