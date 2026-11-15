<?php
require_once __DIR__ . '/BaseModel.php';

class RatingModel extends BaseModel {

    public function create(array $data): int {
        $this->execute(
            'INSERT INTO ratings (appointment_id, patient_id, doctor_id, rating, comment)
             VALUES (?, ?, ?, ?, ?)',
            'iiiis',
            [
                $data['appointment_id'],
                $data['patient_id'],
                $data['doctor_id'],
                $data['rating'],
                $data['comment'] ?? null,
            ]
        );
        return $this->lastInsertId();
    }

    public function findByAppointmentId(int $apptId): ?array {
        return $this->fetchOne(
            'SELECT r.*, p.name as patient_name
             FROM ratings r
             JOIN users p ON r.patient_id = p.id
             WHERE r.appointment_id = ?',
            'i', [$apptId]
        );
    }

    public function getByDoctor(int $doctorId): array {
        return $this->fetchAll(
            'SELECT r.*, p.name as patient_name, a.appt_date
             FROM ratings r
             JOIN users p ON r.patient_id = p.id
             JOIN appointments a ON r.appointment_id = a.id
             WHERE r.doctor_id = ?
             ORDER BY r.created_at DESC',
            'i', [$doctorId]
        );
    }

    public function getAverageByDoctor(int $doctorId): array {
        $row = $this->fetchOne(
            'SELECT AVG(rating) as avg_rating, COUNT(*) as total
             FROM ratings WHERE doctor_id = ?',
            'i', [$doctorId]
        );
        return [
            'avg'   => round((float)($row['avg_rating'] ?? 0), 1),
            'total' => (int)($row['total'] ?? 0),
        ];
    }

    public function hasRated(int $appointmentId, int $patientId): bool {
        $row = $this->fetchOne(
            'SELECT id FROM ratings WHERE appointment_id = ? AND patient_id = ?',
            'ii', [$appointmentId, $patientId]
        );
        return $row !== null;
    }
}