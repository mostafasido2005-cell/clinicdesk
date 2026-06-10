<?php
require_once __DIR__ . '/BaseModel.php';

class AppointmentModel extends BaseModel {

    public function book(array $data): bool {
        try {
            $result = $this->execute(
                'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason, status)
                 VALUES (?, ?, ?, ?, ?, "pending")',
                'iisss',
                [
                    $data['patient_id'],
                    $data['doctor_id'],
                    $data['appt_date'],
                    $data['appt_time'],
                    $data['reason'] ?? null,
                ]
            );
            return (bool) $result;
        } catch (RuntimeException $e) {
            return false;
        }
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool {
        $row = $this->fetchOne(
            'SELECT id FROM appointments 
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ? 
             AND status != "cancelled"',
            'iss', [$doctorId, $date, $time]
        );
        return $row !== null;
    }

    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT a.*, 
                    p.name as patient_name, p.email as patient_email, p.phone as patient_phone,
                    d.name as doctor_name, d.email as doctor_email,
                    doc.id as doctor_record_id, doc.consultation_fee,
                    s.name as specialization_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             WHERE a.id = ?',
            'i', [$id]
        );
    }

    public function getByPatient(int $patientId, int $offset, int $perPage, array $filters = []): array {
        $conditions = ['a.patient_id = ?'];
        $params = [$patientId];
        $types = 'i';

        $this->applyFilters($conditions, $params, $types, $filters);

        $where = 'WHERE ' . implode(' AND ', $conditions);

        return $this->fetchAll(
            "SELECT a.*, 
                    d.name as doctor_name,
                    s.name as specialization_name
             FROM appointments a
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             $where
             ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types . 'ii',
            array_merge($params, [$perPage, $offset])
        );
    }

    public function getByDoctor(int $doctorId, int $offset, int $perPage, array $filters = []): array {
        $conditions = ['a.doctor_id = ?'];
        $params = [$doctorId];
        $types = 'i';

        $this->applyFilters($conditions, $params, $types, $filters);

        $where = 'WHERE ' . implode(' AND ', $conditions);

        return $this->fetchAll(
            "SELECT a.*,
                    p.name as patient_name, p.phone as patient_phone
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             $where
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT ? OFFSET ?",
            $types . 'ii',
            array_merge($params, [$perPage, $offset])
        );
    }

    public function getAll(int $offset, int $perPage, array $filters = []): array {
        $conditions = [];
        $params = [];
        $types = '';

        $this->applyFilters($conditions, $params, $types, $filters);

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return $this->fetchAll(
            "SELECT a.*,
                    p.name as patient_name,
                    d.name as doctor_name,
                    s.name as specialization_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             $where
             ORDER BY a.appt_date DESC, a.appt_time DESC
             LIMIT ? OFFSET ?",
            $types . 'ii',
            array_merge($params, [$perPage, $offset])
        );
    }

    public function countFiltered(array $filters = [], string $scope = '', int $scopeId = 0): int {
        $conditions = [];
        $params = [];
        $types = '';

        if ($scope === 'patient') {
            $conditions[] = 'a.patient_id = ?';
            $params[] = $scopeId;
            $types .= 'i';
        } elseif ($scope === 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $params[] = $scopeId;
            $types .= 'i';
        }

        $this->applyFilters($conditions, $params, $types, $filters);

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $row = $this->fetchOne(
            "SELECT COUNT(*) as total FROM appointments a $where",
            $types, $params
        );
        return (int)($row['total'] ?? 0);
    }

    public function updateStatus(int $id, string $status, string $notes = '', int $cancelledBy = 0, string $cancelReason = ''): bool {
        if ($cancelledBy && $cancelReason) {
            $result = $this->execute(
                'UPDATE appointments SET status = ?, doctor_notes = ?, cancelled_by = ?, cancellation_reason = ? WHERE id = ?',
                'ssisi', [$status, $notes, $cancelledBy, $cancelReason, $id]
            );
        } else {
            $result = $this->execute(
                'UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?',
                'ssi', [$status, $notes, $id]
            );
        }
        return (bool) $result;
    }

    public function getTodayByDoctor(int $doctorId): array {
        return $this->fetchAll(
            'SELECT a.*, p.name as patient_name, p.phone as patient_phone
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
             ORDER BY a.appt_time ASC',
            'i', [$doctorId]
        );
    }

    public function getUpcoming(int $doctorId, int $limit = 5): array {
        return $this->fetchAll(
            'SELECT a.*, p.name as patient_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             WHERE a.doctor_id = ? AND a.appt_date >= CURDATE() AND a.status != "cancelled"
             ORDER BY a.appt_date ASC, a.appt_time ASC
             LIMIT ?',
            'ii', [$doctorId, $limit]
        );
    }

    public function getRecentAll(int $limit = 5): array {
        return $this->fetchAll(
            'SELECT a.*, p.name as patient_name, d.name as doctor_name, s.name as specialization_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             ORDER BY a.created_at DESC
             LIMIT ?',
            'i', [$limit]
        );
    }

    public function countToday(): int {
        $row = $this->fetchOne(
            'SELECT COUNT(*) as total FROM appointments WHERE appt_date = CURDATE()'
        );
        return (int)($row['total'] ?? 0);
    }

    public function countByStatus(): array {
        return $this->fetchAll(
            'SELECT status, COUNT(*) as total FROM appointments GROUP BY status'
        );
    }

    public function countThisWeekByStatus(): array {
        return $this->fetchAll(
            'SELECT status, COUNT(*) as total FROM appointments 
             WHERE WEEK(appt_date) = WEEK(NOW()) AND YEAR(appt_date) = YEAR(NOW())
             GROUP BY status'
        );
    }

    public function getLast14Days(): array {
        return $this->fetchAll(
            'SELECT appt_date, COUNT(*) as total FROM appointments
             WHERE appt_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY appt_date ORDER BY appt_date ASC'
        );
    }

    public function getForReport(array $filters): array {
        $conditions = [];
        $params = [];
        $types = '';

        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[] = $filters['start_date'];
            $types .= 's';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[] = $filters['end_date'];
            $types .= 's';
        }
        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $params[] = $filters['doctor_id'];
            $types .= 'i';
        }
        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return $this->fetchAll(
            "SELECT a.*, p.name as patient_name, p.phone as patient_phone,
                    d.name as doctor_name, s.name as specialization_name
             FROM appointments a
             JOIN users p ON a.patient_id = p.id
             JOIN doctors doc ON a.doctor_id = doc.id
             JOIN users d ON doc.user_id = d.id
             JOIN specializations s ON doc.specialization_id = s.id
             $where
             ORDER BY a.appt_date ASC, a.appt_time ASC",
            $types, $params
        );
    }

    private function applyFilters(array &$conditions, array &$params, string &$types, array $filters): void {
        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $params[] = $filters['start_date'];
            $types .= 's';
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $params[] = $filters['end_date'];
            $types .= 's';
        }
        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $params[] = (int)$filters['doctor_id'];
            $types .= 'i';
        }
        if (!empty($filters['patient_name'])) {
            $conditions[] = 'p.name LIKE ?';
            $params[] = '%' . $filters['patient_name'] . '%';
            $types .= 's';
        }
    }
}