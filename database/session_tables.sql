-- Session-Based Time Slots Migration
-- Run this in your Supabase SQL Editor

-- Sessions table (e.g., "Lunch", "Dinner", "Breakfast")
CREATE TABLE IF NOT EXISTS booking_sessions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(50) NOT NULL,
    description TEXT,
    display_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Time Slots table (specific times within each session)
CREATE TABLE IF NOT EXISTS session_time_slots (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    session_id UUID REFERENCES booking_sessions(id) ON DELETE CASCADE,
    time_value TIME NOT NULL,
    time_label VARCHAR(20) NOT NULL,
    max_bookings INTEGER DEFAULT 10,
    is_active BOOLEAN DEFAULT true,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_booking_sessions_active ON booking_sessions(is_active, display_order);
CREATE INDEX IF NOT EXISTS idx_session_time_slots_session ON session_time_slots(session_id);
CREATE INDEX IF NOT EXISTS idx_session_time_slots_active ON session_time_slots(is_active, display_order);

-- Enable RLS
ALTER TABLE booking_sessions ENABLE ROW LEVEL SECURITY;
ALTER TABLE session_time_slots ENABLE ROW LEVEL SECURITY;

-- Policies for booking_sessions
CREATE POLICY "Allow public reads for active sessions" ON booking_sessions
    FOR SELECT USING (is_active = true);

CREATE POLICY "Allow authenticated full access to sessions" ON booking_sessions
    FOR ALL USING (auth.role() = 'authenticated');

-- Policies for session_time_slots
CREATE POLICY "Allow public reads for active time slots" ON session_time_slots
    FOR SELECT USING (is_active = true);

CREATE POLICY "Allow authenticated full access to time slots" ON session_time_slots
    FOR ALL USING (auth.role() = 'authenticated');

-- Alter bookings table to add session references
-- First, drop the old time_slot check constraint that restricts to 'lunch'/'dinner'
ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_time_slot_check;

-- Add new columns for session references
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS session_id UUID REFERENCES booking_sessions(id);
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS time_slot_id UUID REFERENCES session_time_slots(id);

-- Insert default sessions with time slots
INSERT INTO booking_sessions (name, description, display_order, is_active) VALUES
    ('Lunch', 'Lunch service from 11:30 AM to 3:00 PM', 1, true),
    ('Dinner', 'Dinner service from 6:00 PM to 10:00 PM', 2, true);

-- Get the session IDs and insert time slots
DO $$
DECLARE
    lunch_id UUID;
    dinner_id UUID;
BEGIN
    SELECT id INTO lunch_id FROM booking_sessions WHERE name = 'Lunch' LIMIT 1;
    SELECT id INTO dinner_id FROM booking_sessions WHERE name = 'Dinner' LIMIT 1;
    
    -- Lunch time slots
    INSERT INTO session_time_slots (session_id, time_value, time_label, display_order) VALUES
        (lunch_id, '11:30', '11:30 AM', 1),
        (lunch_id, '12:00', '12:00 PM', 2),
        (lunch_id, '12:30', '12:30 PM', 3),
        (lunch_id, '13:00', '1:00 PM', 4),
        (lunch_id, '13:30', '1:30 PM', 5),
        (lunch_id, '14:00', '2:00 PM', 6),
        (lunch_id, '14:30', '2:30 PM', 7);
    
    -- Dinner time slots
    INSERT INTO session_time_slots (session_id, time_value, time_label, display_order) VALUES
        (dinner_id, '18:00', '6:00 PM', 1),
        (dinner_id, '18:30', '6:30 PM', 2),
        (dinner_id, '19:00', '7:00 PM', 3),
        (dinner_id, '19:30', '7:30 PM', 4),
        (dinner_id, '20:00', '8:00 PM', 5),
        (dinner_id, '20:30', '8:30 PM', 6),
        (dinner_id, '21:00', '9:00 PM', 7);
END $$;
