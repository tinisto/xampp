-- Account Activation SQL Script
-- Use this if you have direct database access (phpMyAdmin, MySQL console, etc.)

-- Step 1: Check your user status
SELECT id, email, is_active, created_at 
FROM users 
WHERE email = 'your-email@example.com';  -- Replace with your actual email

-- Step 2: Activate your account
UPDATE users 
SET is_active = 1,
    activation_token = NULL,
    activation_link = NULL
WHERE email = 'your-email@example.com';  -- Replace with your actual email

-- Step 3: Verify activation
SELECT id, email, is_active 
FROM users 
WHERE email = 'your-email@example.com';  -- Replace with your actual email

-- Alternative: Activate ALL inactive users (use with caution)
-- UPDATE users SET is_active = 1 WHERE is_active = 0;

-- View all users and their activation status
-- SELECT id, email, is_active, created_at FROM users ORDER BY created_at DESC;