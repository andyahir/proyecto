<?php
// includes/header.php — incluir al inicio de cada página del dashboard
requireAdmin();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StreamVault — <?= $pageTitle ?? 'Panel' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --navy:      #0a1628;
  --navy-mid:  #0f2044;
  --navy-card: #152a52;
  --navy-light:#1a3362;
  --blue:      #1e4dd8;
  --blue-bright:#2e6cf0;
  --accent:    #38bdf8;
  --green:     #34d399;
  --red:       #f87171;
  --yellow:    #fbbf24;
  --text:      #e8eef8;
  --text-muted:#7a93bc;
  --border:    rgba(46,108,240,0.2);
  --border-hover: rgba(46,108,240,0.5);
  --radius:    12px;
}
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Sora', sans-serif;
  background: var(--navy);
  color: var(--text);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ===== TOPBAR ===== */
.topbar {
  background: var(--navy-mid);
  border-bottom: 1px solid var(--border);
  padding: 0 28px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky; top: 0; z-index: 100;
  backdrop-filter: blur(12px);
}
.topbar-brand {
  display: flex; align-items: center; gap: 12px;
  font-weight: 700; font-size: 1.1rem; color: var(--text);
  text-decoration: none;
}
.topbar-brand .icon {
  width: 34px; height: 34px;
  background: linear-gradient(135deg, var(--blue), var(--accent));
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
}
.topbar-brand .icon svg { width: 18px; height: 18px; fill: #fff; }

.topbar-user {
  display: flex; align-items: center; gap: 12px;
  font-size: 0.85rem; color: var(--text-muted);
}
.topbar-user strong { color: var(--text); }
.btn-logout {
  padding: 6px 14px;
  background: rgba(248,113,113,0.12);
  border: 1px solid rgba(248,113,113,0.3);
  color: var(--red);
  border-radius: 8px;
  font-family: 'Sora', sans-serif;
  font-size: 0.8rem; font-weight: 600;
  cursor: pointer; text-decoration: none;
  transition: background 0.2s;
}
.btn-logout:hover { background: rgba(248,113,113,0.22); }

/* ===== NAV ===== */
.subnav {
  background: var(--navy-card);
  border-bottom: 1px solid var(--border);
  padding: 0 28px;
  display: flex; align-items: center; gap: 4px;
  overflow-x: auto;
}
.subnav a {
  display: flex; align-items: center; gap: 8px;
  padding: 14px 18px;
  color: var(--text-muted);
  text-decoration: none;
  font-size: 0.85rem; font-weight: 500;
  border-bottom: 2px solid transparent;
  white-space: nowrap;
  transition: color 0.2s, border-color 0.2s;
}
.subnav a:hover { color: var(--text); }
.subnav a.active {
  color: var(--accent);
  border-bottom-color: var(--accent);
}
.subnav a svg { width: 16px; height: 16px; }

/* ===== MAIN CONTENT ===== */
.main {
  flex: 1;
  padding: 32px 28px;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}

/* ===== SECTION HEADER ===== */
.section-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 28px;
}
.section-header h2 {
  font-size: 1.4rem; font-weight: 700;
  background: linear-gradient(135deg, var(--text), var(--accent));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}

/* ===== CARD ===== */
.card {
  background: var(--navy-card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 28px;
  margin-bottom: 24px;
}

/* ===== FORM GRID ===== */
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label {
  font-size: 0.75rem; font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase; letter-spacing: 0.07em;
}
.form-group input,
.form-group select,
.form-group textarea {
  padding: 11px 14px;
  background: var(--navy-mid);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text);
  font-family: 'Sora', sans-serif;
  font-size: 0.9rem;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: var(--blue-bright);
  box-shadow: 0 0 0 3px rgba(46,108,240,0.18);
}
.form-group input::placeholder,
.form-group textarea::placeholder { color: var(--text-muted); }
.form-group select option { background: var(--navy-mid); }
.form-group textarea { resize: vertical; min-height: 100px; }
.form-group input[readonly] {
  background: rgba(56,189,248,0.07);
  border-color: rgba(56,189,248,0.2);
  color: var(--accent);
  font-family: 'DM Mono', monospace;
  cursor: not-allowed;
}

/* ===== BUTTONS ===== */
.btn { display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 20px; border-radius: 8px; font-family:'Sora',sans-serif;
  font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none;
  text-decoration: none; transition: all 0.15s; }
