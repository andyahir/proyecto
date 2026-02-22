<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Películas';
require_once __DIR__ . '/includes/header.php';

$db  = getDB();
$msg = '';
$err = '';

// ——— ACCIONES POST ———
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'registrar' || $action === 'modificar') {
        $id          = intval($_POST['id'] ?? 0);
        $nombre      = trim($_POST['nombre'] ?? '');
        $genero_id   = intval($_POST['genero_id'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $trailer_url = trim($_POST['trailer_url'] ?? '');

        // Subir imagen
        $imagenPath = $_POST['imagen_actual'] ?? null;
        if (!empty($_FILES['imagen']['name'])) {
            $ext  = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];
            if (in_array($ext, $allowed)) {
                $filename = uniqid('peli_') . '.' . $ext;
                $dest = __DIR__ . '/uploads/' . $filename;
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
                    $imagenPath = UPLOAD_URL . $filename;
                }
            }
        }

        if ($nombre && $genero_id) {
            if ($action === 'registrar') {
                $stmt = $db->prepare("INSERT INTO peliculas (nombre,genero_id,imagen,descripcion,trailer_url) VALUES (?,?,?,?,?)");
                $stmt->execute([$nombre,$genero_id,$imagenPath,$descripcion,$trailer_url]);
                $msg = 'Película registrada correctamente.';
            } else {
                $stmt = $db->prepare("UPDATE peliculas SET nombre=?,genero_id=?,imagen=?,descripcion=?,trailer_url=? WHERE id=?");
                $stmt->execute([$nombre,$genero_id,$imagenPath,$descripcion,$trailer_url,$id]);
                $msg = 'Película actualizada.';
            }
        } else { $err = 'Nombre y género son obligatorios.'; }
    }

    if ($action === 'activar' || $action === 'inactivar') {
        $id = intval($_POST['id']);
        $est = $action === 'activar' ? 'activa' : 'inactiva';
        $db->prepare("UPDATE peliculas SET estatus=? WHERE id=?")->execute([$est,$id]);
        $msg = 'Estatus actualizado.';
    }
}

// ——— CARGAR DATOS PARA EDITAR ———
$editMovie = null;
if (isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM peliculas WHERE id=?");
    $stmt->execute([intval($_GET['editar'])]);
    $editMovie = $stmt->fetch();
}

// ——— CARGAR GENEROS Y PELÍCULAS ———
$generos  = $db->query("SELECT * FROM generos ORDER BY nombre")->fetchAll();
$peliculas = $db->query("SELECT p.*, g.nombre AS genero FROM peliculas p JOIN generos g ON g.id=p.genero_id ORDER BY p.created_at DESC")->fetchAll();
?>

