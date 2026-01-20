-- Rembayung Database Schema
-- Run this in your Supabase SQL Editor

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    booking_date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL CHECK (time_slot IN ('lunch', 'dinner')),
    pax INTEGER NOT NULL CHECK (pax >= 2 AND pax <= 8),
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'cancelled')),
    special_requests TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create indexes for faster queries
CREATE INDEX IF NOT EXISTS idx_bookings_date ON bookings(booking_date);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_bookings_created ON bookings(created_at DESC);

-- Enable Row Level Security
ALTER TABLE bookings ENABLE ROW LEVEL SECURITY;
ALTER TABLE admins ENABLE ROW LEVEL SECURITY;

-- Policy: Allow anonymous inserts for bookings (public form)
CREATE POLICY "Allow anonymous booking inserts" ON bookings
    FOR INSERT WITH CHECK (true);

-- Policy: Allow authenticated reads for bookings (admin)
CREATE POLICY "Allow authenticated reads" ON bookings
    FOR SELECT USING (auth.role() = 'authenticated');

-- Policy: Allow authenticated updates for bookings (admin)
CREATE POLICY "Allow authenticated updates" ON bookings
    FOR UPDATE USING (auth.role() = 'authenticated');

-- Insert a test admin (password: admin123 - remember to change this!)
-- Password hash is for 'admin123' using PASSWORD_DEFAULT
INSERT INTO admins (email, password_hash, name)
VALUES ('admin@rembayung.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin')
ON CONFLICT (email) DO NOTHING;
