<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Consultar Películas';
require_once __DIR__ . '/includes/header.php';

$db = getDB();
$generoFiltro = intval($_GET['genero'] ?? 0);
$busqueda     = trim($_GET['q'] ?? '');

$where = '1=1';
$params = [];
if ($generoFiltro) { $where .= ' AND p.genero_id=?'; $params[] = $generoFiltro; }
if ($busqueda)     { $where .= ' AND p.nombre LIKE ?'; $params[] = '%'.$busqueda.'%'; }

$stmt = $db->prepare("SELECT p.*,g.nombre AS genero FROM peliculas p JOIN generos g ON g.id=p.genero_id WHERE $where ORDER BY p.nombre");
$stmt->execute($params);
$peliculas = $stmt->fetchAll();
$generos   = $db->query("SELECT * FROM generos ORDER BY nombre")->fetchAll();
?>

<div class="main">
  <div class="section-header">
    <h2>Consultar Películas</h2>
  </div>

  <!-- FILTROS -->
  <div class="card">
    <form method="GET" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
      <div class="form-group" style="flex:1;min-width:200px;">
        <label>Buscar por nombre</label>
        <input type="text" name="q" placeholder="Nombre de la película..." value="<?= htmlspecialchars($busqueda) ?>">
      </div>
      <div class="form-group" style="width:200px;">
        <label>Filtrar por género</label>
        <select name="genero">
          <option value="">Todos</option>
          <?php foreach ($generos as $g): ?>
          <option value="<?= $g['id'] ?>" <?= $generoFiltro==$g['id']?'selected':'' ?>><?= htmlspecialchars($g['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="margin-bottom:1px;">Filtrar</button>
      <a href="catalogo.php" class="btn btn-warning" style="margin-bottom:1px;">Limpiar</a>
    </form>
  </div>

  <!-- GRID DE PELÍCULAS -->
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px;margin-top:8px;">
  <?php foreach ($peliculas as $p): ?>
  <div style="background:var(--navy-card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;"
       onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 40px rgba(0,0,0,0.4)'"
       onmouseout="this.style.transform='';this.style.boxShadow=''">
    <?php if ($p['imagen']): ?>
    <img src="<?= htmlspecialchars($p['imagen']) ?>" style="width:100%;height:160px;object-fit:cover;" alt="poster">
    <?php else: ?>
    <div style="width:100%;height:160px;background:var(--navy-mid);display:flex;align-items:center;justify-content:center;color:var(--text-muted);">SIN IMAGEN</div>
    <?php endif; ?>
    <div style="padding:16px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
        <strong style="font-size:0.95rem;"><?= htmlspecialchars($p['nombre']) ?></strong>
        <span class="badge badge-<?= $p['estatus']==='activa'?'active':'inactive' ?>"><?= $p['estatus'] ?></span>
      </div>
      <div style="font-size:0.78rem;color:var(--accent);margin-bottom:8px;"><?= htmlspecialchars($p['genero']) ?></div>
      <p style="font-size:0.82rem;color:var(--text-muted);line-height:1.5;margin-bottom:14px;
         display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
        <?= htmlspecialchars($p['descripcion'] ?? 'Sin descripción.') ?>
      </p>
      <?php if ($p['trailer_url']): ?>
      <button onclick="openTrailer('<?= htmlspecialchars($p['trailer_url'], ENT_QUOTES) ?>')" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M4 3l16 9-16 9V3z"/></svg>
        Ver Tráiler
      </button>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (!$peliculas): ?>
  <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted);">No se encontraron películas.</div>
  <?php endif; ?>
  </div>
</div>

<!-- ===== MODAL TRÁILER ===== -->
<div id="trailerModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.85);backdrop-filter:blur(6px);">
  <div style="position:relative;width:min(860px,95vw);">
    <button onclick="closeTrailer()" style="position:absolute;top:-42px;right:0;background:none;border:none;cursor:pointer;color:#fff;display:flex;align-items:center;gap:6px;font-family:'Sora',sans-serif;font-size:0.88rem;opacity:0.8;transition:opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
      Cerrar
    </button>
    <div style="position:relative;padding-bottom:56.25%;height:0;border-radius:14px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,0.7);border:1px solid rgba(46,108,240,0.3);">
      <iframe id="trailerFrame" src="" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen
        style="position:absolute;inset:0;width:100%;height:100%;border:none;"></iframe>
    </div>
  </div>
</div>

<script>
function getYouTubeId(url) {
  var match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  return match ? match[1] : null;
}
function openTrailer(url) {
  var id = getYouTubeId(url);
  if (!id) { alert('URL de YouTube no válida.'); return; }
  document.getElementById('trailerFrame').src = 'https://www.youtube.com/embed/' + id + '?autoplay=1';
  document.getElementById('trailerModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeTrailer() {
  document.getElementById('trailerFrame').src = '';
  document.getElementById('trailerModal').style.display = 'none';
  document.body.style.overflow = '';
}
document.getElementById('trailerModal').addEventListener('click', function(e) {
  if (e.target === this) closeTrailer();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeTrailer();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>