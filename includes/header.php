<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Rembayung - Authentic Kampung Cuisine by Khairul Aming. Experience the taste of Malaysian heritage in modern elegance.">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/favicon.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'kampung': {
                            'brown': '#8B4513',
                            'brown-dark': '#6B3410',
                            'charcoal': '#2C2C2C',
                            'gold': '#D4AF37',
                            'cream': '#FFF8F0',
                            'cream-dark': '#F5EBE0'
                        },
                        'theme': {
                            'primary': 'var(--color-primary)',
                            'secondary': 'var(--color-secondary)',
                            'bg': 'var(--color-background)',
                            'text': 'var(--color-text)'
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
    
    <!-- Theme System -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/themes.css">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="bg-kampung-cream font-body text-kampung-charcoal" data-theme="modern-malaysian">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>/" class="flex items-center">
                    <img src="<?= BASE_URL ?>/assets/images/logo.webp" alt="Rembayung" class="h-10 md:h-12 nav-logo-img">
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?= BASE_URL ?>/" class="nav-link nav-link-themed transition-colors">Home</a>
                    <a href="<?= BASE_URL ?>/#about" class="nav-link nav-link-themed transition-colors">Our Story</a>
                    <a href="<?= BASE_URL ?>/#menu" class="nav-link nav-link-themed transition-colors">Menu</a>
                    <a href="<?= BASE_URL ?>/gallery.php" class="nav-link nav-link-themed transition-colors">Gallery</a>
                    <a href="<?= BASE_URL ?>/contact.php" class="nav-link nav-link-themed transition-colors">Contact</a>
                    <a href="<?= BASE_URL ?>/booking.php" class="btn-primary btn-primary-themed px-6 py-2.5 rounded-full transition-all duration-300 transform hover:scale-105">
                        Reserve a Table
                    </a>
                    
                    <!-- Theme Switcher -->
                    <div class="theme-switcher" id="themeSwitcher">
                        <button class="theme-switcher-btn" id="themeSwitcherBtn" aria-label="Change theme">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </button>
                        <div class="theme-panel" id="themePanel">
                            <p class="theme-panel-title">Choose Theme</p>
                            <div class="theme-option active" data-theme="modern-malaysian">
                                <div class="theme-swatch swatch-modern-malaysian">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="theme-option-label">Modern Malaysian</span>
                            </div>
                            <div class="theme-option" data-theme="rustic-elegance">
                                <div class="theme-swatch swatch-rustic-elegance">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="theme-option-label">Rustic Elegance</span>
                            </div>
                            <div class="theme-option" data-theme="editorial-food">
                                <div class="theme-swatch swatch-editorial-food">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="theme-option-label">Editorial Food</span>
                            </div>
                            <div class="theme-option" data-theme="dark-moody">
                                <div class="theme-swatch swatch-dark-moody">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="theme-option-label">Dark & Moody</span>
                            </div>
                            <div class="theme-option" data-theme="signature-rembayung">
                                <div class="theme-swatch swatch-signature-rembayung">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="theme-option-label">Signature Rembayung</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden p-2 mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div class="md:hidden hidden mobile-menu-panel backdrop-blur-md border-t" id="mobileMenu">
            <div class="px-4 py-6 space-y-4">
                <a href="<?= BASE_URL ?>/" class="block mobile-nav-link py-2">Home</a>
                <a href="<?= BASE_URL ?>/#about" class="block mobile-nav-link py-2">Our Story</a>
                <a href="<?= BASE_URL ?>/#menu" class="block mobile-nav-link py-2">Menu</a>
                <a href="<?= BASE_URL ?>/gallery.php" class="block mobile-nav-link py-2">Gallery</a>
                <a href="<?= BASE_URL ?>/contact.php" class="block mobile-nav-link py-2">Contact</a>
                <a href="<?= BASE_URL ?>/booking.php" class="block w-full text-center btn-primary-themed py-3 rounded-full transition-colors">
                    Reserve a Table
                </a>
                
                <!-- Mobile Theme Switcher -->
                <div class="pt-4 border-t mobile-theme-section-border">
                    <p class="text-sm font-semibold mobile-theme-label mb-3">Choose Theme</p>
                    <div class="flex flex-wrap gap-2" id="mobileThemeOptions">
                        <button class="mobile-theme-btn active px-3 py-2 rounded-lg text-sm font-medium border-2" data-theme="modern-malaysian">🇲🇾 Malaysian</button>
                        <button class="mobile-theme-btn px-3 py-2 rounded-lg text-sm font-medium border-2" data-theme="rustic-elegance">🪵 Rustic</button>
                        <button class="mobile-theme-btn px-3 py-2 rounded-lg text-sm font-medium border-2" data-theme="editorial-food">📰 Editorial</button>
                        <button class="mobile-theme-btn px-3 py-2 rounded-lg text-sm font-medium border-2" data-theme="dark-moody">🌙 Dark</button>
                        <button class="mobile-theme-btn px-3 py-2 rounded-lg text-sm font-medium border-2" data-theme="signature-rembayung">⭐ Signature</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
