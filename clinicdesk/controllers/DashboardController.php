<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class DashboardController {

    private UserModel $userModel;
    private AppointmentModel $appointmentModel;
    private PrescriptionModel $prescriptionModel;
    private DoctorModel $doctorModel;
    private NotificationModel $notificationModel;

    public function __construct() {
        $this->userModel         = new UserModel();
        $this->appointmentModel  = new AppointmentModel();
        $this->prescriptionModel = new PrescriptionModel();
        $this->doctorModel       = new DoctorModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index(): void {
        Auth::requireRole('admin', 'doctor', 'patient');
        $role = Auth::role();

        if ($role === 'admin') {
            $this->adminDashboard();
        } elseif ($role === 'doctor') {
            $this->doctorDashboard();
        } else {
            $this->patientDashboard();
        }
    }

    private function adminDashboard(): void {
        $pageTitle = 'Admin Dashboard — ' . APP_NAME;

        // User counts by role
        $userCounts = [];
        foreach ($this->userModel->countByRole() as $row) {
            $userCounts[$row['role']] = $row['total'];
        }

        // Appointment stats
        $appointmentsToday = $this->appointmentModel->countToday();
        $weeklyStats       = [];
        foreach ($this->appointmentModel->countThisWeekByStatus() as $row) {
            $weeklyStats[$row['status']] = $row['total'];
        }

        $statusCounts = [];
        foreach ($this->appointmentModel->countByStatus() as $row) {
            $statusCounts[$row['status']] = $row['total'];
        }

        // Recent appointments
        $recentAppointments = $this->appointmentModel->getRecentAll(5);

        // Chart data for last 14 days
        $chartData = $this->appointmentModel->getLast14Days();
        $chartLabels = array_column($chartData, 'appt_date');
        $chartValues = array_column($chartData, 'total');

        // Total doctors
        $totalDoctors = $this->doctorModel->countAll();

        require_once __DIR__ . '/../views/dashboard/admin.php';
    }

    private function doctorDashboard(): void {
        $pageTitle = 'Doctor Dashboard — ' . APP_NAME;

        $doctor = $this->doctorModel->findByUserId(Auth::id());
        if (!$doctor) {
            flashMessage('error', 'Doctor profile not found.');
            redirect(BASE_URL . '/index.php?page=auth&action=login');
        }

        $doctorId = $doctor['id'];

        // Today's appointments
        $todayAppointments = $this->appointmentModel->getTodayByDoctor($doctorId);

        // Upcoming appointments
        $upcomingAppointments = $this->appointmentModel->getUpcoming($doctorId, 5);

        // Monthly stats
        $statusCounts = [];
        foreach ($this->appointmentModel->countByStatus() as $row) {
            $statusCounts[$row['status']] = $row['total'];
        }

        // Rating
        $rating = $this->doctorModel->getAverageRating($doctorId);

        // Unread notifications
        $unreadCount = $this->notificationModel->countUnread(Auth::id());

        require_once __DIR__ . '/../views/dashboard/doctor.php';
    }

    private function patientDashboard(): void {
        $pageTitle = 'Patient Dashboard — ' . APP_NAME;

        $patientId = Auth::id();

        // Active appointments
        $activeAppointments = $this->appointmentModel->getByPatient(
            $patientId, 0, 5, ['status' => 'pending']
        );

        // Next upcoming appointment
        $upcomingAppointments = $this->appointmentModel->getByPatient(
            $patientId, 0, 1, []
        );
        $nextAppointment = $upcomingAppointments[0] ?? null;

        // Counts
        $totalCompleted    = $this->appointmentModel->countFiltered(['status' => 'completed'], 'patient', $patientId);
        $totalPrescriptions = $this->prescriptionModel->countByPatient($patientId);
        $totalActive       = $this->appointmentModel->countFiltered(['status' => 'pending'], 'patient', $patientId);

        // Unread notifications
        $unreadCount = $this->notificationModel->countUnread($patientId);

        require_once __DIR__ . '/../views/dashboard/patient.php';
    }
}