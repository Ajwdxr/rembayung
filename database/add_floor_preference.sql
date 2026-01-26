-- Add floor_preference column to bookings table
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS floor_preference VARCHAR(20) DEFAULT 'any';

-- Add check constraint to ensure valid values (optional but recommended)
-- ALTER TABLE bookings ADD CONSTRAINT check_floor_preference CHECK (floor_preference IN ('any', 'ground', 'upper'));
