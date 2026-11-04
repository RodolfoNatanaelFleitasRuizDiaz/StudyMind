<?php
session_start();
require_once '../includes/auth.php'; require_login();
require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id'];
$stmt=$conexion->prepare('SELECT COUNT(*) FROM materias WHERE usuario_id=?'); $stmt->execute([$uid]); $materias=(int)$stmt->fetchColumn();
$stmt=$conexion->prepare('SELECT COUNT(*), COALESCE(AVG(puntuacion),0) FROM intentos WHERE usuario_id=?'); $stmt->execute([$uid]); [$intentos,$promedio]=$stmt->fetch(PDO::FETCH_NUM);
$pageTitle='Panel principal'; $assetPrefix='../'; $navPrefix='../'; require_once '../includes/helpbit.php';
$mision=((int)$intentos===0)?'Completar tu primer cuestionario.':'Completar un cuestionario y repasar un tema pendiente.';
require '../includes/header.php';
?>
<div class="hero mb-4"><h1 class="h2 fw-bold">¡Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>! 💚</h1><p class="mb-0">¿Qué te gustaría aprender hoy?</p></div>
<div class="helpbit-card mb-4">
<?= helpbit('feliz','../') ?>
<div class="helpbit-bubble"><div class="fw-semibold">Soy Helpbit, tu compañero de aprendizaje. ¿Listo para aprender algo nuevo? 💚</div>
<div class="small mt-2"><strong>Tu misión de hoy:</strong> <?= htmlspecialchars($mision) ?></div>
<div class="small text-purple mt-1">¡Vos podés!</div></div></div>
<?php if(!empty($_SESSION['usuario']['es_demo'])): ?><div class="alert alert-info">Estás usando la cuenta de demostración. Los datos son compartidos para mostrar StudyMind.</div><?php endif; ?>
<div class="row g-3 mb-4">
<div class="col-md-4"><div class="card p-3"><div class="text-secondary small">Mis materias</div><div class="display-6 fw-semibold"><?= $materias ?></div></div></div>
<div class="col-md-4"><div class="card p-3"><div class="text-secondary small">Cuestionarios realizados</div><div class="display-6 fw-semibold"><?= (int)$intentos ?></div></div></div>
<div class="col-md-4"><div class="card p-3"><div class="text-secondary small">Promedio de resultados</div><div class="display-6 fw-semibold"><?= number_format((float)$promedio,1) ?>%</div></div></div>
</div>
<div class="row g-3">
<div class="col-md-6"><div class="card p-4 h-100"><h2 class="h5">📚 Organizar materias</h2><p class="text-secondary">Creá materias y agregá temas para preparar tus cuestionarios.</p><a class="btn btn-success" href="materias.php">Ver mis materias</a></div></div>
<div class="col-md-6"><div class="card p-4 h-100"><h2 class="h5">🧠 Practicar</h2><p class="text-secondary">Respondé preguntas y revisá tus resultados.</p><a class="btn btn-outline-success" href="cuestionarios.php">Ir a cuestionarios</a></div></div>
</div>
<?php require '../includes/footer.php'; ?>
