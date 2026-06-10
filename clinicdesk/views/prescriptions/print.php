<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/helpers.php';
Auth::requireRole('admin', 'doctor', 'patient');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription #<?= $prescription['id'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 40px; color: #333; }
        .header { text-align: center; border-bottom: 3px solid #3a7bd5; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #3a7bd5; font-size: 28px; }
        .header p { color: #666; font-size: 14px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .info-box h4 { color: #3a7bd5; margin-bottom: 8px; font-size: 13px; text-transform: uppercase; }
        .info-box p { font-size: 15px; font-weight: bold; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #3a7bd5; border-bottom: 1px solid #e0e0e0; padding-bottom: 8px; margin-bottom: 12px; }
        .section p { font-size: 14px; line-height: 1.8; white-space: pre-line; }
        .footer { margin-top: 50px; text-align: right; border-top: 1px solid #e0e0e0; padding-top: 20px; }
        .footer p { font-size: 13px; color: #666; }
        .stamp { display: inline-block; border: 2px solid #3a7bd5; padding: 10px 20px; border-radius: 8px; color: #3a7bd5; font-weight: bold; margin-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 ClinicDesk</h1>
        <p>Medical Prescription</p>
        <p>Date: <?= formatDate($prescription['appt_date']) ?></p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>Patient</h4>
            <p><?= htmlspecialchars($prescription['patient_name'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="info-box">
            <h4>Doctor</h4>
            <p>Dr. <?= htmlspecialchars($prescription['doctor_name'], ENT_QUOTES, 'UTF-8') ?></p>
            <small><?= htmlspecialchars($prescription['specialization_name'], ENT_QUOTES, 'UTF-8') ?></small>
        </div>
        <div class="info-box">
            <h4>Appointment Date</h4>
            <p><?= formatDate($prescription['appt_date']) ?> at <?= formatTime($prescription['appt_time']) ?></p>
        </div>
        <div class="info-box">
            <h4>Prescription ID</h4>
            <p>#<?= $prescription['id'] ?></p>
        </div>
    </div>

    <div class="section">
        <h3>Diagnosis</h3>
        <p><?= htmlspecialchars($prescription['diagnosis'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="section">
        <h3>Medications</h3>
        <p><?= htmlspecialchars($prescription['medications'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <?php if ($prescription['notes']): ?>
    <div class="section">
        <h3>Additional Notes</h3>
        <p><?= htmlspecialchars($prescription['notes'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Issued by: Dr. <?= htmlspecialchars($prescription['doctor_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><?= htmlspecialchars($prescription['specialization_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <div class="stamp">Doctor Signature</div>
    </div>

    <div class="no-print" style="text-align:center;margin-top:30px;">
        <button onclick="window.print()"
                style="background:#3a7bd5;color:white;border:none;padding:10px 30px;border-radius:8px;cursor:pointer;font-size:15px;">
            🖨️ Print
        </button>
        <button onclick="window.close()"
                style="background:#6c757d;color:white;border:none;padding:10px 30px;border-radius:8px;cursor:pointer;font-size:15px;margin-left:10px;">
            Close
        </button>
    </div>
</body>
</html>