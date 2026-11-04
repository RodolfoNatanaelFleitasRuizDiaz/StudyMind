<?php
session_start(); require_once '../includes/auth.php'; require_login(); require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id']; $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){verify_csrf();$nombre=trim($_POST['nombre']??'');$desc=trim($_POST['descripcion']??'');if($nombre!==''){$s=$conexion->prepare('INSERT INTO materias(usuario_id,nombre,descripcion) VALUES(?,?,?)');$s->execute([$uid,$nombre,$desc]);$msg='Materia agregada correctamente.';}}
$s=$conexion->prepare('SELECT * FROM materias WHERE usuario_id=? ORDER BY nombre');$s->execute([$uid]);$lista=$s->fetchAll();
$pageTitle='Mis materias';$assetPrefix='../';$navPrefix='../';require '../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Mis materias</h1><a class="btn btn-outline-success" href="crear_contenido.php">Crear temas y preguntas</a></div><?php if($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="row g-3"><div class="col-lg-5"><div class="card p-4"><h2 class="h5">Agregar materia</h2><form method="post"><input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>"><label class="form-label">Nombre</label><input class="form-control mb-3" name="nombre" maxlength="100" required><label class="form-label">Descripción</label><textarea class="form-control mb-3" name="descripcion" rows="3"></textarea><button class="btn btn-success">Guardar materia</button></form></div></div>
<div class="col-lg-7"><div class="card p-4"><h2 class="h5">Materias registradas</h2><?php if(!$lista): ?><p class="text-secondary mb-0">Todavía no agregaste materias.</p><?php else: foreach($lista as $m): ?><div class="border-bottom py-2"><strong><?= htmlspecialchars($m['nombre']) ?></strong><div class="small text-secondary"><?= htmlspecialchars($m['descripcion']??'') ?></div></div><?php endforeach; endif; ?></div></div></div>
<?php require '../includes/footer.php'; ?>
