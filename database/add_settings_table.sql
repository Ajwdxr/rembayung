-- Add restaurant settings table for fixed weekly closure day
-- Run this in your Supabase SQL Editor

CREATE TABLE IF NOT EXISTS restaurant_settings (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Enable RLS
ALTER TABLE restaurant_settings ENABLE ROW LEVEL SECURITY;

-- Allow public reads (for closure day display)
CREATE POLICY "Allow public reads for settings" ON restaurant_settings
    FOR SELECT USING (true);

-- Allow authenticated (admin) full access
CREATE POLICY "Allow authenticated full access to settings" ON restaurant_settings
    FOR ALL USING (auth.role() = 'authenticated');

-- Insert default setting for weekly closure day
-- Values: 0=Sunday, 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday, null=No fixed closure
INSERT INTO restaurant_settings (setting_key, setting_value) VALUES
    ('weekly_closure_day', null)
ON CONFLICT (setting_key) DO NOTHING;
