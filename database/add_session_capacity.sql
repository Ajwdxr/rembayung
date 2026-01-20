-- Add session capacity column for pax limit per session
-- Run this in your Supabase SQL Editor

-- Add max_pax column to booking_sessions (default 225)
ALTER TABLE booking_sessions 
ADD COLUMN IF NOT EXISTS max_pax INTEGER DEFAULT 225;

-- Update existing sessions to have the default capacity
UPDATE booking_sessions SET max_pax = 225 WHERE max_pax IS NULL;

-- Create a function to calculate remaining pax for a session on a date
CREATE OR REPLACE FUNCTION get_session_availability(
    p_session_id UUID,
    p_booking_date DATE
)
RETURNS INTEGER AS $$
DECLARE
    v_max_pax INTEGER;
    v_booked_pax INTEGER;
BEGIN
    -- Get the max pax for the session
    SELECT COALESCE(max_pax, 225) INTO v_max_pax 
    FROM booking_sessions 
    WHERE id = p_session_id;
    
    -- Calculate total booked pax for confirmed/pending bookings
    SELECT COALESCE(SUM(pax), 0) INTO v_booked_pax
    FROM bookings
    WHERE session_id = p_session_id
    AND booking_date = p_booking_date
    AND status IN ('pending', 'confirmed');
    
    -- Return remaining capacity
    RETURN GREATEST(v_max_pax - v_booked_pax, 0);
END;
$$ LANGUAGE plpgsql;

-- Grant execute permission on the function
GRANT EXECUTE ON FUNCTION get_session_availability(UUID, DATE) TO anon;
GRANT EXECUTE ON FUNCTION get_session_availability(UUID, DATE) TO authenticated;
