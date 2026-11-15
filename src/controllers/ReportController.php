<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/CSRF.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/AppointmentModel.php';
require_once __DIR__ . '/../models/DoctorModel.php';

class ReportController {

    private AppointmentModel $appointmentModel;
    private DoctorModel $doctorModel;

    public function __construct() {
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel      = new DoctorModel();
    }

    public function index(): void {
        Auth::requireRole('admin');

        $doctors   = $this->doctorModel->getAll();
        $filters   = [];
        $results   = [];
        $summary   = [];
        $hasSearch = false;

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $hasSearch = true;
            $filters   = [
                'start_date' => $_GET['start_date'],
                'end_date'   => $_GET['end_date'],
                'doctor_id'  => $_GET['doctor_id'] ?? '',
                'status'     => $_GET['status'] ?? '',
            ];

            // Validate dates
            if (strtotime($filters['start_date']) > strtotime($filters['end_date'])) {
                flashMessage('error', 'Start date must be before end date.');
                redirect(BASE_URL . '/index.php?page=reports');
            }

            $results = $this->appointmentModel->getForReport($filters);

            // Summary
            foreach ($results as $row) {
                $summary[$row['status']] = ($summary[$row['status']] ?? 0) + 1;
            }

            // Export CSV
            if (isset($_GET['export']) && $_GET['export'] === 'csv') {
                $this->exportCsv($results, $filters);
            }
        }

        $pageTitle = 'Reports — ' . APP_NAME;
        require_once __DIR__ . '/../views/reports/index.php';
    }

    private function exportCsv(array $results, array $filters): void {
        $filename = 'clinicdesk_report_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'Patient Name',
            'Doctor Name',
            'Specialization',
            'Date',
            'Time',
            'Status',
            'Reason',
        ]);

        // Data rows
        foreach ($results as $row) {
            fputcsv($output, [
                $row['patient_name'],
                $row['doctor_name'],
                $row['specialization_name'],
                $row['appt_date'],
                $row['appt_time'],
                ucfirst($row['status']),
                $row['reason'] ?? '',
            ]);
        }

        fclose($output);
        exit();
    }
}