<?php
/**
 * Admin Content Management Page
 * Manage About, Menu, and Gallery content
 */
$pageTitle = 'Content Management';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../includes/supabase.php';

$supabase = new Supabase();

// Get current tab
$activeTab = $_GET['tab'] ?? 'about';

// Fetch data based on active tab
$aboutData = [];
$menuData = [];
$galleryData = [];

$aboutResult = $supabase->get('about_content', 'order=display_order.asc');
$aboutData = $aboutResult['success'] ? $aboutResult['data'] : [];

$menuResult = $supabase->get('menu_items', 'order=display_order.asc');
$menuData = $menuResult['success'] ? $menuResult['data'] : [];

$galleryResult = $supabase->get('gallery_images', 'order=display_order.asc');
$galleryData = $galleryResult['success'] ? $galleryResult['data'] : [];
?>

<div class="mb-8">
    <h1 class="text-2xl font-semibold text-kampung-charcoal">Content Management</h1>
    <p class="text-gray-500">Manage your website's About, Menu, and Gallery content</p>
</div>

<!-- Tabs -->
<div class="flex space-x-1 mb-6 bg-gray-100 p-1 rounded-lg w-fit">
    <a href="?tab=about" 
       class="px-6 py-2 rounded-md text-sm font-medium transition-all <?= $activeTab === 'about' ? 'bg-white shadow text-kampung-brown' : 'text-gray-600 hover:text-kampung-brown' ?>">
        About Section
    </a>
    <a href="?tab=menu" 
       class="px-6 py-2 rounded-md text-sm font-medium transition-all <?= $activeTab === 'menu' ? 'bg-white shadow text-kampung-brown' : 'text-gray-600 hover:text-kampung-brown' ?>">
        Menu Items
    </a>
    <a href="?tab=gallery" 
       class="px-6 py-2 rounded-md text-sm font-medium transition-all <?= $activeTab === 'gallery' ? 'bg-white shadow text-kampung-brown' : 'text-gray-600 hover:text-kampung-brown' ?>">
        Gallery
    </a>
</div>

<!-- About Section Tab -->
<div id="about-tab" class="<?= $activeTab !== 'about' ? 'hidden' : '' ?>">
    <div class="admin-card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-kampung-charcoal">About Section Content</h2>
            <button onclick="openAboutModal()" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-kampung-brown/90 transition-colors">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Content
            </button>
        </div>
        
        <?php if (empty($aboutData)): ?>
        <p class="text-gray-500 text-center py-8">No about content added yet</p>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($aboutData as $about): ?>
            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex gap-4">
                    <?php if ($about['image_filename']): ?>
                    <img src="<?= BASE_URL ?>/assets/uploads/about/<?= htmlspecialchars($about['image_filename']) ?>" 
                         alt="About image" class="w-32 h-32 object-cover rounded-lg">
                    <?php else: ?>
                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <h3 class="font-semibold text-kampung-charcoal"><?= htmlspecialchars($about['title']) ?></h3>
                        <p class="text-gray-600 text-sm mt-1 line-clamp-3"><?= htmlspecialchars(substr($about['description'], 0, 200)) ?>...</p>
                        <?php if ($about['quote']): ?>
                        <p class="text-kampung-brown italic text-sm mt-2">"<?= htmlspecialchars($about['quote']) ?>"</p>
                        <?php endif; ?>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button onclick="editAbout('<?= $about['id'] ?>')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button onclick="deleteContent('about_content', '<?= $about['id'] ?>')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Menu Items Tab -->
