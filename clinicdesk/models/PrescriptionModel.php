<?php
require_once __DIR__ . '/BaseModel.php';

class PrescriptionModel extends BaseModel {

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT pr.*, a.appt_date, a.appt_time, a.patient_id, a.doctor_id,
                    p.name as patient_name,
                    d.name as doctor_name,
                    s.name as specialization_name
             FROM prescriptions pr
             JOIN appointments a ON pr.appointment_id = a.id
             JOIN users p ON a.patient_id = p.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             WHERE pr.id = ?',
            'i', [$id]
        );
    }

    public function findByAppointmentId(int $apptId): ?array {
        return $this->fetchOne(
            'SELECT pr.*, a.appt_date, a.appt_time, a.patient_id, a.doctor_id,
                    p.name as patient_name,
                    d.name as doctor_name,
                    s.name as specialization_name
             FROM prescriptions pr
             JOIN appointments a ON pr.appointment_id = a.id
             JOIN users p ON a.patient_id = p.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             WHERE pr.appointment_id = ?',
            'i', [$apptId]
        );
    }

    public function create(array $data): int {
        $this->execute(
            'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
             VALUES (?, ?, ?, ?, ?)',
            'issss',
            [
                $data['appointment_id'],
                $data['diagnosis'],
                $data['medications'],
                $data['notes'] ?? null,
                $data['file_path'] ?? null,
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $result = $this->execute(
            'UPDATE prescriptions SET diagnosis = ?, medications = ?, notes = ?, file_path = ?
             WHERE id = ?',
            'ssssi',
            [
                $data['diagnosis'],
                $data['medications'],
                $data['notes'] ?? null,
                $data['file_path'] ?? null,
                $id,
            ]
        );
        return (bool) $result;
    }

    public function getByPatient(int $patientId): array {
        return $this->fetchAll(
            'SELECT pr.*, a.appt_date, a.appt_time,
                    d.name as doctor_name,
                    s.name as specialization_name
             FROM prescriptions pr
             JOIN appointments a ON pr.appointment_id = a.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             WHERE a.patient_id = ?
             ORDER BY pr.created_at DESC',
            'i', [$patientId]
        );
    }

    public function countByPatient(int $patientId): int {
        $row = $this->fetchOne(
            'SELECT COUNT(*) as total FROM prescriptions pr
             JOIN appointments a ON pr.appointment_id = a.id
             WHERE a.patient_id = ?',
            'i', [$patientId]
        );
        return (int)($row['total'] ?? 0);
    }
}