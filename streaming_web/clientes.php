<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Clientes';
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
        $password = $_POST['password'] ?? '';

        if ($nombre && $ap && $correo && $password) {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO clientes (nombre,apellido_paterno,apellido_materno,correo,password_hash) VALUES (?,?,?,?,?)");
                $stmt->execute([$nombre,$ap,$am,$correo,$hash]);
                // La clave se genera vía trigger
                $id   = $db->lastInsertId();
                $row  = $db->prepare("SELECT clave FROM clientes WHERE id=?");
                $row->execute([$id]);
                $clave = $row->fetchColumn();
                $msg = "Cliente registrado. Clave asignada: <strong>$clave</strong>";
            } catch (\Exception $e) {
                $err = 'Error: correo ya registrado o dato inválido.';
            }
        } else { $err = 'Todos los campos marcados con * son obligatorios.'; }
    }

    if ($action === 'activar' || $action === 'inactivar') {
        $id  = intval($_POST['id']);
        $est = $action === 'activar' ? 'activo' : 'inactivo';
        $db->prepare("UPDATE clientes SET estatus=? WHERE id=?")->execute([$est,$id]);
        $msg = 'Estatus actualizado.';
    }

    if ($action === 'eliminar') {
        $id = intval($_POST['id']);
        $db->prepare("DELETE FROM clientes WHERE id=?")->execute([$id]);
        $msg = 'Cliente eliminado.';
    }

    if ($action === 'actualizar') {
        $id     = intval($_POST['id']);
        $nombre = trim($_POST['nombre'] ?? '');
        $ap     = trim($_POST['apellido_paterno'] ?? '');
        $am     = trim($_POST['apellido_materno'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $db->prepare("UPDATE clientes SET nombre=?,apellido_paterno=?,apellido_materno=?,correo=? WHERE id=?")
           ->execute([$nombre,$ap,$am,$correo,$id]);
        $msg = 'Cliente actualizado.';
    }
}

$clientes = $db->query("SELECT id,clave,nombre,apellido_paterno,apellido_materno,correo,estatus,created_at FROM clientes ORDER BY created_at DESC")->fetchAll();
?>

<div class="main">
  <div class="section-header"><h2>Registro de Clientes</h2></div>

  <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= $err ?></div><?php endif; ?>

  <!-- FORMULARIO -->
  <div class="card">
    <form method="POST">
      <input type="hidden" name="action" value="registrar">
      <div class="form-grid">
        <div class="form-group">
          <label>Nombre *</label>
          <input type="text" name="nombre" placeholder="Nombre" required>
        </div>
        <div class="form-group">
          <label>Clave (generada automáticamente)</label>
          <input type="text" value="— auto —" readonly>
        </div>
        <div class="form-group">
          <label>Apellido Paterno *</label>
          <input type="text" name="apellido_paterno" placeholder="Apellido paterno" required>
        </div>
        <div class="form-group">
          <label>Apellido Materno</label>
          <input type="text" name="apellido_materno" placeholder="Apellido materno">
        </div>
        <div class="form-group">
          <label>Correo electrónico *</label>
          <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
        </div>
        <div class="form-group">
          <label>Contraseña *</label>
          <input type="password" name="password" placeholder="Contraseña para app" required>
        </div>
      </div>
      <div style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
          </svg>
          Registrar Cliente
        </button>
      </div>
    </form>
  </div>

  <!-- TABLA CLIENTES -->
  <div class="card" style="padding:0;margin-top:8px;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Clave</th>
            <th>Nombre Completo</th>
            <th>Correo / Usuario</th>
            <th>Fecha Registro</th>
            <th>Estatus</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $c): ?>
        <tr>
          <td><code style="font-family:'DM Mono',monospace;font-size:0.8rem;color:var(--accent);"><?= htmlspecialchars($c['clave']) ?></code></td>
          <td><?= htmlspecialchars($c['nombre'].' '.$c['apellido_paterno'].' '.$c['apellido_materno']) ?></td>
          <td><?= htmlspecialchars($c['correo']) ?></td>
          <td style="font-size:0.8rem;color:var(--text-muted);"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
          <td><span class="badge badge-<?= $c['estatus']==='activo'?'active':'inactive' ?>"><?= $c['estatus'] ?></span></td>
          <td>
            <div class="td-actions">
              <form method="POST" style="display:contents;">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <?php if ($c['estatus']==='inactivo'): ?>
                <button name="action" value="activar" class="btn btn-sm btn-success">Activar</button>
                <?php else: ?>
                <button name="action" value="inactivar" class="btn btn-sm btn-danger">Inactivar</button>
                <?php endif; ?>
                <button name="action" value="eliminar" class="btn btn-sm btn-danger"
                  onclick="return confirm('¿Eliminar este cliente?')">Eliminar</button>
              </form>
              <button class="btn btn-sm btn-warning"
                onclick="openEditModal(<?= $c['id'] ?>,'<?= htmlspecialchars($c['nombre'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['apellido_paterno'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['apellido_materno'],ENT_QUOTES) ?>','<?= htmlspecialchars($c['correo'],ENT_QUOTES) ?>')">
                Actualizar
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$clientes): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">Sin clientes registrados.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL EDITAR CLIENTE -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:999;align-items:center;justify-content:center;">
  <div style="background:var(--navy-card);border:1px solid var(--border);border-radius:var(--radius);padding:32px;width:480px;max-width:95vw;">
    <h3 style="margin-bottom:20px;font-size:1.1rem;">Actualizar Cliente</h3>
    <form method="POST">
      <input type="hidden" name="action" value="actualizar">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-grid">
        <div class="form-group"><label>Nombre</label><input type="text" name="nombre" id="edit_nombre" required></div>
        <div class="form-group"><label>Apellido Paterno</label><input type="text" name="apellido_paterno" id="edit_ap" required></div>
        <div class="form-group"><label>Apellido Materno</label><input type="text" name="apellido_materno" id="edit_am"></div>
        <div class="form-group"><label>Correo</label><input type="email" name="correo" id="edit_correo" required></div>
      </div>
      <div style="display:flex;gap:10px;margin-top:20px;">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-warning" onclick="document.getElementById('editModal').style.display='none'">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(id,nombre,ap,am,correo){
  document.getElementById('edit_id').value    = id;
  document.getElementById('edit_nombre').value = nombre;
  document.getElementById('edit_ap').value    = ap;
  document.getElementById('edit_am').value    = am;
  document.getElementById('edit_correo').value = correo;
  document.getElementById('editModal').style.display='flex';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
