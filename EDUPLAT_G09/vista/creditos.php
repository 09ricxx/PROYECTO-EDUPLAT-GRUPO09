<?php $activePage = 'creditos'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Créditos</title>
  <link rel="stylesheet" href="public/css/styles.css" />
  <style>
    .member-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px;
      text-align: center;
      transition: border-color var(--transition);
    }
    .member-card:hover { border-color: #1e2022; }
    .member-avatar {
      width: 48px; height: 48px;
      border-radius: 50%;
      margin: 0 auto 12px;
      display: flex; align-items: center; justify-content: center;
      font-family: var(--font-mono);
      font-weight: 400;
      font-size: 0.78rem;
      color: var(--text-dim);
      background: var(--surface2);
      border: 1px solid var(--border);
    }
    .member-name { font-size: 0.82rem; font-weight: 500; margin-bottom: 3px; color: var(--text); }
    .member-role { font-size: 0.7rem; color: var(--text-muted); }
  </style>
</head>
<body>
<div class="app-layout">

  <?php require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title">Créditos del Proyecto</span>
      <div class="topbar-actions">
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="card fade-in" style="margin-bottom:20px;padding:36px 32px;text-align:center;">
        <h1 style="font-size:1.6rem;margin-bottom:6px;">EduPlat</h1>
        <p style="font-size:0.82rem;margin-bottom:16px;">Plataforma de Cursos en Línea</p>
        <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;">
          <span class="badge badge-blue" style="padding:4px 10px;">Grupo G09</span>
          <span class="badge badge-muted" style="padding:4px 10px;">Primer Parcial 2026</span>
          <span class="badge badge-green" style="padding:4px 10px;">HTML · CSS · JavaScript</span>
        </div>
      </div>

      <div class="card fade-in fade-in-1" style="margin-bottom:20px;">
        <h3 class="card-title" style="margin-bottom:14px;">Descripción del Proyecto</h3>
        <p style="line-height:1.75;font-size:0.84rem;">
          <strong style="color:var(--text);font-weight:500;">EduPlat</strong> es un sistema web para gestionar cursos virtuales, donde docentes publican contenidos, tareas y evaluaciones, y estudiantes acceden al material y hacen seguimiento académico.
        </p>
        <div style="margin-top:16px;display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;">
            <div style="font-size:0.78rem;font-weight:500;margin-bottom:4px;color:var(--text);">Gestión de Cursos</div>
            <div style="font-size:0.72rem;color:var(--text-muted);">Crear, editar y eliminar cursos con sus contenidos.</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;">
            <div style="font-size:0.78rem;font-weight:500;margin-bottom:4px;color:var(--text);">Gestión de Tareas</div>
            <div style="font-size:0.72rem;color:var(--text-muted);">Publicar tareas con fechas límite y ponderaciones.</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px;">
            <div style="font-size:0.78rem;font-weight:500;margin-bottom:4px;color:var(--text);">Evaluaciones</div>
            <div style="font-size:0.72rem;color:var(--text-muted);">Crear exámenes y visualizar resultados por grupo.</div>
          </div>
        </div>
      </div>

      <div class="fade-in fade-in-2">
        <div class="section-header" style="margin-bottom:14px;">
          <h2>Equipo de Desarrollo — G09</h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
          <div class="member-card">
            <div class="member-avatar">BR</div>
            <div class="member-name">Burgos Holguín Roger Gustavo</div>
            <div class="member-role">Desarrollador Frontend</div>
          </div>
          <div class="member-card">
            <div class="member-avatar">ML</div>
            <div class="member-name">Morejon Salavarria Luis Diego</div>
            <div class="member-role">Diseñador UI / Líder</div>
          </div>
          <div class="member-card">
            <div class="member-avatar">OJ</div>
            <div class="member-name">Ortiz Peña Jimmy Ricardo</div>
            <div class="member-role">Desarrollador Frontend</div>
          </div>
        </div>
      </div>

      <div class="card fade-in" style="margin-top:20px;">
        <h3 class="card-title" style="margin-bottom:14px;">Tecnologías Utilizadas</h3>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 14px;">
            <div style="font-size:0.8rem;font-weight:500;color:var(--text);">HTML</div>
            <div style="font-size:0.68rem;color:var(--text-muted);">Estructura semántica</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 14px;">
            <div style="font-size:0.8rem;font-weight:500;color:var(--text);">CSS</div>
            <div style="font-size:0.68rem;color:var(--text-muted);">Variables · Externos</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 14px;">
            <div style="font-size:0.8rem;font-weight:500;color:var(--text);">JavaScript</div>
            <div style="font-size:0.68rem;color:var(--text-muted);">Interactividad · CRUD</div>
          </div>
          <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:8px 14px;">
            <div style="font-size:0.8rem;font-weight:500;color:var(--text);">Google Fonts</div>
            <div style="font-size:0.68rem;color:var(--text-muted);">DM Sans · DM Mono</div>
          </div>
        </div>
      </div>

      <div class="card fade-in" style="margin-top:16px;margin-bottom:8px;">
        <h3 class="card-title" style="margin-bottom:14px;">Páginas del Sistema</h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:8px;">
          <a href="index.php?accion=login" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">01</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=login</div><div style="font-size:0.68rem;color:var(--text-muted);">Login</div></div>
          </a>
          <a href="index.php?accion=registro" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">02</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=registro</div><div style="font-size:0.68rem;color:var(--text-muted);">Crear cuenta</div></div>
          </a>
          <a href="index.php?accion=dashboard" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">03</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=dashboard</div><div style="font-size:0.68rem;color:var(--text-muted);">Panel principal</div></div>
          </a>
          <a href="index.php?accion=cursos" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">04</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=cursos</div><div style="font-size:0.68rem;color:var(--text-muted);">Gestión CRUD</div></div>
          </a>
          <a href="index.php?accion=tareas" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">05</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=tareas</div><div style="font-size:0.68rem;color:var(--text-muted);">Gestión CRUD</div></div>
          </a>
          <a href="index.php?accion=evaluaciones" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">06</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=evaluaciones</div><div style="font-size:0.68rem;color:var(--text-muted);">Evaluaciones CRUD</div></div>
          </a>
          <a href="index.php?accion=perfil" style="display:flex;align-items:center;gap:8px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px;color:var(--text);transition:border-color 0.12s;" onmouseover="this.style.borderColor='#252830'" onmouseout="this.style.borderColor='var(--border)'">
            <span style="font-size:0.7rem;font-family:var(--font-mono);color:var(--text-muted);">07</span>
            <div><div style="font-size:0.8rem;font-weight:500;">accion=perfil</div><div style="font-size:0.68rem;color:var(--text-muted);">Perfil de usuario</div></div>
          </a>
        </div>
      </div>

    </div>
  </main>
</div>
</body>
</html>
