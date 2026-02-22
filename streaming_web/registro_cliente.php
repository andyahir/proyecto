<?php
require_once __DIR__ . '/includes/config.php';
// Esta página es PÚBLICA — no require admin

$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $ap       = trim($_POST['apellido_paterno'] ?? '');
    $am       = trim($_POST['apellido_materno'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($nombre && $ap && $correo && $password) {
        if ($password !== $confirm) {
            $err = 'Las contraseñas no coinciden.';
        } else {
            try {
                $db   = getDB();
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $db->prepare("INSERT INTO clientes (nombre,apellido_paterno,apellido_materno,correo,password_hash) VALUES (?,?,?,?,?)")
                   ->execute([$nombre,$ap,$am,$correo,$hash]);
                $id  = $db->lastInsertId();
                $row = $db->prepare("SELECT clave FROM clientes WHERE id=?");
                $row->execute([$id]);
                $clave = $row->fetchColumn();
                $msg = "¡Registro exitoso! Tu clave de acceso es: <strong>$clave</strong>. Ya puedes iniciar sesión en la app.";
            } catch (\Exception $e) {
                $err = 'El correo ya está registrado.';
            }
        }
    } else {
        $err = 'Todos los campos marcados con * son obligatorios.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StreamVault — Registrarme</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root{--navy:#0a1628;--navy-mid:#0f2044;--navy-card:#152a52;--blue:#1e4dd8;--blue-bright:#2e6cf0;--accent:#38bdf8;--text:#e8eef8;--text-muted:#7a93bc;--border:rgba(46,108,240,0.25);--danger:#f87171;--green:#34d399;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Sora',sans-serif;background:var(--navy);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;position:relative;overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:50px 50px;opacity:0.3;pointer-events:none;}
body::after{content:'';position:fixed;top:-150px;left:50%;transform:translateX(-50%);width:700px;height:400px;background:radial-gradient(ellipse,rgba(30,77,216,0.25) 0%,transparent 70%);pointer-events:none;}
.wrap{position:relative;z-index:10;width:500px;max-width:100%;animation:fadeUp 0.5s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px);}to{opacity:1;transform:translateY(0);}}
.brand{text-align:center;margin-bottom:28px;}
.brand-icon{width:54px;height:54px;background:linear-gradient(135deg,var(--blue),var(--accent));border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 0 30px rgba(56,189,248,0.25);}
.brand-icon svg{width:26px;height:26px;fill:#fff;}
.brand h1{font-size:1.6rem;font-weight:700;color:var(--text);}
.brand p{font-size:0.83rem;color:var(--text-muted);margin-top:4px;}
.card{background:var(--navy-card);border:1px solid var(--border);border-radius:20px;padding:36px 32px;box-shadow:0 20px 60px rgba(0,0,0,0.4);}
.card h2{font-size:1rem;font-weight:600;color:var(--text);margin-bottom:24px;text-align:center;}
.form-group{margin-bottom:16px;}
.form-group label{display:block;font-size:0.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:6px;}
.form-group input{width:100%;padding:11px 14px;background:var(--navy-mid);border:1px solid var(--border);border-radius:9px;color:var(--text);font-family:'Sora',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
.form-group input:focus{border-color:var(--blue-bright);box-shadow:0 0 0 3px rgba(46,108,240,0.18);}
.form-group input::placeholder{color:var(--text-muted);}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.btn-register{width:100%;padding:13px;background:linear-gradient(135deg,var(--blue),var(--blue-bright));color:#fff;font-family:'Sora',sans-serif;font-size:0.95rem;font-weight:600;border:none;border-radius:10px;cursor:pointer;letter-spacing:0.03em;transition:all 0.15s;box-shadow:0 4px 18px rgba(30,77,216,0.35);margin-top:4px;}
.btn-register:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(30,77,216,0.5);}
.alert{padding:12px 16px;border-radius:9px;font-size:0.87rem;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.alert-success{background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.3);color:var(--green);}
.alert-error{background:rgba(248,113,113,0.1);border:1px solid rgba(248,113,113,0.3);color:var(--danger);}
.back-link{text-align:center;margin-top:20px;font-size:0.82rem;color:var(--text-muted);}
.back-link a{color:var(--accent);text-decoration:none;}
</style>
</head>
<body>
<div class="wrap">
  <div class="brand">
    <div class="brand-icon">
      <svg viewBox="0 0 24 24"><path d="M4 3l16 9-16 9V3z"/></svg>
    </div>
    <h1>StreamVault</h1>
    <p>Crea tu cuenta para acceder al catálogo</p>
  </div>

  <div class="card">
    <h2>Crear Cuenta</h2>

    <?php if ($msg): ?>
    <div class="alert alert-success"><?= $msg ?></div>
    <?php elseif ($err): ?>
    <div class="alert alert-error"><?= $err ?></div>
    <?php endif; ?>

    <?php if (!$msg): ?>
    <form method="POST">
      <div class="grid2">
        <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" placeholder="Tu nombre" required></div>
        <div class="form-group"><label>Apellido Paterno *</label><input type="text" name="apellido_paterno" placeholder="Apellido" required></div>
      </div>
      <div class="form-group"><label>Apellido Materno</label><input type="text" name="apellido_materno" placeholder="Opcional"></div>
      <div class="form-group"><label>Correo electrónico *</label><input type="email" name="correo" placeholder="tu@correo.com" required></div>
      <div class="grid2">
        <div class="form-group"><label>Contraseña *</label><input type="password" name="password" placeholder="••••••••" required></div>
        <div class="form-group"><label>Confirmar *</label><input type="password" name="confirm" placeholder="••••••••" required></div>
      </div>
      <button type="submit" class="btn-register">Registrarme</button>
    </form>
    <?php endif; ?>

    <div class="back-link">
      ¿Ya tienes cuenta? <a href="index.php">Ir al Panel</a>
    </div>
  </div>
</div>
</body>
</html>
