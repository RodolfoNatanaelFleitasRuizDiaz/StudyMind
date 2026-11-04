<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $email=strtolower(trim($_POST['email'] ?? '')); $password=$_POST['password'] ?? '';
    $stmt=$conexion->prepare('SELECT id,nombre,email,password,es_demo FROM usuarios WHERE email=?');
    $stmt->execute([$email]); $u=$stmt->fetch();
    if($u && password_verify($password,$u['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario']=['id'=>(int)$u['id'],'nombre'=>$u['nombre'],'email'=>$u['email'],'es_demo'=>(int)$u['es_demo']];
        header('Location: ../pages/dashboard.php'); exit;
    } else $error='Correo o contraseña incorrectos.';
}
$bodyClass='login-bg'; $pageTitle='Iniciar sesión'; $assetPrefix='../'; $navPrefix='../';
require_once '../includes/helpbit.php';
require '../includes/header.php';
?>
<div class="row justify-content-center align-items-center g-4 py-lg-4"><div class="col-lg-5 text-center text-lg-start">
<div class="d-flex flex-column flex-lg-row align-items-center gap-3 mb-3">
<?= helpbit('feliz','../','lg') ?>
<div class="helpbit-bubble"><strong>¡Hola! ¡Qué bueno verte!</strong><br><span class="small">Soy Helpbit, tu compañero de estudio.</span></div></div>
<h1 class="h2 fw-bold">Aprender, paso a paso.</h1><p class="mb-4 mb-lg-0">Tu espacio para estudiar mejor.</p></div>
<div class="col-md-8 col-lg-5"><div class="card p-4">
<h2 class="h4">Bienvenido a StudyMind</h2>
<?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<label class="form-label">Correo</label><input class="form-control mb-3" type="email" name="email" required>
<label class="form-label">Contraseña</label><input class="form-control mb-3" type="password" name="password" required>
<button class="btn btn-success w-100">Ingresar</button>
</form>
<div class="soft-purple rounded-3 p-3 mt-3 small"><strong>Acceso de demostración</strong><br>Correo: demo@studymind.local<br>Contraseña: StudyMind123!</div>
<p class="small mt-3 mb-0">¿No tenés cuenta? <a href="register.php">Registrate</a></p>
</div></div></div>
<?php require '../includes/footer.php'; ?>
