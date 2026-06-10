<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/RatingModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class RatingController {

    private RatingModel $ratingModel;
    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;
    private NotificationModel $notificationModel;

    public function __construct() {
        $this->ratingModel        = new RatingModel();
        $this->appointmentModel   = new AppointmentModel();
        $this->doctorModel        = new DoctorModel();
        $this->notificationModel  = new NotificationModel();
    }

    public function store(): void {
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
            flashMessage('error', 'Please select a rating between 1 and 5.');
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
            'New Rating Received',
            'You received a ' . $rating . '-star rating from a patient!'
        );

        flashMessage('success', 'Thank you for your feedback!');
        redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
    }
}