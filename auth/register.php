<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $nombre = trim($_POST['nombre'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Completá los datos. La contraseña debe tener al menos 8 caracteres.';
    } else {
        try {
            $stmt = $conexion->prepare('INSERT INTO usuarios(nombre,email,password) VALUES(?,?,?)');
            $stmt->execute([$nombre,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $_SESSION['usuario'] = ['id'=>(int)$conexion->lastInsertId(),'nombre'=>$nombre,'email'=>$email,'es_demo'=>0];
            header('Location: ../pages/dashboard.php'); exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000' ? 'Ese correo ya está registrado.' : 'No se pudo crear la cuenta.';
        }
    }
}
$bodyClass='login-bg'; $pageTitle='Crear cuenta'; $assetPrefix='../'; $navPrefix='../';
require '../includes/header.php';
?>
<div class="row justify-content-center"><div class="col-md-6 col-lg-5"><div class="card p-4">
<h1 class="h3 mb-1">Crear cuenta</h1><p class="text-secondary">Empezá a organizar tu aprendizaje.</p>
<?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
<label class="form-label">Nombre</label><input class="form-control mb-3" name="nombre" required maxlength="100">
<label class="form-label">Correo electrónico</label><input class="form-control mb-3" type="email" name="email" required>
<label class="form-label">Contraseña</label><input class="form-control mb-3" type="password" name="password" minlength="8" required>
<button class="btn btn-success w-100">Registrarme</button>
</form><p class="small mt-3 mb-0">¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
</div></div></div>
<?php require '../includes/footer.php'; ?>
