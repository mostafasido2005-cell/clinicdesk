<?php
// Application Settings
define('APP_NAME', 'ClinicDesk');
define('BASE_URL', 'http://localhost/clinicdesk');
define('ITEMS_PER_PAGE', 10);

// Upload Settings
define('MAX_AVATAR_SIZE', 1048576);      // 1MB
define('MAX_PHOTO_SIZE', 1048576);       // 1MB
define('MAX_PRESCRIPTION_SIZE', 3145728); // 3MB

// Upload Paths
define('UPLOAD_AVATARS', __DIR__ . '/../public/uploads/avatars/');
define('UPLOAD_PHOTOS', __DIR__ . '/../public/uploads/doctor_photos/');
define('UPLOAD_PRESCRIPTIONS', __DIR__ . '/../public/uploads/prescriptions/');

// Session
define('SESSION_NAME', 'clinicdesk_session');

// Time slots for appointments
define('TIME_SLOTS', [
    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
    '15:00', '15:30', '16:00'
]);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}