<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';

// Routing
$page   = $_GET['page']   ?? 'auth';
$action = $_GET['action'] ?? 'login';

// If logged in and visiting root, go to dashboard
if (Auth::check() && $page === 'auth' && $action === 'login') {
    redirect(BASE_URL . '/index.php?page=dashboard');
}

switch ($page) {

    case 'auth':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        switch ($action) {
            case 'login':
                $_SERVER['REQUEST_METHOD'] === 'POST'
                    ? $controller->loginPost()
                    : $controller->login();
                break;
            case 'logout':
                $controller->logout();
                break;
            case 'change_password':
                $_SERVER['REQUEST_METHOD'] === 'POST'
                    ? $controller->changePasswordPost()
                    : $controller->changePassword();
                break;
            default:
                $controller->login();
        }
        break;

    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'users':
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        switch ($action) {
            case 'create': $controller->create(); break;
            case 'store':  $controller->store();  break;
            case 'edit':   $controller->edit();   break;
            case 'update': $controller->update(); break;
            case 'toggle': $controller->toggleActive(); break;
            case 'reset_password': $controller->resetPassword(); break;
            default:       $controller->index();
        }
        break;

    case 'doctors':
        require_once __DIR__ . '/controllers/DoctorController.php';
        $controller = new DoctorController();
        switch ($action) {
            case 'edit':                 $controller->edit();                 break;
            case 'update':               $controller->update();               break;
            case 'specializations':      $controller->specializations();      break;
            case 'add_specialization':   $controller->addSpecialization();    break;
            case 'delete_specialization':$controller->deleteSpecialization(); break;
            default:                     $controller->index();
        }
        break;

    case 'appointments':
        require_once __DIR__ . '/controllers/AppointmentController.php';
        $controller = new AppointmentController();
        switch ($action) {
            case 'book':          $controller->book();         break;
            case 'store':         $controller->store();        break;
            case 'detail':        $controller->detail();       break;
            case 'update_status': $controller->updateStatus(); break;
            case 'calendar':      $controller->calendar();     break;
            case 'rate':          $controller->rate();         break;
            default:              $controller->index();
        }
        break;

    case 'prescriptions':
        require_once __DIR__ . '/controllers/PrescriptionController.php';
        $controller = new PrescriptionController();
        switch ($action) {
            case 'add':      $controller->add();       break;
            case 'store':    $controller->store();     break;
            case 'download': $controller->download();  break;
            case 'print':    $controller->printView(); break;
            default:         $controller->index();
        }
        break;

    case 'reports':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController();
        $controller->index();
        break;

    case 'notifications':
        require_once __DIR__ . '/controllers/NotificationController.php';
        $controller = new NotificationController();
        switch ($action) {
            case 'mark_read': $controller->markRead();       break;
            case 'delete':    $controller->delete();         break;
            case 'count':     $controller->getUnreadCount(); break;
            default:          $controller->index();
        }
        break;

    case 'ratings':
        require_once __DIR__ . '/controllers/RatingController.php';
        $controller = new RatingController();
        $controller->store();
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/views/errors/404.php';
}