.btn-primary { background: linear-gradient(135deg,var(--blue),var(--blue-bright)); color:#fff;
  box-shadow: 0 4px 14px rgba(30,77,216,0.35); }
.btn-primary:hover { box-shadow: 0 6px 20px rgba(30,77,216,0.5); transform: translateY(-1px); }
.btn-success { background: rgba(52,211,153,0.15); border:1px solid rgba(52,211,153,0.3); color:var(--green); }
.btn-success:hover { background: rgba(52,211,153,0.25); }
.btn-danger  { background: rgba(248,113,113,0.12); border:1px solid rgba(248,113,113,0.3); color:var(--red); }
.btn-danger:hover  { background: rgba(248,113,113,0.22); }
.btn-warning { background: rgba(251,191,36,0.12); border:1px solid rgba(251,191,36,0.3); color:var(--yellow); }
.btn-warning:hover { background: rgba(251,191,36,0.22); }
.btn-sm { padding: 6px 12px; font-size: 0.78rem; }

/* ===== TABLE ===== */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead th {
  background: var(--navy-mid);
  padding: 12px 16px;
  font-size: 0.72rem; font-weight: 600;
  text-transform: uppercase; letter-spacing: 0.08em;
  color: var(--text-muted);
  text-align: left;
  border-bottom: 1px solid var(--border);
}
tbody td {
  padding: 14px 16px;
  border-bottom: 1px solid rgba(46,108,240,0.08);
  font-size: 0.88rem; color: var(--text);
  vertical-align: middle;
}
tbody tr:hover { background: rgba(46,108,240,0.05); }
tbody tr:last-child td { border-bottom: none; }
.td-actions { display: flex; gap: 6px; flex-wrap: wrap; }

/* ===== BADGE ===== */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
.badge-active   { background: rgba(52,211,153,0.15); color: var(--green);  border: 1px solid rgba(52,211,153,0.3); }
.badge-inactive { background: rgba(248,113,113,0.12); color: var(--red);    border: 1px solid rgba(248,113,113,0.3); }

/* ===== ALERTS ===== */
.alert { padding: 12px 18px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px;
  display: flex; align-items: center; gap: 10px; }
.alert-success { background: rgba(52,211,153,0.1); border:1px solid rgba(52,211,153,0.3); color:var(--green); }
.alert-error   { background: rgba(248,113,113,0.1); border:1px solid rgba(248,113,113,0.3); color:var(--red); }

/* ===== MOVIE THUMB ===== */
.movie-thumb { width: 54px; height: 76px; object-fit: cover; border-radius: 6px;
  border: 1px solid var(--border); background: var(--navy-mid); }

/* scroll */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: var(--navy-mid); }
::-webkit-scrollbar-thumb { background: var(--navy-light); border-radius: 3px; }
</style>
</head>
<body>

<header class="topbar">
  <a href="dashboard.php" class="topbar-brand">
    <div class="icon">
      <svg viewBox="0 0 24 24"><path d="M4 3l16 9-16 9V3z"/></svg>
    </div>
    StreamVault
  </a>
  <div class="topbar-user">
    <span>Bienvenido, <strong><?= htmlspecialchars($_SESSION['admin_nombre']) ?></strong></span>
    <a href="logout.php" class="btn-logout">Cerrar sesión</a>
  </div>
</header>

<nav class="subnav">
  <a href="peliculas.php" class="<?= $currentPage==='peliculas.php'?'active':'' ?>">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <rect x="2" y="7" width="20" height="15" rx="2"/><path d="M16 2l-4 5-4-5"/>
    </svg>
    Registro de Películas
  </a>
  <a href="catalogo.php" class="<?= $currentPage==='catalogo.php'?'active':'' ?>">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
    </svg>
    Consultar Películas
  </a>
  <a href="clientes.php" class="<?= $currentPage==='clientes.php'?'active':'' ?>">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    Consultar Clientes
  </a>
  <a href="usuarios.php" class="<?= $currentPage==='usuarios.php'?'active':'' ?>">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/>
      <line x1="19" y1="8" x2="23" y2="8"/><line x1="21" y1="6" x2="21" y2="10"/>
    </svg>
    Registro de Usuarios
  </a>
</nav>
