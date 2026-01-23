<?php

/**
 * Admin Login
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/supabase.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $supabase = new Supabase();
        $result = $supabase->get('admins', 'email=eq.' . urlencode($email) . '&select=*');

        if ($result['success'] && !empty($result['data'])) {
            $admin = $result['data'][0];

            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['name'] ?? 'Admin';
                redirect(BASE_URL . '/admin/dashboard.php');
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Rembayung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kampung': {
                            'brown': '#8B4513',
                            'charcoal': '#2C2C2C',
                            'gold': '#D4AF37',
                            'cream': '#FFF8F0'
                        }
                    },
                    fontFamily: {
                        'heading': ['Cormorant Garamond', 'serif'],
                        'body': ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-kampung-charcoal font-body flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="font-heading text-4xl font-bold text-kampung-gold">Rembayung</h1>
            <p class="text-gray-400 mt-2">Admin Console</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl p-8 shadow-2xl">
            <h2 class="text-2xl font-semibold text-kampung-charcoal mb-6">Sign In</h2>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"
                        placeholder="admin@rembayung.my">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"
                        placeholder="••••••••">
                </div>

                <p class="text-sm text-gray-500 mt-2">The email and password is admin@rembayung.my and admin123. <br> this is for demo purposes only</p>

                <button type="submit"
                    class="w-full py-3 bg-kampung-brown text-white rounded-lg font-semibold hover:bg-opacity-90 transition-all">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
            <a href="<?= BASE_URL ?>/" class="hover:text-kampung-gold transition-colors">← Back to Website</a>
        </p>
    </div>
</body>

</html>