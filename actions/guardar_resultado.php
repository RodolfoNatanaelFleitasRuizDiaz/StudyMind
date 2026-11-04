<?php
session_start(); require_once '../includes/auth.php'; require_login(); require_once '../config/database.php';
if($_SERVER['REQUEST_METHOD']!=='POST') {header('Location: ../pages/cuestionarios.php');exit;}
verify_csrf();$uid=(int)$_SESSION['usuario']['id'];$tema=(int)($_POST['tema_id']??0);$respuestas=$_POST['respuestas']??[];
$s=$conexion->prepare('SELECT t.id FROM temas t JOIN materias m ON m.id=t.materia_id WHERE t.id=? AND m.usuario_id=?');$s->execute([$tema,$uid]);if(!$s->fetch())exit('Tema no válido.');
if(!is_array($respuestas)||count($respuestas)===0)exit('No se recibieron respuestas.');
$ids=array_map('intval',array_keys($respuestas));$marks=implode(',',array_fill(0,count($ids),'?'));
$s=$conexion->prepare("SELECT id,respuesta_correcta FROM preguntas WHERE tema_id=? AND id IN ($marks)");$s->execute(array_merge([$tema],$ids));$correctas=$s->fetchAll();
if(count($correctas)!==count($ids))exit('Hay preguntas no válidas.');
$map=[];foreach($correctas as $r)$map[(int)$r['id']]=$r['respuesta_correcta'];
$total=count($map);$bien=0;foreach($respuestas as $id=>$op)if(isset($map[(int)$id])&&strtoupper($op)===$map[(int)$id])$bien++;
$puntuacion=round(($bien/$total)*100,2);
$conexion->beginTransaction();
try{
$s=$conexion->prepare('INSERT INTO intentos(usuario_id,tema_id,puntuacion,total_preguntas,respuestas_correctas) VALUES(?,?,?,?,?)');$s->execute([$uid,$tema,$puntuacion,$total,$bien]);$intento=(int)$conexion->lastInsertId();
$ins=$conexion->prepare('INSERT INTO respuestas(intento_id,pregunta_id,respuesta_seleccionada,es_correcta) VALUES(?,?,?,?)');
foreach($respuestas as $id=>$op)$ins->execute([$intento,(int)$id,strtoupper($op),isset($map[(int)$id])&&strtoupper($op)===$map[(int)$id]?1:0]);
$conexion->commit();header('Location: ../pages/resultados.php?id='.$intento);exit;
}catch(Throwable $e){$conexion->rollBack();error_log($e->getMessage());exit('No se pudo guardar el resultado.');}
