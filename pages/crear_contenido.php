<?php
session_start();require_once '../includes/auth.php';require_login();require_once '../config/database.php';
$uid=(int)$_SESSION['usuario']['id'];$mensaje='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$accion=$_POST['accion']??'';
 if($accion==='tema'){
  $materia=(int)($_POST['materia_id']??0);$nombre=trim($_POST['nombre_tema']??'');
  $s=$conexion->prepare('SELECT id FROM materias WHERE id=? AND usuario_id=?');$s->execute([$materia,$uid]);
  if($s->fetch()&&$nombre!==''){$s=$conexion->prepare('INSERT INTO temas(materia_id,nombre) VALUES(?,?)');$s->execute([$materia,$nombre]);$mensaje='Tema creado.';}
 } elseif($accion==='pregunta'){
  $tema=(int)($_POST['tema_id']??0);$s=$conexion->prepare('SELECT t.id FROM temas t JOIN materias m ON m.id=t.materia_id WHERE t.id=? AND m.usuario_id=?');$s->execute([$tema,$uid]);
  $vals=[trim($_POST['pregunta']??''),trim($_POST['a']??''),trim($_POST['b']??''),trim($_POST['c']??''),trim($_POST['d']??''),$_POST['correcta']??'A',trim($_POST['explicacion']??'')];
  if($s->fetch()&& !in_array('',array_slice($vals,0,5),true)){
   $ins=$conexion->prepare('INSERT INTO preguntas(tema_id,creador_id,pregunta,opcion_a,opcion_b,opcion_c,opcion_d,respuesta_correcta,explicacion) VALUES(?,?,?,?,?,?,?,?,?)');
   $ins->execute(array_merge([$tema,$uid],$vals));$mensaje='Pregunta agregada correctamente.';
  }else $mensaje='Revisá los datos e intentá nuevamente.';
 }
}
$s=$conexion->prepare('SELECT id,nombre FROM materias WHERE usuario_id=? ORDER BY nombre');$s->execute([$uid]);$materias=$s->fetchAll();
$s=$conexion->prepare('SELECT t.id,t.nombre,m.nombre AS materia FROM temas t JOIN materias m ON m.id=t.materia_id WHERE m.usuario_id=? ORDER BY m.nombre,t.nombre');$s->execute([$uid]);$temas=$s->fetchAll();
$pageTitle='Crear contenido';$assetPrefix='../';$navPrefix='../';require '../includes/header.php';
?>
<h1 class="h3 mb-3">Crear contenido</h1>
<?php if($mensaje):?><div class="alert alert-info"><?=htmlspecialchars($mensaje)?></div><?php endif;?>
<div class="row g-3">
<div class="col-lg-5"><div class="card p-4"><h2 class="h5">Agregar tema</h2><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(csrf_token())?>"><input type="hidden" name="accion" value="tema"><label class="form-label">Materia</label><select class="form-select mb-3" name="materia_id" required><?php foreach($materias as $m):?><option value="<?=$m['id']?>"><?=htmlspecialchars($m['nombre'])?></option><?php endforeach;?></select><label class="form-label">Nombre del tema</label><input class="form-control mb-3" name="nombre_tema" required><button class="btn btn-success">Guardar tema</button></form></div></div>
<div class="col-lg-7"><div class="card p-4"><h2 class="h5">Crear pregunta</h2><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(csrf_token())?>"><input type="hidden" name="accion" value="pregunta"><label class="form-label">Tema</label><select class="form-select mb-3" name="tema_id" required><?php foreach($temas as $t):?><option value="<?=$t['id']?>"><?=htmlspecialchars($t['materia'].' — '.$t['nombre'])?></option><?php endforeach;?></select><label class="form-label">Pregunta</label><textarea class="form-control mb-3" name="pregunta" required></textarea>
<?php foreach(['a','b','c','d'] as $op):?><label class="form-label">Opción <?=strtoupper($op)?></label><input class="form-control mb-2" name="<?=$op?>" required><?php endforeach;?>
<label class="form-label mt-2">Respuesta correcta</label><select class="form-select mb-3" name="correcta"><?php foreach(['A','B','C','D'] as $op):?><option><?=$op?></option><?php endforeach;?></select><label class="form-label">Explicación (opcional)</label><textarea class="form-control mb-3" name="explicacion"></textarea><button class="btn btn-success">Guardar pregunta</button></form></div></div></div>
<?php require '../includes/footer.php';?>
