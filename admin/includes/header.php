<?php
/**
 * Admin Header
 */
require_once __DIR__ . '/../../includes/config.php';

if (!isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/login.php');
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>Admin - Rembayung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-gray-50 font-body">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 admin-sidebar flex-shrink-0 fixed inset-y-0 left-0 z-30 overflow-y-auto">
            <div class="p-6">
                <h1 class="font-heading text-2xl font-semibold text-kampung-gold">Rembayung</h1>
                <p class="text-gray-500 text-sm">Admin Console</p>
            </div>
            
            <nav class="px-4 space-y-1">
                <a href="<?= BASE_URL ?>/admin/dashboard.php" 
                   class="admin-nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/bookings.php" 
                   class="admin-nav-link <?= $currentPage === 'bookings' ? 'active' : '' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Bookings
                </a>
                <a href="<?= BASE_URL ?>/admin/sessions.php" 
                   class="admin-nav-link <?= $currentPage === 'sessions' ? 'active' : '' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Sessions
                </a>
                <a href="<?= BASE_URL ?>/admin/closures.php" 
                   class="admin-nav-link <?= $currentPage === 'closures' ? 'active' : '' ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    Closures
                </a>
            </nav>
            
            <div class="absolute bottom-0 left-0 w-64 p-4 border-t border-white/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-kampung-gold flex items-center justify-center text-white font-semibold text-sm">
                            <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                        </div>
                        <span class="ml-3 text-gray-300 text-sm"><?= $_SESSION['admin_name'] ?? 'Admin' ?></span>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/logout.php" class="text-gray-500 hover:text-red-400" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 ml-64 p-8 overflow-y-auto">
