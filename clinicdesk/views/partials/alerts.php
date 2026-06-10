<?php
$flash = getFlash();
if ($flash):
    $type = $flash['type'];
    $alertClass = match($type) {
        'success' => 'alert-success',
        'error'   => 'alert-danger',
        'warning' => 'alert-warning',
        'info'    => 'alert-info',
        default   => 'alert-info',
    };
    $icon = match($type) {
        'success' => 'fas fa-check-circle',
        'error'   => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info'    => 'fas fa-info-circle',
        default   => 'fas fa-info-circle',
    };
?>
<div class="alert <?= $alertClass ?> alert-dismissible fade show mx-3 mt-3" role="alert"
     style="border-radius:10px;border:none;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    <i class="<?= $icon ?> mr-2"></i>
    <strong><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></strong>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
<?php endif; ?>