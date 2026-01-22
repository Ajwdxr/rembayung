<?php

/**
 * Rembayung - Contact Page
 */
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Contact';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<section class="pt-32 pb-16 bg-kampung-charcoal text-white relative overflow-hidden">
    <div class="absolute inset-0 batik-pattern opacity-10"></div>
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <p class="text-kampung-gold font-medium tracking-wider uppercase mb-4">Contact</p>
        <h1 class="font-heading text-5xl md:text-6xl font-bold mb-6">Get in Touch</h1>
        <p class="text-white/80 text-lg">We'd love to hear from you. Reach out for reservations, inquiries, or feedback.</p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Contact Info -->
            <div>
                <h2 class="font-heading text-3xl font-bold text-kampung-charcoal mb-8">Contact Information</h2>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Address</h3>
                            <p class="text-gray-600"><?= RESTAURANT_ADDRESS ?></p>
                        </div>
                    </div>

                    <!-- <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Phone</h3>
                            <p class="text-gray-600"><?= RESTAURANT_PHONE ?></p>
                        </div>
                    </div> -->

                    <!-- <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Email</h3>
                            <p class="text-gray-600"><?= RESTAURANT_EMAIL ?></p>
                        </div>
                    </div> -->

                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-kampung-gold/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-kampung-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-lg mb-1">Hours</h3>
                            <p class="text-gray-600">Daily: 11:00 AM - 11:00 PM</p>
                            <p class="text-kampung-gold font-medium mt-1">Closed on <?= CLOSED_DAY ?>s</p>
                        </div>
                    </div>
                </div>

                <!-- Social -->
                <div class="mt-10">
                    <h3 class="font-semibold text-lg mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <!-- Instagram -->
                        <a href="https://www.instagram.com/rembayungmy/" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-kampung-gold transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <!-- TikTok -->
                        <a href="https://www.tiktok.com/@rembayungmy" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-kampung-gold transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div class="rounded-2xl overflow-hidden h-[500px]">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1184.367956368523!2d101.70371841891945!3d3.1674659585802214!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc490029136eb3%3A0x5435818f65486218!2sRembayung!5e0!3m2!1sen!2smy!4v1769096630040!5m2!1sen!2smy"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>