<div id="menu-tab" class="<?= $activeTab !== 'menu' ? 'hidden' : '' ?>">
    <div class="admin-card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-kampung-charcoal">Menu Items</h2>
            <button onclick="openMenuModal()" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-kampung-brown/90 transition-colors">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Menu Item
            </button>
        </div>
        
        <?php if (empty($menuData)): ?>
        <p class="text-gray-500 text-center py-8">No menu items added yet</p>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($menuData as $item): ?>
            <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                <?php if ($item['image_filename']): ?>
                <img src="<?= BASE_URL ?>/assets/uploads/menu/<?= htmlspecialchars($item['image_filename']) ?>" 
                     alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-40 object-cover">
                <?php else: ?>
                <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <?php endif; ?>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-kampung-charcoal"><?= htmlspecialchars($item['name']) ?></h3>
                        <span class="text-kampung-gold font-semibold"><?= htmlspecialchars($item['price']) ?></span>
                    </div>
                    <p class="text-gray-500 text-sm mb-3 line-clamp-2"><?= htmlspecialchars($item['description']) ?></p>
                    <div class="flex justify-between items-center">
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded capitalize"><?= htmlspecialchars($item['category']) ?></span>
                        <div class="flex gap-1">
                            <button onclick="editMenu('<?= $item['id'] ?>')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteContent('menu_items', '<?= $item['id'] ?>')" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Gallery Tab -->
