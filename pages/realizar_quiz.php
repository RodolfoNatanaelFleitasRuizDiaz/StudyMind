<?php
session_start(); require_once '../includes/auth.php'; require_login(); require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id'];$tema=(int)($_GET['tema']??0);
$s=$conexion->prepare('SELECT t.nombre AS tema,m.nombre AS materia FROM temas t JOIN materias m ON m.id=t.materia_id WHERE t.id=? AND m.usuario_id=?');$s->execute([$tema,$uid]);$info=$s->fetch();
if(!$info){http_response_code(404);exit('Tema no encontrado.');}
$s=$conexion->prepare('SELECT id,pregunta,opcion_a,opcion_b,opcion_c,opcion_d FROM preguntas WHERE tema_id=? ORDER BY RAND() LIMIT 10');$s->execute([$tema]);$preguntas=$s->fetchAll();
if(!$preguntas) exit('Este tema todavía no tiene preguntas.');
$pageTitle='Resolver cuestionario';$assetPrefix='../';$navPrefix='../';require_once '../includes/helpbit.php';
require '../includes/header.php';
?>
<div class="d-flex align-items-center gap-3 mb-2"><?= helpbit('estudiando','../','sm') ?><div class="helpbit-bubble small">¡Vamos a poner esas neuronas a trabajar!</div></div>
<h1 class="h3"><?= htmlspecialchars($info['tema']) ?></h1><p class="text-secondary"><?= htmlspecialchars($info['materia']) ?> · <?= count($preguntas) ?> preguntas</p>
<form method="post" action="../actions/guardar_resultado.php" class="card p-4">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="tema_id" value="<?= $tema ?>">
<?php foreach($preguntas as $i=>$p): ?><fieldset class="mb-4"><legend class="h6"><?= ($i+1).'. '.htmlspecialchars($p['pregunta']) ?></legend>
<?php foreach(['a','b','c','d'] as $op): ?><label class="option-label"><input type="radio" required name="respuestas[<?= (int)$p['id'] ?>]" value="<?= strtoupper($op) ?>"> <?= strtoupper($op) ?>. <?= htmlspecialchars($p['opcion_'.$op]) ?></label><?php endforeach; ?></fieldset><?php endforeach; ?>
<button class="btn btn-success">Enviar respuestas</button></form>
<?php require '../includes/footer.php'; ?>