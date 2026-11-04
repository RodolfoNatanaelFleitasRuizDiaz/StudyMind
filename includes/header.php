<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$pageTitle = $pageTitle ?? 'StudyMind';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?> | StudyMind</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= $assetPrefix ?? '../' ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '') ?>">
<?php if (($bodyClass ?? '') === 'login-bg'): ?><div class="bg-orb o1"></div><div class="bg-orb o2"></div><div class="bg-orb o3"></div><?php endif; ?>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $navPrefix ?? '../' ?>pages/dashboard.php">Study<span>Mind</span></a>
    <?php if (!empty($_SESSION['usuario'])): ?>
      <div class="d-flex align-items-center gap-3">
        <span class="small text-secondary">Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></span>
        <a class="btn btn-sm btn-outline-success" href="<?= $navPrefix ?? '../' ?>auth/logout.php">Salir</a>
      </div>
    <?php endif; ?>
  </div>
</nav>
<main class="container py-4">
