<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/PrescriptionModel.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class PrescriptionController {

    private PrescriptionModel $prescriptionModel;
    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;
    private NotificationModel $notificationModel;

    public function __construct() {
        $this->prescriptionModel = new PrescriptionModel();
        $this->appointmentModel  = new AppointmentModel();
        $this->doctorModel       = new DoctorModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index(): void {
        Auth::requireRole('patient');

        $patientId     = Auth::id();
        $prescriptions = $this->prescriptionModel->getByPatient($patientId);
        $pageTitle     = 'My Prescriptions — ' . APP_NAME;
        require_once __DIR__ . '/../views/prescriptions/index.php';
    }

    public function add(): void {
        Auth::requireRole('doctor');

        $appointmentId = (int)($_GET['appointment_id'] ?? 0);
        $appointment   = $this->appointmentModel->findById($appointmentId);

        if (!$appointment) {
            flashMessage('error', 'Appointment not found.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        // Verify ownership
        $doctor = $this->doctorModel->findByUserId(Auth::id());
        if (!$doctor || $appointment['doctor_id'] !== $doctor['id']) {
            flashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        if ($appointment['status'] !== 'completed') {
            flashMessage('error', 'Prescription can only be added for completed appointments.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
        }

        // Check if prescription already exists
        $existing = $this->prescriptionModel->findByAppointmentId($appointmentId);
        if ($existing) {
            flashMessage('error', 'Prescription already exists for this appointment.');
            redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
        }

        $pageTitle = 'Add Prescription — ' . APP_NAME;
        require_once __DIR__ . '/../views/prescriptions/add.php';
    }

    public function store(): void {
        Auth::requireRole('doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        $appointmentId = (int)($_POST['appointment_id'] ?? 0);
        $diagnosis     = trim($_POST['diagnosis'] ?? '');
        $medications   = trim($_POST['medications'] ?? '');
        $notes         = trim($_POST['notes'] ?? '');

        $appointment = $this->appointmentModel->findById($appointmentId);

        if (!$appointment) {
            flashMessage('error', 'Appointment not found.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        // Verify ownership
        $doctor = $this->doctorModel->findByUserId(Auth::id());
        if (!$doctor || $appointment['doctor_id'] !== $doctor['id']) {
            flashMessage('error', 'Access denied.');
            redirect(BASE_URL . '/index.php?page=appointments');
        }

        if (!$diagnosis || !$medications) {
            flashMessage('error', 'Diagnosis and medications are required.');
            redirect(BASE_URL . '/index.php?page=prescriptions&action=add&appointment_id=' . $appointmentId);
        }

        // Handle PDF upload
        $filePath = null;
        if (!empty($_FILES['prescription_file']['name'])) {
            $uploaded = $this->handlePdfUpload($_FILES['prescription_file'], $appointmentId);
            if ($uploaded === false) {
                flashMessage('error', 'Invalid file. Only PDF files under 3MB are allowed.');
                redirect(BASE_URL . '/index.php?page=prescriptions&action=add&appointment_id=' . $appointmentId);
            }
            $filePath = $uploaded;
        }

        $this->prescriptionModel->create([
            'appointment_id' => $appointmentId,
            'diagnosis'      => $diagnosis,
            'medications'    => $medications,
            'notes'          => $notes,
            'file_path'      => $filePath,
        ]);

        // Notify patient
        $this->notificationModel->create(
            $appointment['patient_id'],
            'Prescription Added',
            'Your prescription for appointment on ' . formatDate($appointment['appt_date']) . ' is ready.'
        );

        flashMessage('success', 'Prescription added successfully!');
        redirect(BASE_URL . '/index.php?page=appointments&action=detail&id=' . $appointmentId);
    }

    public function download(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id           = (int)($_GET['id'] ?? 0);
        $prescription = $this->prescriptionModel->findById($id);

        if (!$prescription) {
            flashMessage('error', 'Prescription not found.');
            redirect(BASE_URL . '/index.php?page=prescriptions');
        }

        // Ownership check
        $role = Auth::role();
        if ($role === 'patient' && $prescription['patient_id'] !== Auth::id()) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            exit();
        }
        if ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            if (!$doctor || $prescription['doctor_id'] !== $doctor['id']) {
                http_response_code(403);
                require_once __DIR__ . '/../views/errors/403.php';
                exit();
            }
        }

        if (!$prescription['file_path']) {
            flashMessage('error', 'No file attached to this prescription.');
            redirect(BASE_URL . '/index.php?page=prescriptions');
        }

        $filePath = UPLOAD_PRESCRIPTIONS . $prescription['file_path'];

        if (!file_exists($filePath)) {
            flashMessage('error', 'File not found on server.');
            redirect(BASE_URL . '/index.php?page=prescriptions');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription_' . $id . '.pdf"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    }

    public function printView(): void {
        Auth::requireRole('admin', 'doctor', 'patient');

        $id           = (int)($_GET['id'] ?? 0);
        $prescription = $this->prescriptionModel->findById($id);

        if (!$prescription) {
            flashMessage('error', 'Prescription not found.');
            redirect(BASE_URL . '/index.php?page=prescriptions');
        }

        // Ownership check
        $role = Auth::role();
        if ($role === 'patient' && $prescription['patient_id'] !== Auth::id()) {
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            exit();
        }

        $pageTitle = 'Print Prescription — ' . APP_NAME;
        require_once __DIR__ . '/../views/prescriptions/print.php';
    }

    private function handlePdfUpload(array $file, int $appointmentId): string|false {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > MAX_PRESCRIPTION_SIZE) return false;

        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') return false;

        $filename = 'prescription_' . $appointmentId . '_' . time() . '.pdf';

        if (!move_uploaded_file($file['tmp_name'], UPLOAD_PRESCRIPTIONS . $filename)) return false;

        return $filename;
    }
}