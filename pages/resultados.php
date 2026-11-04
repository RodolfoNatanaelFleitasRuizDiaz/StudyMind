<?php
session_start();require_once '../includes/auth.php';require_login();require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id'];$id=(int)($_GET['id']??0);
$s=$conexion->prepare('SELECT i.*,t.nombre AS tema FROM intentos i JOIN temas t ON t.id=i.tema_id WHERE i.id=? AND i.usuario_id=?');$s->execute([$id,$uid]);$r=$s->fetch();if(!$r)exit('Resultado no encontrado.');
$pageTitle='Resultado';$assetPrefix='../';$navPrefix='../';require_once '../includes/helpbit.php';
require '../includes/header.php';
?>
<div class="card p-5 text-center"><div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-3"><?= helpbit($r['puntuacion']>=80?'feliz':'pensando','../') ?><div class="helpbit-bubble"><?= $r['puntuacion']>=80 ? '¡ESO ES! ¡Sabía que podías hacerlo!' : ($r['puntuacion']>=60 ? '¡Buen avance! Un repaso más y lo tenés.' : 'No pasa nada, ¡podemos intentarlo otra vez!') ?></div></div><h1 class="h3">¡Cuestionario completado!</h1><div class="display-2 fw-bold text-success"><?= number_format((float)$r['puntuacion'],0) ?>%</div><p><?= (int)$r['respuestas_correctas'] ?> respuestas correctas de <?= (int)$r['total_preguntas'] ?></p><h2 class="h5"><?= htmlspecialchars($r['tema']) ?></h2>
<?php if($r['puntuacion']>=80): ?><p class="text-success">¡Excelente trabajo! Seguí así.</p><?php elseif($r['puntuacion']>=60): ?><p class="text-purple">Buen avance. Podés reforzar algunos conceptos.</p><?php else: ?><p class="text-secondary">No te desanimes. Te recomendamos repasar este tema y volver a intentarlo.</p><?php endif; ?>
<a class="btn btn-success mt-2" href="cuestionarios.php">Volver a cuestionarios</a></div>
<?php require '../includes/footer.php'; ?>
