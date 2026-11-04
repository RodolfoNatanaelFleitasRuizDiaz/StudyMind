<?php
session_start(); require_once '../includes/auth.php'; require_login(); require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id'];
$s=$conexion->prepare('SELECT t.id,t.nombre AS tema,m.nombre AS materia,COUNT(p.id) AS cantidad FROM temas t JOIN materias m ON m.id=t.materia_id LEFT JOIN preguntas p ON p.tema_id=t.id WHERE m.usuario_id=? GROUP BY t.id,t.nombre,m.nombre HAVING COUNT(p.id)>0 ORDER BY m.nombre,t.nombre');
$s->execute([$uid]);$temas=$s->fetchAll();
$pageTitle='Cuestionarios';$assetPrefix='../';$navPrefix='../';require '../includes/header.php';
?>
<h1 class="h3 mb-3">Cuestionarios</h1><div class="card p-4"><p class="text-secondary">Elegí un tema para comenzar a practicar.</p><?php if(!$temas): ?><p>No hay cuestionarios todavía. Primero agregá una materia y temas con preguntas.</p><?php else: foreach($temas as $t): ?><div class="d-flex justify-content-between align-items-center border-bottom py-3"><div><strong><?= htmlspecialchars($t['materia']) ?> — <?= htmlspecialchars($t['tema']) ?></strong><div class="small text-secondary"><?= (int)$t['cantidad'] ?> preguntas</div></div><a class="btn btn-sm btn-success" href="realizar_quiz.php?tema=<?= (int)$t['id'] ?>">Comenzar</a></div><?php endforeach; endif; ?></div>
<?php require '../includes/footer.php'; ?>
