<?php
session_start();
require_once '../includes/auth.php'; require_login();
require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id'];
$stmt=$conexion->prepare('SELECT COUNT(*) FROM materias WHERE usuario_id=?'); $stmt->execute([$uid]); $materias=(int)$stmt->fetchColumn();
$stmt=$conexion->prepare('SELECT COUNT(*), COALESCE(AVG(puntuacion),0) FROM intentos WHERE usuario_id=?'); $stmt->execute([$uid]); [$intentos,$promedio]=$stmt->fetch(PDO::FETCH_NUM);
$pageTitle='Panel principal'; $assetPrefix='../'; $navPrefix='../'; require_once '../includes/helpbit.php';
// Cuestionarios disponibles = temas con al menos una pregunta, de materias del usuario actual
// (misma definición que usa pages/cuestionarios.php).
$stmt=$conexion->prepare('SELECT COUNT(DISTINCT t.id) FROM temas t JOIN materias m ON m.id=t.materia_id JOIN preguntas p ON p.tema_id=t.id WHERE m.usuario_id=?'); $stmt->execute([$uid]); $disponibles=(int)$stmt->fetchColumn();
$intentos=(int)$intentos; $promedio=(float)$promedio;
$txtDisp=$disponibles===1?'1 cuestionario disponible':$disponibles.' cuestionarios disponibles';
if($disponibles===0){
    $saludo='¡Hola! Parece que todavía no tenés cuestionarios. ¡Vamos a crear el primero!';
    $mision='Agregar una materia con un tema y sus preguntas.';
}elseif($intentos===0){
    $saludo='Soy Helpbit, tu compañero de aprendizaje. Tenés '.$txtDisp.' para empezar a practicar.';
    $mision='Completar tu primer cuestionario.';
}else{
    $frase=$promedio>=80?'¡Excelente trabajo, seguí así!':($promedio>=60?'¡Buen avance! Un repaso más y lo mejorás.':'Cada intento suma. ¡Probá de nuevo para mejorar!');
    $saludo='¡Muy bien! Ya completaste '.($intentos===1?'1 cuestionario':$intentos.' cuestionarios').' con un promedio de '.number_format($promedio,1).'%. '.$frase;
    $mision='Seguir practicando: tenés '.$txtDisp.'.';
}
// Últimos 5 intentos del usuario actual (más reciente primero). Solo columnas existentes en intentos/temas/materias.
$stmt=$conexion->prepare('SELECT i.id,i.puntuacion,i.respuestas_correctas,i.total_preguntas,i.fecha,t.nombre AS tema,m.nombre AS materia FROM intentos i JOIN temas t ON t.id=i.tema_id JOIN materias m ON m.id=t.materia_id WHERE i.usuario_id=? ORDER BY i.fecha DESC,i.id DESC LIMIT 5'); $stmt->execute([$uid]); $ultimos=$stmt->fetchAll(PDO::FETCH_ASSOC);
require '../includes/header.php';
?>
<div class="hero mb-4"><h1 class="h2 fw-bold">¡Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>! 💚</h1><p class="mb-0">¿Qué te gustaría aprender hoy?</p></div>
<div class="helpbit-card mb-4">
<?= helpbit('feliz','../') ?>
<div class="helpbit-bubble"><div class="fw-semibold"><?= htmlspecialchars($saludo) ?></div>
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
<div class="card p-4 mt-4">
<h2 class="h5 mb-2">🏆 Últimos resultados</h2>
<?php if(!$ultimos): ?>
<p class="text-secondary mb-0">¡Todavía no tenés resultados! Completá tu primer cuestionario y Helpbit te acompañará.</p>
<?php else: foreach($ultimos as $u):
    $pct=max(0,min(100,(float)$u['puntuacion']));
    $color=$pct>=80?'var(--green)':($pct>=60?'var(--purple)':'#adb5bd');
    $fechaTxt=!empty($u['fecha'])?date('d/m/Y H:i',strtotime($u['fecha'])):'—';
?>
<a class="d-block text-decoration-none text-reset border-top py-3" href="resultados.php?id=<?= (int)$u['id'] ?>">
<div class="d-flex justify-content-between align-items-start gap-3">
<div><strong><?= htmlspecialchars($u['tema']) ?></strong><div class="small text-secondary"><?= htmlspecialchars($u['materia']) ?> · <?= htmlspecialchars($fechaTxt) ?></div></div>
<div class="text-end flex-shrink-0"><div class="fw-semibold"><?= (int)$u['respuestas_correctas'] ?>/<?= (int)$u['total_preguntas'] ?></div><div class="small text-secondary"><?= number_format($pct,0) ?>%</div></div>
</div>
<div class="progress mt-2" style="height:8px" role="progressbar" aria-label="Rendimiento" aria-valuenow="<?= (int)round($pct) ?>" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width:<?= $pct ?>%;background-color:<?= $color ?>"></div></div>
</a>
<?php endforeach; endif; ?>
</div>
<?php require '../includes/footer.php'; ?>