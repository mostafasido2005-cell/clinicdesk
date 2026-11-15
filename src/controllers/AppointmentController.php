<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../models/RatingModel.php';

class AppointmentController {

    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;
    private NotificationModel $notificationModel;
    private RatingModel $ratingModel;

    public function __construct() {
        $this->appointmentModel  = new AppointmentModel();
        $this->doctorModel       = new DoctorModel();
        $this->notificationModel = new NotificationModel();
        $this->ratingModel       = new RatingModel();
    }

    public function index(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        $page    = max(1, (int)($_GET['p'] ?? 1));
        $filters = [
            'status'       => $_GET['status'] ?? '',
            'start_date'   => $_GET['start_date'] ?? '',
            'end_date'     => $_GET['end_date'] ?? '',
            'doctor_id'    => $_GET['doctor_id'] ?? '',
            'patient_name' => $_GET['patient_name'] ?? '',
        ];

        $role = Auth::role();

        if ($role === 'patient') {
            $total     = $this->appointmentModel->countFiltered($filters, 'patient', Auth::id());
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointmentModel->getByPatient(
                Auth::id(), $paginator->offset(), ITEMS_PER_PAGE, $filters
            );
        } elseif ($role === 'doctor') {
            $doctor    = $this->doctorModel->findByUserId(Auth::id());
            $doctorId  = $doctor['id'] ?? 0;
            $total     = $this->appointmentModel->countFiltered($filters, 'doctor', $doctorId);
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointmentModel->getByDoctor(
                $doctorId, $paginator->offset(), ITEMS_PER_PAGE, $filters
            );
        } else {
            $total     = $this->appointmentModel->countFiltered($filters);
            $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
            $appointments = $this->appointmentModel->getAll(
                $paginator->offset(), ITEMS_PER_PAGE, $filters
            );
        }

        $doctors   = $this->doctorModel->getAll();
        $pageTitle = 'Appointments — ' . APP_NAME;
        require_once __DIR__ . '/../views/appointments/index.php';
    }

    public function book(): void {
        Auth::requireRole('patient');

        $doctors   = $this->doctorModel->getAll();
        $timeSlots = TIME_SLOTS;
        $pageTitle = 'Book Appointment — ' . APP_NAME;
        require_once __DIR__ . '/../views/appointments/book.php';
    }

    public function store(): void {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);
        $date     = $_POST['appt_date'] ?? '';
        $time     = $_POST['appt_time'] ?? '';
        $reason   = trim($_POST['reason'] ?? '');

