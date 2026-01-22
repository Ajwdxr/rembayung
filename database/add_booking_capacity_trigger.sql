-- Concurrent Booking Protection Trigger with FIFO Ordering
-- Run this in Supabase SQL Editor
-- This prevents overbooking and ensures first-come-first-served by milliseconds

-- ============================================
-- STEP 1: Create the validation function with advisory lock
-- ============================================

CREATE OR REPLACE FUNCTION check_booking_capacity()
RETURNS TRIGGER AS $$
DECLARE
    session_max_pax INTEGER;
    current_booked_pax INTEGER;
    remaining_pax INTEGER;
    lock_key BIGINT;
BEGIN
    -- Create a unique lock key based on session_id and booking_date
    -- This ensures bookings for the same session+date are processed one at a time (FIFO)
    lock_key := hashtext(NEW.session_id::text || NEW.booking_date::text);
    
    -- Acquire advisory lock - this serializes concurrent bookings
    -- First booking to acquire the lock gets processed first (by milliseconds)
    PERFORM pg_advisory_xact_lock(lock_key);
    
    -- Get the max pax for this session
    SELECT max_pax INTO session_max_pax
    FROM booking_sessions
    WHERE id = NEW.session_id;
    
    -- Use default if session not found
    IF session_max_pax IS NULL THEN
        session_max_pax := 225;
    END IF;
    
    -- Calculate currently booked pax for this session and date
    -- Only count pending and confirmed bookings
    SELECT COALESCE(SUM(pax), 0) INTO current_booked_pax
    FROM bookings
    WHERE session_id = NEW.session_id
      AND booking_date = NEW.booking_date
      AND status IN ('pending', 'confirmed');
    
    remaining_pax := session_max_pax - current_booked_pax;
    
    -- Check if new booking would exceed capacity
    IF NEW.pax > remaining_pax THEN
        IF remaining_pax <= 0 THEN
            RAISE EXCEPTION 'Sorry, this session is fully booked. Please select a different date or session.'
                USING ERRCODE = 'check_violation';
        ELSE
            RAISE EXCEPTION 'Sorry, only % pax available for this session. Please reduce party size or choose another session.', remaining_pax
                USING ERRCODE = 'check_violation';
        END IF;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ============================================
-- STEP 2: Create the trigger
-- ============================================

-- Drop existing trigger if it exists (safe to run multiple times)
DROP TRIGGER IF EXISTS check_booking_capacity_trigger ON bookings;

-- Create trigger that fires before each INSERT
CREATE TRIGGER check_booking_capacity_trigger
    BEFORE INSERT ON bookings
    FOR EACH ROW
    EXECUTE FUNCTION check_booking_capacity();

-- ============================================
-- VERIFICATION: Test the trigger
-- ============================================
-- You can test by trying to insert a booking that exceeds capacity:
-- INSERT INTO bookings (booking_date, session_id, time_slot_id, pax, name, phone, email)
-- VALUES ('2026-01-25', 'session-id-here', 'slot-id-here', 999, 'Test', '123', 'test@test.com');
-- This should fail with "Sorry, only X pax available..." error