<div class="main">
  <div class="section-header">
    <h2><?= $editMovie ? 'Modificar Película' : 'Registrar Nueva Película' ?></h2>
  </div>

  <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= $err ?></div><?php endif; ?>

  <!-- FORMULARIO REGISTRO/EDICIÓN -->
  <div class="card">
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="<?= $editMovie ? 'modificar' : 'registrar' ?>">
      <?php if ($editMovie): ?>
      <input type="hidden" name="id" value="<?= $editMovie['id'] ?>">
      <input type="hidden" name="imagen_actual" value="<?= $editMovie['imagen'] ?>">
      <?php endif; ?>

      <div class="form-grid">
        <div class="form-group">
          <label>Nombre de la Película *</label>
          <input type="text" name="nombre" placeholder="Ej. Inception" required
            value="<?= htmlspecialchars($editMovie['nombre'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Género *</label>
          <select name="genero_id" required>
            <option value="">— Seleccionar —</option>
            <?php foreach ($generos as $g): ?>
            <option value="<?= $g['id'] ?>" <?= ($editMovie['genero_id']??'') == $g['id'] ? 'selected':'' ?>>
              <?= htmlspecialchars($g['nombre']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Cargar Imagen (poster)</label>
          <input type="file" name="imagen" accept="image/*">
          <?php if (!empty($editMovie['imagen'])): ?>
          <small style="color:var(--text-muted)">Imagen actual guardada. Sube otra para reemplazarla.</small>
          <?php endif; ?>
        </div>
        <div class="form-group">
          <label>URL del Tráiler (YouTube)</label>
          <input type="url" name="trailer_url" placeholder="https://www.youtube.com/watch?v=..."
            value="<?= htmlspecialchars($editMovie['trailer_url'] ?? '') ?>">
        </div>
        <div class="form-group full">
          <label>Descripción</label>
          <textarea name="descripcion" placeholder="Sinopsis de la película..."><?= htmlspecialchars($editMovie['descripcion'] ?? '') ?></textarea>
        </div>
      </div>

      <div style="margin-top:20px;display:flex;gap:10px;align-items:center;">
        <button type="submit" class="btn btn-primary">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          <?= $editMovie ? 'Guardar Cambios' : 'Registrar Película' ?>
        </button>
        <?php if ($editMovie): ?>
        <a href="peliculas.php" class="btn btn-warning">Cancelar</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- TABLA PELÍCULAS -->
  <div class="section-header" style="margin-top:8px;">
    <h2>Catálogo de Películas</h2>
  </div>
  <div class="card" style="padding:0;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Género</th>
            <th>Descripción</th>
            <th>Tráiler</th>
            <th>Estatus</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($peliculas as $p): ?>
        <tr>
          <td>
            <?php if ($p['imagen']): ?>
            <img src="<?= htmlspecialchars($p['imagen']) ?>" class="movie-thumb" alt="poster">
            <?php else: ?>
            <div class="movie-thumb" style="display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:0.6rem;">SIN<br>IMG</div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($p['nombre']) ?></strong></td>
          <td><?= htmlspecialchars($p['genero']) ?></td>
          <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
            <?= htmlspecialchars($p['descripcion'] ?? '') ?>
          </td>
          <td>
            <?php if ($p['trailer_url']): ?>
            <button onclick="openTrailer('<?= htmlspecialchars($p['trailer_url'], ENT_QUOTES) ?>')" class="btn btn-sm btn-primary">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M4 3l16 9-16 9V3z"/></svg>
              Ver
            </button>
            <?php else: ?>
            <span style="color:var(--text-muted);font-size:0.78rem;">—</span>
            <?php endif; ?>
          </td>
          <td><span class="badge badge-<?= $p['estatus']==='activa'?'active':'inactive' ?>"><?= $p['estatus'] ?></span></td>
          <td>
            <div class="td-actions">
              <form method="POST" style="display:contents;">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <?php if ($p['estatus']==='inactiva'): ?>
                <button name="action" value="activar" class="btn btn-sm btn-success">Activar</button>
                <?php else: ?>
                <button name="action" value="inactivar" class="btn btn-sm btn-danger">Inactivar</button>
                <?php endif; ?>
              </form>
              <a href="peliculas.php?editar=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Modificar</a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$peliculas): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">No hay películas registradas.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ===== MODAL TRÁILER ===== -->
<div id="trailerModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.85);backdrop-filter:blur(6px);">
  <div style="position:relative;width:min(860px,95vw);">
    <!-- Botón cerrar -->
    <button onclick="closeTrailer()" style="position:absolute;top:-42px;right:0;background:none;border:none;cursor:pointer;color:#fff;display:flex;align-items:center;gap:6px;font-family:'Sora',sans-serif;font-size:0.88rem;opacity:0.8;transition:opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
      Cerrar
    </button>
    <!-- Contenedor 16:9 -->
    <div style="position:relative;padding-bottom:56.25%;height:0;border-radius:14px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,0.7);border:1px solid rgba(46,108,240,0.3);">
      <iframe id="trailerFrame" src="" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen
        style="position:absolute;inset:0;width:100%;height:100%;border:none;"></iframe>
    </div>
  </div>
</div>

<script>
function getYouTubeId(url) {
  // Soporta: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
  var match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  return match ? match[1] : null;
}

function openTrailer(url) {
  var id = getYouTubeId(url);
  if (!id) { alert('URL de YouTube no válida.'); return; }
  document.getElementById('trailerFrame').src = 'https://www.youtube.com/embed/' + id + '?autoplay=1';
  var modal = document.getElementById('trailerModal');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeTrailer() {
  document.getElementById('trailerFrame').src = '';
  document.getElementById('trailerModal').style.display = 'none';
  document.body.style.overflow = '';
}

// Cerrar al hacer clic fuera del video
document.getElementById('trailerModal').addEventListener('click', function(e) {
  if (e.target === this) closeTrailer();
});

// Cerrar con tecla Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeTrailer();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>