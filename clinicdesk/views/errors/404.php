<?php
$pageTitle = '404 — Page Not Found';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="hold-transition" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div class="text-center" style="color: white;">
        <h1 style="font-size: 120px; font-weight: 900; margin: 0; color: #3498db; text-shadow: 0 0 30px rgba(52,152,219,0.5);">404</h1>
        <h2 style="font-size: 32px; margin: 10px 0;">Page Not Found</h2>
        <p style="font-size: 18px; opacity: 0.8; margin-bottom: 30px;">The page you're looking for doesn't exist.</p>
        <a href="<?= BASE_URL ?>/index.php?page=dashboard" 
           style="background: #3498db; color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-size: 16px; margin-right: 10px;">
            <i class="fas fa-home"></i> Go to Dashboard
        </a>
        <a href="javascript:history.back()" 
           style="background: transparent; color: white; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-size: 16px; border: 2px solid white;">
            <i class="fas fa-arrow-left"></i> Go Back
        </a>
    </div>
</body>
</html>