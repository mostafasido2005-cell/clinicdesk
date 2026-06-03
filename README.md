# ClinicDesk - Clinic Management Dashboard

A full-featured clinic management system built with PHP, MySQL, and AdminLTE 3.

## Project Overview
ClinicDesk is a private, login-protected clinic management dashboard serving three roles: Admin, Doctor, and Patient.

## Features
- Multi-role authentication (Admin, Doctor, Patient)
- Appointment booking with conflict detection
- Prescription management
- Real-time notifications
- Patient ratings for doctors
- Weekly calendar view for doctors
- Reports with CSV export
- Dark mode support
- First-login password change enforcement
- CSRF protection on all forms
- XSS prevention
- Secure file serving

## Requirements
- PHP 8.0+
- MySQL 5.7+
- XAMPP
- Web Browser

## Installation
1. Copy project to: C:/xampp/htdocs/clinicdesk/
2. Open phpMyAdmin and create database: clinicdesk_db
3. Import: clinicdesk_db.sql
4. Open in browser: http://localhost/clinicdesk

## Demo Accounts

| Role    | Email                     | Password   |
|---------|---------------------------|------------|
| Admin   | admin@clinic.local        | Admin@1234 |
| Doctor  | doctor@clinic.local       | Admin@1234 |
| Doctor  | sara@clinic.local         | Admin@1234 |
| Doctor  | khaled@clinic.local       | Admin@1234 |
| Patient | john.doe@patient.local    | Admin@1234 |
| Patient | alice.smith@patient.local | Admin@1234 |
| Patient | bob.johnson@patient.local | Admin@1234 |
| Patient | ahmad@patient.local       | Admin@1234 |

## Database Schema
- users - All system users
- specializations - Medical specializations
- doctors - Doctor profiles
- appointments - Patient appointments
- prescriptions - Medical prescriptions
- notifications - System notifications
- ratings - Patient ratings for doctors

## Security Features
- password_hash/password_verify BCRYPT
- CSRF tokens on all POST forms
- Prepared statements
- XSS prevention
- Session fixation prevention
- File type validation
- Role-based access control

## Built With
- PHP no framework
- MySQL with mysqli
- AdminLTE 3
- Bootstrap 4
- Chart.js
- DataTables

## Course Info
- Islamic University of Gaza
- Faculty of Information Technology
- Course: SDEV 2106 / WDMM 2010 / MOBC 2102
- Semester 2, 2025-2026
- Instructor: Eng. Mohammed Zuqlam
