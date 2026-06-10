<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Paginator.php';
require_once __DIR__ . '/../models/DoctorModel.php';
require_once __DIR__ . '/../models/SpecializationModel.php';
require_once __DIR__ . '/../models/RatingModel.php';

class DoctorController {

    private DoctorModel $doctorModel;
    private SpecializationModel $specializationModel;
    private RatingModel $ratingModel;

    public function __construct() {
        $this->doctorModel         = new DoctorModel();
        $this->specializationModel = new SpecializationModel();
        $this->ratingModel         = new RatingModel();
    }

    public function index(): void {
        Auth::requireRole('admin');

        $page      = max(1, (int)($_GET['p'] ?? 1));
        $total     = $this->doctorModel->countAll();
        $paginator = new Paginator($total, ITEMS_PER_PAGE, $page);
        $doctors   = $this->doctorModel->getAllPaginated($paginator->offset(), ITEMS_PER_PAGE);

        $pageTitle = 'Manage Doctors — ' . APP_NAME;
        require_once __DIR__ . '/../views/doctors/index.php';
    }

    public function edit(): void {
        Auth::requireRole('admin', 'doctor');

        $id = (int)($_GET['id'] ?? 0);

        // Doctor can only edit their own profile
        if (Auth::role() === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            if (!$doctor || $doctor['id'] !== $id) {
                flashMessage('error', 'Access denied.');
                redirect(BASE_URL . '/index.php?page=dashboard');
            }
        } else {
            $doctor = $this->doctorModel->findById($id);
        }

        if (!$doctor) {
            flashMessage('error', 'Doctor not found.');
            redirect(BASE_URL . '/index.php?page=doctors');
        }

        $specializations = $this->specializationModel->getAll();
        $ratings         = $this->ratingModel->getByDoctor($doctor['id']);
        $ratingStats     = $this->ratingModel->getAverageByDoctor($doctor['id']);

        $pageTitle = 'Edit Doctor — ' . APP_NAME;
        require_once __DIR__ . '/../views/doctors/edit.php';
    }

    public function update(): void {
        Auth::requireRole('admin', 'doctor');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=doctors');
        }

        $doctorId = (int)($_POST['doctor_id'] ?? 0);

        // Doctor can only update their own profile
        if (Auth::role() === 'doctor') {
            $doctor = $this->doctorModel->findByUserId(Auth::id());
            if (!$doctor || $doctor['id'] !== $doctorId) {
                flashMessage('error', 'Access denied.');
                redirect(BASE_URL . '/index.php?page=dashboard');
            }
        } else {
            $doctor = $this->doctorModel->findById($doctorId);
        }

        if (!$doctor) {
            flashMessage('error', 'Doctor not found.');
            redirect(BASE_URL . '/index.php?page=doctors');
        }

        $availableDays = isset($_POST['available_days'])
            ? implode(',', $_POST['available_days'])
            : 'Sun,Mon,Tue,Wed,Thu';

        // Handle photo upload
        $photo = $doctor['photo'];
        if (!empty($_FILES['photo']['name'])) {
            $uploaded = $this->handlePhotoUpload($_FILES['photo']);
            if ($uploaded === false) {
                flashMessage('error', 'Invalid image. Use JPEG or PNG under 1MB.');
                redirect(BASE_URL . '/index.php?page=doctors&action=edit&id=' . $doctorId);
            }
            if ($photo && file_exists(UPLOAD_PHOTOS . $photo)) {
                unlink(UPLOAD_PHOTOS . $photo);
            }
            $photo = $uploaded;
        }

        $this->doctorModel->update($doctorId, [
            'specialization_id' => (int)($_POST['specialization_id'] ?? 1),
            'bio'               => trim($_POST['bio'] ?? ''),
            'consultation_fee'  => (float)($_POST['consultation_fee'] ?? 0),
            'available_days'    => $availableDays,
            'photo'             => $photo,
        ]);

        flashMessage('success', 'Doctor profile updated successfully!');

        if (Auth::role() === 'doctor') {
            redirect(BASE_URL . '/index.php?page=dashboard');
        }
        redirect(BASE_URL . '/index.php?page=doctors');
    }

    public function specializations(): void {
        Auth::requireRole('admin');

        $specializations = $this->specializationModel->getAll();
        $pageTitle       = 'Specializations — ' . APP_NAME;
        require_once __DIR__ . '/../views/doctors/specializations.php';
    }

    public function addSpecialization(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=doctors&action=specializations');
        }

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            flashMessage('error', 'Specialization name is required.');
            redirect(BASE_URL . '/index.php?page=doctors&action=specializations');
        }

        $this->specializationModel->create($name);
        flashMessage('success', 'Specialization added successfully!');
        redirect(BASE_URL . '/index.php?page=doctors&action=specializations');
    }

    public function deleteSpecialization(): void {
        Auth::requireRole('admin');

        if (!CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flashMessage('error', 'Invalid request.');
            redirect(BASE_URL . '/index.php?page=doctors&action=specializations');
        }

        $id = (int)($_POST['id'] ?? 0);

        if (!$this->specializationModel->isSafeToDelete($id)) {
            flashMessage('error', 'Cannot delete — doctors are using this specialization.');
            redirect(BASE_URL . '/index.php?page=doctors&action=specializations');
        }

        $this->specializationModel->delete($id);
        flashMessage('success', 'Specialization deleted.');
        redirect(BASE_URL . '/index.php?page=doctors&action=specializations');
    }

    private function handlePhotoUpload(array $file): string|false {
        if ($file['error'] !== UPLOAD_ERR_OK) return false;
        if ($file['size'] > MAX_PHOTO_SIZE) return false;

        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) return false;

        $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
        if (!in_array($imageInfo[2], $allowedTypes)) return false;

        $ext      = $imageInfo[2] === IMAGETYPE_JPEG ? 'jpg' : 'png';
        $filename = 'doctor_' . uniqid() . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], UPLOAD_PHOTOS . $filename)) return false;

        return $filename;
    }
}