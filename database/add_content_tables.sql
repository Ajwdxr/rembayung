-- Content Management Tables for About, Menu, and Gallery
-- Run this in your Supabase SQL Editor

-- Drop existing tables first (for clean recreation)
DROP TABLE IF EXISTS hero_content CASCADE;
DROP TABLE IF EXISTS gallery_images CASCADE;
DROP TABLE IF EXISTS menu_items CASCADE;
DROP TABLE IF EXISTS about_content CASCADE;

-- About section content
CREATE TABLE IF NOT EXISTS about_content (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image_filename VARCHAR(255),
    quote TEXT,
    quote_author VARCHAR(100),
    display_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Menu items table
CREATE TABLE IF NOT EXISTS menu_items (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price VARCHAR(50) NOT NULL,
    image_filename VARCHAR(255),
    category VARCHAR(50) DEFAULT 'main',
    is_featured BOOLEAN DEFAULT false,
    is_active BOOLEAN DEFAULT true,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Gallery images table
CREATE TABLE IF NOT EXISTS gallery_images (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    image_filename VARCHAR(255),
    alt_text VARCHAR(255) NOT NULL,
    category VARCHAR(50) DEFAULT 'ambiance',
    display_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Hero section content
CREATE TABLE IF NOT EXISTS hero_content (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255),
    tagline VARCHAR(255),
    image_filename VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_menu_items_category ON menu_items(category);
CREATE INDEX IF NOT EXISTS idx_menu_items_active ON menu_items(is_active);
CREATE INDEX IF NOT EXISTS idx_gallery_images_category ON gallery_images(category);
CREATE INDEX IF NOT EXISTS idx_gallery_images_active ON gallery_images(is_active);

-- Enable Row Level Security
ALTER TABLE about_content ENABLE ROW LEVEL SECURITY;
ALTER TABLE menu_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE gallery_images ENABLE ROW LEVEL SECURITY;
ALTER TABLE hero_content ENABLE ROW LEVEL SECURITY;

-- Policies for public read access
CREATE POLICY "Allow public read about_content" ON about_content
    FOR SELECT USING (is_active = true);
    
CREATE POLICY "Allow public read menu_items" ON menu_items
    FOR SELECT USING (is_active = true);
    
CREATE POLICY "Allow public read gallery_images" ON gallery_images
    FOR SELECT USING (is_active = true);
    
CREATE POLICY "Allow public read hero_content" ON hero_content
    FOR SELECT USING (is_active = true);

-- Policies for authenticated admin write access
CREATE POLICY "Allow authenticated insert about_content" ON about_content
    FOR INSERT WITH CHECK (true);
CREATE POLICY "Allow authenticated update about_content" ON about_content
    FOR UPDATE USING (true);
CREATE POLICY "Allow authenticated delete about_content" ON about_content
    FOR DELETE USING (true);

CREATE POLICY "Allow authenticated insert menu_items" ON menu_items
    FOR INSERT WITH CHECK (true);
CREATE POLICY "Allow authenticated update menu_items" ON menu_items
    FOR UPDATE USING (true);
CREATE POLICY "Allow authenticated delete menu_items" ON menu_items
    FOR DELETE USING (true);

CREATE POLICY "Allow authenticated insert gallery_images" ON gallery_images
    FOR INSERT WITH CHECK (true);
CREATE POLICY "Allow authenticated update gallery_images" ON gallery_images
    FOR UPDATE USING (true);
CREATE POLICY "Allow authenticated delete gallery_images" ON gallery_images
    FOR DELETE USING (true);

CREATE POLICY "Allow authenticated insert hero_content" ON hero_content
    FOR INSERT WITH CHECK (true);
CREATE POLICY "Allow authenticated update hero_content" ON hero_content
    FOR UPDATE USING (true);
CREATE POLICY "Allow authenticated delete hero_content" ON hero_content
    FOR DELETE USING (true);

-- Insert default about content
INSERT INTO about_content (title, description, quote, quote_author, display_order)
VALUES (
    'From Kampung to City, With Love',
    'Rembayung is born from a deep love for Malaysian heritage cuisine. Founded by Khairul Aming, a culinary storyteller who has touched millions of hearts through his authentic recipes and warm personality.

The name "Rembayung" evokes the gentle warmth of twilight in the kampung - that magical moment when families gather, smoke rises from wood-fire kitchens, and the aroma of home-cooked meals fills the air.

Every dish we serve carries the essence of Malaysian kampung cooking - bold flavors, traditional techniques, and recipes passed down through generations, presented with modern elegance.',
    'Masak dengan hati, hidang dengan kasih.',
    'Khairul Aming',
    1
);

-- Insert default menu items
INSERT INTO menu_items (name, description, price, category, is_featured, display_order) VALUES
('Nasi Kampung Rembayung', 'Our signature dish. Fragrant rice served with sambal ikan bilis, ayam goreng berempah, and ulam.', 'RM 28', 'signature', true, 1),
('Rendang Tok', 'Slow-cooked beef rendang using generations-old Perak recipe. Rich and aromatic.', 'RM 38', 'main', true, 2),
('Gulai Lemak Ikan Patin', 'Creamy turmeric curry with fresh patin fish and ulam raja.', 'RM 35', 'main', true, 3),
('Ayam Percik', 'Grilled chicken marinated in coconut milk and spices, served with nasi kerabu.', 'RM 32', 'main', true, 4),
('Sambal Petai Udang', 'Tiger prawns stir-fried with petai beans in fiery sambal belacan.', 'RM 42', 'main', true, 5),
('Kuih Kampung Selection', 'Assorted traditional kuih - onde-onde, kuih lapis, seri muka, and more.', 'RM 18', 'dessert', true, 6);

-- Insert default gallery images
INSERT INTO gallery_images (alt_text, category, display_order) VALUES
('Restaurant interior', 'ambiance', 1),
('Dining area', 'ambiance', 2),
('Nasi Kampung', 'food', 3),
('Rendang Tok', 'food', 4),
('Traditional curry', 'food', 5),
('Grilled chicken', 'food', 6),
('Bar area', 'ambiance', 7),
('Chef preparing', 'kitchen', 8),
('Sambal prawns', 'food', 9);
