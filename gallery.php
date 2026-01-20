<?php
/**
 * Rembayung - Gallery Page
 */
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Gallery';

$galleryImages = [
    ['src' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800', 'alt' => 'Restaurant interior', 'category' => 'ambiance'],
    ['src' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800', 'alt' => 'Dining area', 'category' => 'ambiance'],
    ['src' => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=800', 'alt' => 'Nasi Kampung', 'category' => 'food'],
    ['src' => 'https://images.unsplash.com/photo-1606491956689-2ea866880c84?w=800', 'alt' => 'Rendang Tok', 'category' => 'food'],
    ['src' => 'https://images.unsplash.com/photo-1455619452474-d2be8b1e70cd?w=800', 'alt' => 'Traditional curry', 'category' => 'food'],
    ['src' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=800', 'alt' => 'Grilled chicken', 'category' => 'food'],
    ['src' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800', 'alt' => 'Bar area', 'category' => 'ambiance'],
    ['src' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=800', 'alt' => 'Chef preparing', 'category' => 'kitchen'],
    ['src' => 'https://images.unsplash.com/photo-1625943553852-781c6dd46faa?w=800', 'alt' => 'Sambal prawns', 'category' => 'food'],
];
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

    <section class="pt-32 pb-16 bg-kampung-charcoal text-white relative overflow-hidden">
        <div class="absolute inset-0 batik-pattern opacity-10"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <p class="text-kampung-gold font-medium tracking-wider uppercase mb-4 animate-fadeInUp">Gallery</p>
            <h1 class="font-heading text-5xl md:text-6xl font-bold mb-6 animate-fadeInUp animate-delay-100">A Visual Journey</h1>
            <p class="text-white/80 text-lg animate-fadeInUp animate-delay-200">Explore the essence of Rembayung through our lens.</p>
        </div>
    </section>
    
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4 mb-12 scroll-reveal-scale">
                <button class="gallery-filter active px-6 py-2 rounded-full border-2 border-kampung-brown bg-kampung-brown text-white font-medium transition-all" data-filter="all">All</button>
                <button class="gallery-filter px-6 py-2 rounded-full border-2 border-kampung-brown text-kampung-brown font-medium hover:bg-kampung-brown hover:text-white transition-all" data-filter="food">Food</button>
                <button class="gallery-filter px-6 py-2 rounded-full border-2 border-kampung-brown text-kampung-brown font-medium hover:bg-kampung-brown hover:text-white transition-all" data-filter="ambiance">Ambiance</button>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="galleryGrid">
                <?php foreach ($galleryImages as $index => $image): ?>
                <div class="gallery-item cursor-pointer scroll-reveal stagger-<?= ($index % 3) + 1 ?>" data-category="<?= $image['category'] ?>">
                    <img src="<?= $image['src'] ?>" alt="<?= $image['alt'] ?>" class="w-full h-64 object-cover" loading="lazy">
                    <div class="overlay"><p class="text-white font-medium"><?= $image['alt'] ?></p></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <script>
        document.querySelectorAll('.gallery-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.gallery-filter').forEach(b => b.classList.remove('active', 'bg-kampung-brown', 'text-white'));
                btn.classList.add('active', 'bg-kampung-brown', 'text-white');
                const filter = btn.dataset.filter;
                document.querySelectorAll('.gallery-item').forEach(item => {
                    item.style.display = (filter === 'all' || item.dataset.category === filter) ? '' : 'none';
                });
            });
        });
    </script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
