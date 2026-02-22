<?php
require_once __DIR__ . '/includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM administradores WHERE (usuario = ? OR correo = ?) AND estatus = 'activo'");
        $stmt->execute([$usuario, $usuario]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id']     = $admin['id'];
            $_SESSION['admin_nombre'] = $admin['nombre'] . ' ' . $admin['apellido_paterno'];
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StreamVault — Iniciar Sesión</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --navy:      #0a1628;
    --navy-mid:  #0f2044;
    --navy-card: #152a52;
    --blue:      #1e4dd8;
    --blue-bright:#2e6cf0;
    --accent:    #38bdf8;
    --text:      #e8eef8;
    --text-muted:#7a93bc;
    --border:    rgba(46,108,240,0.25);
    --danger:    #f87171;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Sora', sans-serif;
    background: var(--navy);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  /* Background grid + glow */
  body::before {
    content: '';
    position: fixed; inset: 0;
    background-image:
      linear-gradient(var(--border) 1px, transparent 1px),
      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.4;
    pointer-events: none;
  }
  body::after {
    content: '';
    position: fixed;
    top: -200px; left: 50%;
    transform: translateX(-50%);
    width: 800px; height: 500px;
    background: radial-gradient(ellipse, rgba(30,77,216,0.3) 0%, transparent 70%);
    pointer-events: none;
  }

  .login-wrap {
    position: relative; z-index: 10;
    width: 420px;
    animation: fadeUp 0.6s ease both;
  }

  @keyframes fadeUp {
    from { opacity:0; transform: translateY(30px); }
    to   { opacity:1; transform: translateY(0); }
  }

  .brand {
    text-align: center;
    margin-bottom: 36px;
  }
  .brand-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, var(--blue), var(--accent));
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    box-shadow: 0 0 40px rgba(56,189,248,0.3);
  }
  .brand-icon svg { width: 32px; height: 32px; fill: #fff; }
  .brand h1 { font-size: 1.8rem; font-weight: 700; color: var(--text); letter-spacing: -0.5px; }
  .brand p  { font-size: 0.85rem; color: var(--text-muted); margin-top: 4px; }

  .card {
    background: var(--navy-card);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 40px 36px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.5);
  }

  .card h2 {
    font-size: 1.1rem; font-weight: 600;
    color: var(--text);
    margin-bottom: 28px;
    text-align: center;
  }

  .form-group { margin-bottom: 20px; }
  .form-group label {
    display: block;
    font-size: 0.78rem; font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.08em;
    margin-bottom: 8px;
  }
  .form-group input {
    width: 100%;
    padding: 12px 16px;
    background: var(--navy-mid);
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--text);
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .form-group input:focus {
    border-color: var(--blue-bright);
    box-shadow: 0 0 0 3px rgba(46,108,240,0.2);
  }
  .form-group input::placeholder { color: var(--text-muted); }

  .error-box {
    background: rgba(248,113,113,0.1);
    border: 1px solid rgba(248,113,113,0.3);
    border-radius: 10px;
    padding: 12px 16px;
    color: var(--danger);
    font-size: 0.88rem;
    margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
  }

  .btn-login {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--blue), var(--blue-bright));
    color: #fff;
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem; font-weight: 600;
    border: none; border-radius: 10px;
    cursor: pointer;
    letter-spacing: 0.03em;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 4px 20px rgba(30,77,216,0.4);
  }
  .btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 30px rgba(30,77,216,0.5);
  }
  .btn-login:active { transform: translateY(0); }

  .note {
    text-align: center;
    margin-top: 24px;
    font-size: 0.78rem;
    color: var(--text-muted);
  }
</style>
</head>
<body>

<div class="login-wrap">
  <div class="brand">
    <div class="brand-icon">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 3l16 9-16 9V3z"/>
      </svg>
    </div>
    <h1>StreamVault</h1>
    <p>Panel de Administración</p>
  </div>

  <div class="card">
    <h2>Inicio de Sesión</h2>

    <?php if ($error): ?>
    <div class="error-box">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
      </svg>
      <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Usuario</label>
        <input type="text" name="usuario" placeholder="Ingresa tu usuario o correo" required autofocus>
      </div>
      <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" placeholder="••••••••••••" required>
      </div>
      <button type="submit" class="btn-login">Ingresar</button>
    </form>

    <p class="note">Solo administradores tienen acceso a este panel.</p>
  </div>
</div>

</body>
</html>
