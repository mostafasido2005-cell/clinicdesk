<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <!-- DateRangePicker -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/daterangepicker/daterangepicker.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/plugins/toastr/toastr.min.css">

    <style>
        :root {
            --primary: #3a7bd5;
            --primary-dark: #2c5fb3;
            --secondary: #00d2ff;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        /* Sidebar */
        .main-sidebar {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%) !important;
        }
        .brand-link {
            background: rgba(255,255,255,0.05) !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        }
        .brand-link .brand-text {
            font-weight: 700;
            font-size: 1.2rem;
            background: linear-gradient(90deg, #3a7bd5, #00d2ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .nav-sidebar .nav-link {
            color: rgba(255,255,255,0.75) !important;
            border-radius: 8px !important;
            margin: 2px 8px !important;
            transition: all 0.3s ease !important;
        }
        .nav-sidebar .nav-link:hover,
        .nav-sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(58,123,213,0.4), rgba(0,210,255,0.2)) !important;
            color: #fff !important;
            transform: translateX(4px);
        }
        .nav-sidebar .nav-link .nav-icon {
            color: #3a7bd5 !important;
        }

        /* Navbar */
        .main-header {
            background: #fff !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08) !important;
        }

        /* Cards */
        .card {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }

        /* Small boxes */
        .small-box {
            border-radius: 12px !important;
            overflow: hidden;
        }
        .small-box .inner h3 {
            font-size: 2.2rem !important;
            font-weight: 700 !important;
        }

        /* Buttons */
        .btn {
            border-radius: 8px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3a7bd5, #00d2ff) !important;
            border: none !important;
        }
        .btn-primary:hover {
            opacity: 0.9 !important;
            transform: translateY(-1px) !important;
        }

        /* Tables */
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody tr:hover {
            background: rgba(58,123,213,0.04);
        }

        /* Badges */
        .badge {
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 0.8rem !important;
        }

        /* Content wrapper */
        .content-wrapper {
            background: #f4f6f9 !important;
        }

        /* User avatar */
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3a7bd5;
        }

        /* Notification bell */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Dark mode toggle */
        body.dark-mode {
            background: #1a1a2e !important;
        }
        body.dark-mode .content-wrapper {
            background: #16213e !important;
        }
        body.dark-mode .card {
            background: #1e2a45 !important;
            color: #e0e0e0 !important;
        }
        body.dark-mode .main-header {
            background: #1e2a45 !important;
        }
        body.dark-mode .table {
            color: #e0e0e0 !important;
        }
        body.dark-mode .table thead th {
            background: #253555 !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed" id="appBody"></body>