        if (!$doctorId || !$date || !$time) {
            flashMessage('error', 'Please fill in all required fields.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        // Validate date is not in the past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            flashMessage('error', 'Appointment date cannot be in the past.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        // Validate day of week
        $doctor        = $this->doctorModel->findById($doctorId);
        $availableDays = explode(',', $doctor['available_days'] ?? '');
        $dayOfWeek     = date('D', strtotime($date));

        if (!in_array($dayOfWeek, $availableDays)) {
            flashMessage('error', 'Doctor is not available on this day.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        // Check conflict
        if ($this->appointmentModel->hasConflict($doctorId, $date, $time)) {
            flashMessage('error', 'This slot is already booked. Please choose another time.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }

        $booked = $this->appointmentModel->book([
            'patient_id' => Auth::id(),
            'doctor_id'  => $doctorId,
            'appt_date'  => $date,
            'appt_time'  => $time,
            'reason'     => $reason,
        ]);

        if ($booked) {
            // Notify doctor
            $this->notificationModel->create(
                $doctor['user_id'],
                'New Appointment',
                'You have a new appointment on ' . formatDate($date) . ' at ' . formatTime($time)
            );

            flashMessage('success', 'Appointment booked successfully!');
            redirect(BASE_URL . '/index.php?page=appointments');
        } else {
            flashMessage('error', 'Booking failed. Please try again.');
            redirect(BASE_URL . '/index.php?page=appointments&action=book');
        }
    }

    public function detail(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id          = (int)($_GET['id'] ?? 0);
        $appointment = $this->appointmentModel->findById($id);

        if (!$appointment) {
            flashMessage('error', 'Appointment not found.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        // Ownership check
        $this->checkOwnership($appointment);

        $rating    = $this->ratingModel->findByAppointmentId($id);
        $hasRated  = $rating !== null;
        $pageTitle = 'Appointment Detail — ' . APP_NAME;
        require_once __DIR__ . '/../views/appointments/detail.php';
    }

    public function updateStatus(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $notes  = trim($_POST['doctor_notes'] ?? '');

        $appointment = $this->appointmentModel->findById($id);
        if (!$appointment) {
            flashMessage('error', 'Appointment not found.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $this->checkOwnership($appointment);

        // Validate status transitions
        $allowedTransitions = [
            'admin'   => ['pending', 'confirmed', 'completed', 'cancelled'],
            'doctor'  => ['confirmed', 'completed', 'cancelled'],
            'patient' => ['cancelled'],
        ];

        if (!in_array($status, $allowedTransitions[Auth::role()])) {
            flashMessage('error', 'Invalid status change.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $cancelReason = '';
        $cancelledBy  = 0;
        if ($status === 'cancelled') {
            $cancelReason = trim($_POST['cancellation_reason'] ?? '');
            if (strlen($cancelReason) < 10) {
                flashMessage('error', 'Please provide a cancellation reason (min 10 characters).');
                redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $id);
            }
            $cancelledBy = Auth::id();
        }

        $this->appointmentModel->updateStatus($id, $status, $notes, $cancelledBy, $cancelReason);

        // Send notification
        $notifyUserId = Auth::role() === 'patient'
            ? $appointment['doctor_id']
            : $appointment['patient_id'];

        $this->notificationModel->create(
            $notifyUserId,
            'Appointment ' . ucfirst($status),
            'Your appointment on ' . formatDate($appointment['appt_date']) . ' has been ' . $status . '.'
        );

        flashMessage('success', 'Appointment status updated to ' . ucfirst($status) . '.');
        redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $id);
    }

    public function calendar(): void {
        Auth::requireRole('doctor');

        $doctor   = $this->doctorModel->findByUserId(Auth::id());
        $doctorId = $doctor['id'] ?? 0;
        $weekOffset = (int)($_GET['week'] ?? 0);

        $startOfWeek = strtotime('monday this week') + ($weekOffset * 7 * 86400);
        $weekDays    = [];
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = date('Y-m-d', $startOfWeek + ($i * 86400));
        }

        $appointments = $this->appointmentModel->getByDoctor(
            $doctorId, 0, 100,
            ['start_date' => $weekDays[0], 'end_date' => $weekDays[6]]
        );

        // Group by date
        $calendarData = [];
        foreach ($appointments as $appt) {
            $calendarData[$appt['appt_date']][] = $appt;
        }

        $pageTitle = 'My Calendar — ' . APP_NAME;
        require_once __DIR__ . '/../views/appointments/calendar.php';
    }

    public function rate(): void {
        Auth::requireRole('patient');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $rating        = (int)($_POST['rating'] ?? 0);
        $comment       = trim($_POST['comment'] ?? '');

        $appointment = $this->appointmentModel->findById($appointmentId);

        if (!$appointment || $appointment['patient_id'] !== Auth::id()) {
            flashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        if ($appointment['status'] !== 'completed') {
            flashMessage('error', 'You can only rate completed appointments.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
        }

        if ($this->ratingModel->hasRated($appointmentId, Auth::id())) {
            flashMessage('error', 'You have already rated this appointment.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
        }

        if ($rating < 1 || $rating > 5) {
            flashMessage('error', 'Invalid rating.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
        }

        $doctor = $this->doctorModel->findById($appointment['doctor_id']);

        $this->ratingModel->create([
            'appointment_id' => $appointmentId,
            'patient_id'     => Auth::id(),
            'doctor_id'      => $appointment['doctor_id'],
            'rating'         => $rating,
            'comment'        => $comment,
        ]);

        // Notify doctor
        $this->notificationModel->create(
            $doctor['user_id'],
            'New Rating',
            'You received a ' . $rating . '-star rating!'
        );

        flashMessage('success', 'Thank you for your rating!');
        redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
    }

    private function checkOwnership(array $appointment): void {
        $role = Auth::role();
        if ($role === 'patient' && $appointment['patient_id'] !== Auth::id()) {
            flashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }
        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            if (!$doctor || $appointment['doctor_id'] !== $doctor['id']) {
                flashMessage('error', 'Access denied.');
                redirect(BASE_URL . '/index.php?page=appointments');
            }
        }
    }
}