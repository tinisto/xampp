# Email Template Migration Guide

## Overview
We've modernized the email system with consistent, professional templates. All emails now use a unified design that matches the site's branding.

## New Email Templates

### 1. Password Reset (`password-reset.php`)
- Modern design with green gradient header
- Clear call-to-action button
- Security warning about link expiration

### 2. Password Changed (`password-changed.php`)
- Confirmation of successful password change
- Security tips
- Warning if user didn't make the change

### 3. Account Activation (`account-activation.php`)
- Welcome message
- Benefits list
- Activation button

### 4. Admin Notifications (`admin-notification.php`)
Templates for:
- New user registration
- New comments
- Contact form messages
- Database changes

### 5. User Notifications (`user-notification.php`)
Templates for:
- Welcome email
- Account suspended/unsuspended
- Comment replies

### 6. Base Template (`base-template.php`)
- Reusable template structure
- Consistent header/footer
- Helper functions for content blocks

## New Email Service

The `EmailService` class provides a clean interface:

```php
// Get instance
$emailService = EmailService::getInstance();

// Send password reset
$emailService->sendPasswordReset($email, $firstname, $resetLink);

// Send password changed notification
$emailService->sendPasswordChanged($email, $firstname);

// Send account activation
$emailService->sendAccountActivation($email, $firstname, $activationLink);

// Send admin notification
$emailService->sendAdminNotification('new_user', [
    'firstname' => $firstname,
    'lastname' => $lastname,
    'email' => $email,
    'institution_type' => $type
]);

// Send custom email
$emailService->sendCustom(
    $email, 
    'Subject', 
    'Email Title', 
    '<p>HTML content</p>',
    'Button Text',  // optional
    'https://link'  // optional
);
```

## Migration Steps

### Quick Migration (Current Implementation)
1. ✅ Created modern email templates
2. ✅ Updated password change process to use new template
3. ✅ Created EmailService class for future use

### Full Migration (Future)
1. Replace all `sendEmailToAdmin()` calls with `$emailService->sendAdminNotification()`
2. Replace all `sendActivationEmail()` calls with `$emailService->sendAccountActivation()`
3. Replace all custom email HTML with template calls
4. Remove redundant email functions
5. Implement email queue system
6. Add email logging

## Benefits
- Consistent design across all emails
- Mobile-responsive templates
- Easy to maintain and update
- Better deliverability with proper HTML structure
- Plain text alternatives automatically generated