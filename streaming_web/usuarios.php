<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Usuarios Administradores';
require_once __DIR__ . '/includes/header.php';

$db  = getDB();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'registrar') {
        $nombre   = trim($_POST['nombre'] ?? '');
        $ap       = trim($_POST['apellido_paterno'] ?? '');
        $am       = trim($_POST['apellido_materno'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');
        $usuario  = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($nombre && $ap && $correo && $usuario && $password) {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $db->prepare("INSERT INTO administradores (nombre,apellido_paterno,apellido_materno,correo,usuario,password_hash) VALUES (?,?,?,?,?,?)")
                   ->execute([$nombre,$ap,$am,$correo,$usuario,$hash]);
                $msg = 'Administrador registrado correctamente.';
            } catch (\Exception $e) {
                $err = 'Error: usuario o correo ya en uso.';
            }
        } else { $err = 'Todos los campos marcados con * son obligatorios.'; }
    }

    if ($action === 'activar' || $action === 'inactivar') {
        $id  = intval($_POST['id']);
        if ($id == $_SESSION['admin_id']) { $err = 'No puedes cambiar tu propio estatus.'; }
        else {
            $est = $action === 'activar' ? 'activo' : 'inactivo';
            $db->prepare("UPDATE administradores SET estatus=? WHERE id=?")->execute([$est,$id]);
            $msg = 'Estatus actualizado.';
        }
    }

    if ($action === 'eliminar') {
        $id = intval($_POST['id']);
        if ($id == $_SESSION['admin_id']) { $err = 'No puedes eliminarte a ti mismo.'; }
        else {
            $db->prepare("DELETE FROM administradores WHERE id=?")->execute([$id]);
            $msg = 'Administrador eliminado.';
        }
    }

    if ($action === 'actualizar') {
        $id      = intval($_POST['id']);
        $nombre  = trim($_POST['nombre'] ?? '');
        $ap      = trim($_POST['apellido_paterno'] ?? '');
        $am      = trim($_POST['apellido_materno'] ?? '');
        $correo  = trim($_POST['correo'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $db->prepare("UPDATE administradores SET nombre=?,apellido_paterno=?,apellido_materno=?,correo=?,usuario=? WHERE id=?")
           ->execute([$nombre,$ap,$am,$correo,$usuario,$id]);
        $msg = 'Administrador actualizado.';
    }
}

$admins = $db->query("SELECT id,nombre,apellido_paterno,apellido_materno,correo,usuario,estatus,created_at FROM administradores ORDER BY created_at DESC")->fetchAll();
?>

<div class="main">
  <div class="section-header"><h2>Registro de Administradores</h2></div>

  <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= $err ?></div><?php endif; ?>

  <div class="card">
    <form method="POST">
      <input type="hidden" name="action" value="registrar">
      <div class="form-grid">
        <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" placeholder="Nombre" required></div>
        <div class="form-group"><label>Usuario *</label><input type="text" name="usuario" placeholder="usuario_admin" required></div>
        <div class="form-group"><label>Apellido Paterno *</label><input type="text" name="apellido_paterno" required></div>
        <div class="form-group"><label>Apellido Materno</label><input type="text" name="apellido_materno"></div>
        <div class="form-group"><label>Correo *</label><input type="email" name="correo" placeholder="admin@correo.com" required></div>
        <div class="form-group"><label>Contraseña *</label><input type="password" name="password" required></div>
      </div>
      <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
            <line x1="19" y1="8" x2="23" y2="8"/><line x1="21" y1="6" x2="21" y2="10"/>
          </svg>
          Registrar Administrador
        </button>
      </div>
    </form>
  </div>

  <div class="card" style="padding:0;margin-top:8px;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nombre Completo</th>
            <th>Usuario</th>
            <th>Correo</th>
            <th>Fecha Registro</th>
            <th>Estatus</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($admins as $a): ?>
        <tr>
          <td><?= htmlspecialchars($a['nombre'].' '.$a['apellido_paterno'].' '.$a['apellido_materno']) ?></td>
          <td><code style="font-family:'DM Mono',monospace;font-size:0.8rem;color:var(--accent);"><?= htmlspecialchars($a['usuario']) ?></code></td>
          <td><?= htmlspecialchars($a['correo']) ?></td>
          <td style="font-size:0.8rem;color:var(--text-muted);"><?= date('d/m/Y H:i',strtotime($a['created_at'])) ?></td>
          <td><span class="badge badge-<?= $a['estatus']==='activo'?'active':'inactive' ?>"><?= $a['estatus'] ?></span></td>
          <td>
            <div class="td-actions">
              <?php if ($a['id'] != $_SESSION['admin_id']): ?>
              <form method="POST" style="display:contents;">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <?php if ($a['estatus']==='inactivo'): ?>
                <button name="action" value="activar" class="btn btn-sm btn-success">Activar</button>
                <?php else: ?>
                <button name="action" value="inactivar" class="btn btn-sm btn-danger">Inactivar</button>
                <?php endif; ?>
                <button name="action" value="eliminar" class="btn btn-sm btn-danger"
                  onclick="return confirm('¿Eliminar este administrador?')">Eliminar</button>
              </form>
              <button class="btn btn-sm btn-warning"
                onclick="openEditModal(<?= $a['id'] ?>,'<?= htmlspecialchars($a['nombre'],ENT_QUOTES) ?>','<?= htmlspecialchars($a['apellido_paterno'],ENT_QUOTES) ?>','<?= htmlspecialchars($a['apellido_materno'],ENT_QUOTES) ?>','<?= htmlspecialchars($a['correo'],ENT_QUOTES) ?>','<?= htmlspecialchars($a['usuario'],ENT_QUOTES) ?>')">
                Actualizar
              </button>
              <?php else: ?>
              <span style="font-size:0.75rem;color:var(--accent);">Tu cuenta</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL EDITAR -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center;">
  <div style="background:var(--navy-card);border:1px solid var(--border);border-radius:var(--radius);padding:32px;width:500px;max-width:95vw;">
    <h3 style="margin-bottom:20px;font-size:1.1rem;">Actualizar Administrador</h3>
    <form method="POST">
      <input type="hidden" name="action" value="actualizar">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-grid">
        <div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nombre" required></div>
        <div class="form-group"><label>Usuario</label><input type="text" name="usuario" id="edit_usuario" required></div>
        <div class="form-group"><label>Apellido Paterno</label><input type="text" name="apellido_paterno" id="edit_ap" required></div>
        <div class="form-group"><label>Apellido Materno</label><input type="text" name="apellido_materno" id="edit_am"></div>
        <div class="form-group full"><label>Correo</label><input type="email" name="correo" id="edit_correo" required></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:20px;">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-warning" onclick="document.getElementById('editModal').style.display='none'">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(id,nombre,ap,am,correo,usuario){
  document.getElementById('edit_id').value    = id;
  document.getElementById('edit_nombre').value = nombre;
  document.getElementById('edit_ap').value    = ap;
  document.getElementById('edit_am').value    = am;
  document.getElementById('edit_correo').value = correo;
  document.getElementById('edit_usuario').value= usuario;
  document.getElementById('editModal').style.display='flex';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
