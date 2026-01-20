-- Add restaurant holidays/closures table
-- Run this in your Supabase SQL Editor

CREATE TABLE IF NOT EXISTS restaurant_closures (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    closure_date DATE NOT NULL UNIQUE,
    reason VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create index for faster lookups
CREATE INDEX IF NOT EXISTS idx_closures_date ON restaurant_closures(closure_date);

-- Enable RLS
ALTER TABLE restaurant_closures ENABLE ROW LEVEL SECURITY;

-- Allow public reads (guests need to see closed dates)
CREATE POLICY "Allow public reads for closures" ON restaurant_closures
    FOR SELECT USING (true);

-- Allow authenticated (admin) full access
CREATE POLICY "Allow authenticated full access to closures" ON restaurant_closures
    FOR ALL USING (auth.role() = 'authenticated');

-- Example: Insert some test closures (optional)
-- INSERT INTO restaurant_closures (closure_date, reason) VALUES
--     ('2025-12-25', 'Christmas Day'),
--     ('2025-01-01', 'New Year Day'),
--     ('2025-01-29', 'Chinese New Year');