<div id="gallery-tab" class="<?= $activeTab !== 'gallery' ? 'hidden' : '' ?>">
    <div class="admin-card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-kampung-charcoal">Gallery Images</h2>
            <button onclick="openGalleryModal()" class="px-4 py-2 bg-kampung-brown text-white rounded-lg hover:bg-kampung-brown/90 transition-colors">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Image
            </button>
        </div>
        
        <?php if (empty($galleryData)): ?>
        <p class="text-gray-500 text-center py-8">No gallery images added yet</p>
        <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($galleryData as $image): ?>
            <div class="relative group rounded-lg overflow-hidden">
                <?php if ($image['image_filename']): ?>
                <img src="<?= BASE_URL ?>/assets/uploads/gallery/<?= htmlspecialchars($image['image_filename']) ?>" 
                     alt="<?= htmlspecialchars($image['alt_text']) ?>" class="w-full h-40 object-cover">
                <?php else: ?>
                <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                    <button onclick="editGallery('<?= $image['id'] ?>')" class="p-2 bg-white text-blue-600 rounded-full hover:bg-blue-50" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteContent('gallery_images', '<?= $image['id'] ?>')" class="p-2 bg-white text-red-600 rounded-full hover:bg-red-50" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-2">
                    <p class="truncate"><?= htmlspecialchars($image['alt_text']) ?></p>
                    <span class="text-white/70 capitalize"><?= htmlspecialchars($image['category']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- About Modal -->
<div id="aboutModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b sticky top-0 bg-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-kampung-charcoal" id="aboutModalTitle">Add About Content</h3>
                <button onclick="closeModal('aboutModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="aboutForm" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="id" id="aboutId">
            <input type="hidden" name="type" value="about">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" id="aboutTitle" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" id="aboutDescription" rows="5" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quote</label>
                <input type="text" name="quote" id="aboutQuote"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quote Author</label>
                <input type="text" name="quote_author" id="aboutQuoteAuthor"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                <div id="aboutImagePreview" class="mb-2 hidden">
                    <img id="aboutImagePreviewImg" src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg">
                </div>
                <input type="file" name="image" id="aboutImage" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"
                       onchange="previewImage(this, 'aboutImagePreviewImg', 'aboutImagePreview')">
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x1000px, JPG/PNG/WebP</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" id="aboutDisplayOrder" value="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal('aboutModal')" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-kampung-brown text-white rounded-lg hover:bg-kampung-brown/90">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Menu Modal -->
<div id="menuModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b sticky top-0 bg-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-kampung-charcoal" id="menuModalTitle">Add Menu Item</h3>
                <button onclick="closeModal('menuModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="menuForm" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="id" id="menuId">
            <input type="hidden" name="type" value="menu">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" id="menuName" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" id="menuDescription" rows="3" required
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="text" name="price" id="menuPrice" required placeholder="RM 28"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="menuCategory"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                        <option value="signature">Signature</option>
                        <option value="main">Main</option>
                        <option value="appetizer">Appetizer</option>
                        <option value="dessert">Dessert</option>
                        <option value="beverage">Beverage</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                <div id="menuImagePreview" class="mb-2 hidden">
                    <img id="menuImagePreviewImg" src="" alt="Preview" class="w-32 h-24 object-cover rounded-lg">
                </div>
                <input type="file" name="image" id="menuImage" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"
                       onchange="previewImage(this, 'menuImagePreviewImg', 'menuImagePreview')">
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x600px, JPG/PNG/WebP</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="display_order" id="menuDisplayOrder" value="0"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                </div>
                <div class="flex items-center pt-6">
                    <input type="checkbox" name="is_featured" id="menuIsFeatured" value="1" class="mr-2">
                    <label for="menuIsFeatured" class="text-sm text-gray-700">Featured on homepage</label>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal('menuModal')" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-kampung-brown text-white rounded-lg hover:bg-kampung-brown/90">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b sticky top-0 bg-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold text-kampung-charcoal" id="galleryModalTitle">Add Gallery Image</h3>
                <button onclick="closeModal('galleryModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        <form id="galleryForm" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="id" id="galleryId">
            <input type="hidden" name="type" value="gallery">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Image *</label>
                <div id="galleryImagePreview" class="mb-2 hidden">
                    <img id="galleryImagePreviewImg" src="" alt="Preview" class="w-full h-40 object-cover rounded-lg">
                </div>
                <input type="file" name="image" id="galleryImage" accept="image/*"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent"
                       onchange="previewImage(this, 'galleryImagePreviewImg', 'galleryImagePreview')">
                <p class="text-xs text-gray-500 mt-1">Recommended: 800x800px, JPG/PNG/WebP</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alt Text / Caption *</label>
                <input type="text" name="alt_text" id="galleryAltText" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="galleryCategory"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
                    <option value="food">Food</option>
                    <option value="ambiance">Ambiance</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="events">Events</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                <input type="number" name="display_order" id="galleryDisplayOrder" value="0"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-kampung-brown focus:border-transparent">
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal('galleryModal')" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-kampung-brown text-white rounded-lg hover:bg-kampung-brown/90">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 z-50 hidden">
    <div class="flex items-center gap-3 px-6 py-4 rounded-lg shadow-lg" id="toastContent">
        <span id="toastMessage"></span>
    </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

// Store data for editing
const aboutData = <?= json_encode($aboutData) ?>;
const menuData = <?= json_encode($menuData) ?>;
const galleryData = <?= json_encode($galleryData) ?>;

// Modal functions
function openAboutModal() {
    document.getElementById('aboutForm').reset();
    document.getElementById('aboutId').value = '';
    document.getElementById('aboutModalTitle').textContent = 'Add About Content';
    document.getElementById('aboutImagePreview').classList.add('hidden');
    document.getElementById('aboutModal').classList.remove('hidden');
}

function openMenuModal() {
    document.getElementById('menuForm').reset();
    document.getElementById('menuId').value = '';
    document.getElementById('menuModalTitle').textContent = 'Add Menu Item';
    document.getElementById('menuImagePreview').classList.add('hidden');
    document.getElementById('menuModal').classList.remove('hidden');
}

function openGalleryModal() {
    document.getElementById('galleryForm').reset();
    document.getElementById('galleryId').value = '';
    document.getElementById('galleryModalTitle').textContent = 'Add Gallery Image';
    document.getElementById('galleryImagePreview').classList.add('hidden');
    document.getElementById('galleryModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Edit functions
function editAbout(id) {
    const item = aboutData.find(a => a.id === id);
    if (!item) return;
    
    document.getElementById('aboutId').value = item.id;
    document.getElementById('aboutTitle').value = item.title || '';
    document.getElementById('aboutDescription').value = item.description || '';
    document.getElementById('aboutQuote').value = item.quote || '';
    document.getElementById('aboutQuoteAuthor').value = item.quote_author || '';
    document.getElementById('aboutDisplayOrder').value = item.display_order || 0;
    document.getElementById('aboutModalTitle').textContent = 'Edit About Content';
    
    if (item.image_filename) {
        document.getElementById('aboutImagePreviewImg').src = `${BASE_URL}/assets/uploads/about/${item.image_filename}`;
        document.getElementById('aboutImagePreview').classList.remove('hidden');
    } else {
        document.getElementById('aboutImagePreview').classList.add('hidden');
    }
    
    document.getElementById('aboutModal').classList.remove('hidden');
}

function editMenu(id) {
    const item = menuData.find(m => m.id === id);
    if (!item) return;
    
    document.getElementById('menuId').value = item.id;
    document.getElementById('menuName').value = item.name || '';
    document.getElementById('menuDescription').value = item.description || '';
    document.getElementById('menuPrice').value = item.price || '';
    document.getElementById('menuCategory').value = item.category || 'main';
    document.getElementById('menuDisplayOrder').value = item.display_order || 0;
    document.getElementById('menuIsFeatured').checked = item.is_featured;
    document.getElementById('menuModalTitle').textContent = 'Edit Menu Item';
    
    if (item.image_filename) {
        document.getElementById('menuImagePreviewImg').src = `${BASE_URL}/assets/uploads/menu/${item.image_filename}`;
        document.getElementById('menuImagePreview').classList.remove('hidden');
    } else {
        document.getElementById('menuImagePreview').classList.add('hidden');
    }
    
    document.getElementById('menuModal').classList.remove('hidden');
}

function editGallery(id) {
    const item = galleryData.find(g => g.id === id);
    if (!item) return;
    
    document.getElementById('galleryId').value = item.id;
    document.getElementById('galleryAltText').value = item.alt_text || '';
    document.getElementById('galleryCategory').value = item.category || 'ambiance';
    document.getElementById('galleryDisplayOrder').value = item.display_order || 0;
    document.getElementById('galleryModalTitle').textContent = 'Edit Gallery Image';
    
    if (item.image_filename) {
        document.getElementById('galleryImagePreviewImg').src = `${BASE_URL}/assets/uploads/gallery/${item.image_filename}`;
        document.getElementById('galleryImagePreview').classList.remove('hidden');
    } else {
        document.getElementById('galleryImagePreview').classList.add('hidden');
    }
    
    document.getElementById('galleryModal').classList.remove('hidden');
}

// Image preview
function previewImage(input, imgId, containerId) {
    const preview = document.getElementById(imgId);
    const container = document.getElementById(containerId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Delete function
async function deleteContent(table, id) {
    if (!confirm('Are you sure you want to delete this item?')) return;
    
    try {
        const response = await fetch(`${BASE_URL}/admin/api/content.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ table, id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Item deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.error || 'Failed to delete item', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}

// Form submissions
document.getElementById('aboutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitForm(this, 'about');
});

document.getElementById('menuForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitForm(this, 'menu');
});

document.getElementById('galleryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitForm(this, 'gallery');
});

async function submitForm(form, type) {
    const formData = new FormData(form);
    
    try {
        const response = await fetch(`${BASE_URL}/admin/api/content.php`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Content saved successfully', 'success');
            closeModal(type + 'Modal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(result.error || 'Failed to save content', 'error');
        }
    } catch (error) {
        showToast('An error occurred', 'error');
    }
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const content = document.getElementById('toastContent');
    const messageEl = document.getElementById('toastMessage');
    
    content.className = 'flex items-center gap-3 px-6 py-4 rounded-lg shadow-lg ';
    content.className += type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white';
    messageEl.textContent = message;
    
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}

// Close modal on outside click
document.querySelectorAll('[id$="Modal"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
