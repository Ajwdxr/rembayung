<?php
/**
 * Rembayung - Landing Page
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/supabase.php';
$pageTitle = 'Home';

$supabase = new Supabase();

// Fetch menu items from database
$menuResult = $supabase->get('menu_items', 'is_active=eq.true&is_featured=eq.true&order=display_order.asc&limit=6');
$menuItems = [];
if ($menuResult['success'] && !empty($menuResult['data'])) {
    $menuItems = $menuResult['data'];
} else {
    // Fallback to default menu items if database is empty
    $menuItems = [
        [
            'name' => 'Nasi Kampung Rembayung',
            'description' => 'Our signature dish. Fragrant rice served with sambal ikan bilis, ayam goreng berempah, and ulam.',
            'price' => 'RM 28',
            'image_filename' => null
        ],
        [
            'name' => 'Rendang Tok',
            'description' => 'Slow-cooked beef rendang using generations-old Perak recipe. Rich and aromatic.',
            'price' => 'RM 38',
            'image_filename' => null
        ],
        [
            'name' => 'Gulai Lemak Ikan Patin',
            'description' => 'Creamy turmeric curry with fresh patin fish and ulam raja.',
            'price' => 'RM 35',
            'image_filename' => null
        ],
        [
            'name' => 'Ayam Percik',
            'description' => 'Grilled chicken marinated in coconut milk and spices, served with nasi kerabu.',
            'price' => 'RM 32',
            'image_filename' => null
        ],
        [
            'name' => 'Sambal Petai Udang',
            'description' => 'Tiger prawns stir-fried with petai beans in fiery sambal belacan.',
            'price' => 'RM 42',
            'image_filename' => null
        ],
        [
            'name' => 'Kuih Kampung Selection',
            'description' => 'Assorted traditional kuih - onde-onde, kuih lapis, seri muka, and more.',
            'price' => 'RM 18',
            'image_filename' => null
        ]
    ];
}

// Fetch about content from database
$aboutResult = $supabase->get('about_content', 'is_active=eq.true&order=display_order.asc&limit=1');
$aboutContent = null;
if ($aboutResult['success'] && !empty($aboutResult['data'])) {
    $aboutContent = $aboutResult['data'][0];
}

// Default placeholder images for items without uploaded images
$defaultImages = [
    'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400',
    'https://images.unsplash.com/photo-1606491956689-2ea866880c84?w=400',
    'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=400',
    'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=400',
    'https://images.unsplash.com/photo-1625943553852-781c6dd46faa?w=400',
    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400'
];
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="relative h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img 
                src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1920" 
                alt="Restaurant ambiance" 
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 batik-pattern opacity-30"></div>
        
        <!-- Content -->
        <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
            <p class="text-kampung-gold font-medium tracking-[0.3em] uppercase mb-4 animate-fadeInUp">
                Est. 2024
            </p>
            <h1 class="font-heading text-6xl md:text-8xl font-bold mb-6 animate-fadeInUp animate-delay-100">
                Rembayung
            </h1>
            <p class="text-xl md:text-2xl font-light mb-8 text-white/90 animate-fadeInUp animate-delay-200">
                Authentic Kampung Cuisine by Khairul Aming
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fadeInUp animate-delay-300">
                <a href="<?= BASE_URL ?>/booking.php" class="btn-primary px-10 py-4 rounded-full bg-kampung-gold text-kampung-charcoal font-semibold text-lg hover:bg-white transition-all duration-300 transform hover:scale-105">
                    Reserve a Table
                </a>
                <a href="#menu" class="px-10 py-4 rounded-full border-2 border-white text-white font-semibold hover:bg-white hover:text-kampung-charcoal transition-all duration-300">
                    Explore Menu
                </a>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-float">
            <svg class="w-6 h-10 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Image -->
                <div class="relative scroll-reveal-left">
                    <div class="aspect-[4/5] rounded-2xl overflow-hidden">
                        <?php 
                        $aboutImage = 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800';
                        if ($aboutContent && !empty($aboutContent['image_filename'])) {
                            $aboutImage = BASE_URL . '/assets/uploads/about/' . $aboutContent['image_filename'];
                        }
                        ?>
                        <img 
                            src="<?= $aboutImage ?>" 
                            alt="Chef preparing food" 
                            class="w-full h-full object-cover"
                        >
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -bottom-8 -right-8 w-48 h-48 border-4 border-kampung-gold rounded-2xl -z-10"></div>
                </div>
                
                <!-- Content -->
                <div class="scroll-reveal-right">
                    <p class="text-kampung-gold font-medium tracking-wider uppercase mb-4">Our Story</p>
                    <h2 class="font-heading text-4xl md:text-5xl font-bold text-kampung-charcoal mb-6">
                        <?= $aboutContent ? htmlspecialchars($aboutContent['title']) : 'From Kampung to <br>City, With Love' ?>
                    </h2>
                    <div class="space-y-4 text-gray-600 leading-relaxed">
                        <?php if ($aboutContent && !empty($aboutContent['description'])): ?>
                            <?php foreach (explode("\n\n", $aboutContent['description']) as $paragraph): ?>
                            <p><?= nl2br(htmlspecialchars($paragraph)) ?></p>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <p>
                            Rembayung is born from a deep love for Malaysian heritage cuisine. Founded by Khairul Aming, 
                            a culinary storyteller who has touched millions of hearts through his authentic recipes and 
                            warm personality.
                        </p>
                        <p>
                            The name "Rembayung" evokes the gentle warmth of twilight in the kampung - that magical 
                            moment when families gather, smoke rises from wood-fire kitchens, and the aroma of 
                            home-cooked meals fills the air.
                        </p>
                        <p>
                            Every dish we serve carries the essence of Malaysian kampung cooking - bold flavors, 
                            traditional techniques, and recipes passed down through generations, presented with 
                            modern elegance.
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Signature -->
                    <div class="mt-8 pt-8 border-t border-gray-100">
                        <p class="font-heading text-2xl text-kampung-brown italic">
                            "<?= $aboutContent && !empty($aboutContent['quote']) ? htmlspecialchars($aboutContent['quote']) : 'Masak dengan hati, hidang dengan kasih.' ?>"
                        </p>
                        <p class="text-gray-500 mt-2">— <?= $aboutContent && !empty($aboutContent['quote_author']) ? htmlspecialchars($aboutContent['quote_author']) : 'Khairul Aming' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Decorative Divider -->
    <div class="songket-border"></div>
    
    <!-- Menu Section -->
    <section id="menu" class="py-24 bg-kampung-cream-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-16 scroll-reveal">
                <p class="text-kampung-gold font-medium tracking-wider uppercase mb-4">Our Menu</p>
                <h2 class="font-heading text-4xl md:text-5xl font-bold text-kampung-charcoal mb-6">
                    Signature Dishes
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Each dish tells a story of our heritage, crafted with the finest ingredients 
                    and time-honored techniques from the kampung kitchen.
                </p>
            </div>
            
            <!-- Menu Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($menuItems as $index => $item): 
                    // Determine image source
                    $menuImage = $defaultImages[$index % count($defaultImages)];
                    if (!empty($item['image_filename'])) {
                        $menuImage = BASE_URL . '/assets/uploads/menu/' . $item['image_filename'];
                    }
                ?>
                <div class="menu-card relative bg-white rounded-2xl overflow-hidden shadow-lg scroll-reveal stagger-<?= ($index % 3) + 1 ?>">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img 
                            src="<?= $menuImage ?>" 
                            alt="<?= htmlspecialchars($item['name']) ?>" 
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                        >
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-heading text-xl font-semibold text-kampung-charcoal">
                                <?= htmlspecialchars($item['name']) ?>
                            </h3>
                            <span class="text-kampung-gold font-semibold"><?= htmlspecialchars($item['price']) ?></span>
                        </div>
                        <p class="text-gray-500 text-sm">
                            <?= htmlspecialchars($item['description']) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- CTA -->
            <div class="text-center mt-12">
                <p class="text-gray-600 mb-4">Ready to experience authentic kampung flavors?</p>
                <a href="<?= BASE_URL ?>/booking.php" class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-kampung-brown text-white font-semibold hover:bg-kampung-brown-dark transition-all duration-300 transform hover:scale-105">
                    <span>Make a Reservation</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    
    <!-- Location Section -->
    <section id="location" class="py-24 bg-kampung-charcoal text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Info -->
                <div class="scroll-reveal-left">
                    <p class="text-kampung-gold font-medium tracking-wider uppercase mb-4">Visit Us</p>
                    <h2 class="font-heading text-4xl md:text-5xl font-bold mb-8">
                        Find Us in the <br>Heart of Kampung Baru
                    </h2>
                    
                    <div class="space-y-6">
                        <!-- Address -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-1">Address</h3>
                                <p class="text-gray-400"><?= RESTAURANT_ADDRESS ?></p>
                            </div>
                        </div>
                        
                        <!-- Hours -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-1">Operating Hours</h3>
                                <p class="text-gray-400">Lunch: 11:30 AM - 3:00 PM</p>
                                <p class="text-gray-400">Dinner: 6:00 PM - 10:00 PM</p>
                                <p class="text-kampung-gold mt-1">Closed on Mondays</p>
                            </div>
                        </div>
                        
                        <!-- Contact -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg mb-1">Reservations</h3>
                                <p class="text-gray-400"><?= RESTAURANT_PHONE ?></p>
                                <p class="text-gray-400"><?= RESTAURANT_EMAIL ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Map -->
                <div class="scroll-reveal-right">
                    <div class="rounded-2xl overflow-hidden h-[400px] lg:h-full min-h-[400px]">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.7534!2d101.698!3d3.1569!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zM8KwMDknMjQuOCJOIDEwMcKwNDEnNTMuMyJF!5e0!3m2!1sen!2smy!4v1600000000000!5m2!1sen!2smy" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="grayscale hover:grayscale-0 transition-all duration-500"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-24 bg-kampung-brown relative overflow-hidden">
        <!-- Pattern -->
        <div class="absolute inset-0 batik-pattern opacity-10"></div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 scroll-reveal-scale">
            <h2 class="font-heading text-4xl md:text-5xl font-bold text-white mb-6">
                Ready to Dine <br>at Rembayung?
            </h2>
            <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                Join us for an unforgettable culinary journey through Malaysian heritage. 
                Reserve your table today and taste the warmth of kampung cooking.
            </p>
            <a href="<?= BASE_URL ?>/booking.php" class="inline-flex items-center gap-3 px-10 py-5 rounded-full bg-white text-kampung-brown font-semibold text-lg hover:bg-kampung-gold hover:text-white transition-all duration-300 transform hover:scale-105">
                <span>Reserve Your Table</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>
    </section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
