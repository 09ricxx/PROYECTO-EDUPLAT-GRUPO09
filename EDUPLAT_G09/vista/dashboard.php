<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Dashboard</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<div class="app-layout">

  <?php $activePage = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title">Dashboard</span>
      <div class="topbar-actions">
        <form class="search-bar" method="GET" action="index.php">
          <input type="hidden" name="accion" value="cursos" />
          <span class="search-icon">⌕</span>
          <input type="text" name="q" placeholder="Buscar cursos, tareas..." />
        </form>
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
        <a href="index.php?accion=perfil"><div class="avatar" style="width:30px;height:30px;font-size:0.68rem;cursor:pointer;"><?= htmlspecialchars(inicialesUsuario()) ?></div></a>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="fade-in" style="margin-bottom:24px;">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['nombres']) ?></h1>
        <p style="margin-top:3px;font-size:0.8rem;">Resumen de tu actividad académica.</p>
      </div>

      <div class="stats-grid fade-in fade-in-1">
        <?php if ($rol === 'estudiante'): ?>
          <div class="stat-card"><div class="stat-icon blue">≡</div><div class="stat-value"><?= $cursosActivos ?></div><div class="stat-label">Cursos activos</div></div>
          <div class="stat-card"><div class="stat-icon gold">✎</div><div class="stat-value"><?= $tareasPendientes ?></div><div class="stat-label">Tareas pendientes</div></div>
          <div class="stat-card"><div class="stat-icon green">✓</div><div class="stat-value"><?= $tareasEntregadas ?></div><div class="stat-label">Tareas entregadas</div></div>
          <div class="stat-card"><div class="stat-icon blue">◫</div><div class="stat-value"><?= $promedio !== null ? $promedio : '—' ?></div><div class="stat-label">Promedio general</div></div>
        <?php elseif ($rol === 'docente'): ?>
          <div class="stat-card"><div class="stat-icon blue">≡</div><div class="stat-value"><?= $cursosActivos ?></div><div class="stat-label">Cursos activos</div></div>
          <div class="stat-card"><div class="stat-icon gold">✎</div><div class="stat-value"><?= $tareasPendientes ?></div><div class="stat-label">Tareas creadas</div></div>
          <div class="stat-card"><div class="stat-icon green">✓</div><div class="stat-value"><?= $tareasEntregadas ?></div><div class="stat-label">Por calificar</div></div>
          <div class="stat-card"><div class="stat-icon blue">◫</div><div class="stat-value"><?= $promedio ?></div><div class="stat-label">Alumnos totales</div></div>
        <?php else: ?>
          <div class="stat-card"><div class="stat-icon blue">≡</div><div class="stat-value"><?= $cursosActivos ?></div><div class="stat-label">Cursos totales</div></div>
          <div class="stat-card"><div class="stat-icon gold">✎</div><div class="stat-value"><?= $tareasPendientes ?></div><div class="stat-label">Estudiantes</div></div>
          <div class="stat-card"><div class="stat-icon green">✓</div><div class="stat-value"><?= $tareasEntregadas ?></div><div class="stat-label">Docentes</div></div>
          <div class="stat-card"><div class="stat-icon blue">◫</div><div class="stat-value"><?= $promedio ?></div><div class="stat-label">Inscripciones</div></div>
        <?php endif; ?>
      </div>

      <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px;">

        <div class="card fade-in fade-in-2">
          <div class="card-header">
            <h3 class="card-title"><?= $rol === 'docente' ? 'Mis Cursos (como docente)' : 'Mis Cursos' ?></h3>
            <a href="index.php?accion=cursos"><button class="btn btn-ghost btn-sm">Ver todos</button></a>
          </div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <?php if (empty($misCursos)): ?>
              <p style="font-size:0.82rem;color:var(--text-muted);">Aún no tienes cursos <?= $rol === 'estudiante' ? 'inscritos' : 'creados' ?>.</p>
            <?php endif; ?>
            <?php foreach ($misCursos as $c): ?>
              <a href="index.php?accion=curso&id=<?= (int) $c['id_curso'] ?>" style="display:block;">
                <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--surface2);border-radius:var(--radius-sm);border:1px solid var(--border);">
                  <div style="width:38px;height:38px;border-radius:var(--radius-sm);background:var(--border);display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;"><?= htmlspecialchars($c['icono']) ?></div>
                  <div style="flex:1;">
                    <div style="font-weight:500;font-size:0.85rem;color:var(--text);"><?= htmlspecialchars($c['nombre']) ?></div>
                    <div style="font-size:0.72rem;color:var(--text-muted);margin-top:1px;">Ing. <?= htmlspecialchars($c['docente_nombres'] . ' ' . $c['docente_apellidos']) ?> · <?= (int) $c['total_alumnos'] ?> alumnos</div>
                    <?php if ($rol === 'estudiante'): ?>
                      <div class="progress-bar" style="margin-top:6px;"><div class="progress-fill" style="width:<?= (int) ($c['progreso'] ?? 0) ?>%"></div></div>
                      <div style="font-size:0.68rem;color:var(--text-muted);margin-top:3px;font-family:var(--font-mono);"><?= (int) ($c['progreso'] ?? 0) ?>%</div>
                    <?php endif; ?>
                  </div>
                  <span class="badge <?= $c['estado'] === 'activo' ? 'badge-green' : 'badge-muted' ?>"><?= ucfirst($c['estado']) ?></span>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="card fade-in fade-in-3">
          <div class="card-header">
            <h3 class="card-title"><?= $rol === 'estudiante' ? 'Próximas entregas' : 'Accesos rápidos' ?></h3>
            <?php if ($rol === 'estudiante'): ?><a href="index.php?accion=tareas"><button class="btn btn-ghost btn-sm">Ver</button></a><?php endif; ?>
          </div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            <?php if ($rol === 'estudiante'): ?>
              <?php if (empty($proximasEntregas)): ?>
                <p style="font-size:0.82rem;color:var(--text-muted);">No tienes entregas próximas. 🎉</p>
              <?php endif; ?>
              <?php foreach ($proximasEntregas as $p):
                $dias = (int) ceil((strtotime($p['fecha_limite']) - time()) / 86400);
                $color = $dias <= 1 ? 'var(--danger)' : ($dias <= 3 ? 'var(--warning)' : 'var(--accent)');
                $texto = $dias <= 0 ? 'vence hoy' : ($dias === 1 ? 'vence mañana' : "$dias días restantes");
              ?>
                <div style="padding:10px 12px;background:var(--surface2);border-radius:var(--radius-sm);border-left:2px solid <?= $color ?>;">
                  <div style="font-size:0.82rem;font-weight:500;color:var(--text);"><?= htmlspecialchars($p['titulo']) ?></div>
                  <div style="font-size:0.7rem;color:var(--text-muted);margin-top:1px;"><?= htmlspecialchars($p['curso_nombre']) ?></div>
                  <div style="font-size:0.67rem;color:<?= $color ?>;margin-top:5px;font-family:var(--font-mono);"><?= $texto ?></div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <a href="index.php?accion=cursos"><button class="btn btn-ghost" style="width:100%;justify-content:center;margin-bottom:6px;">+ Nuevo Curso</button></a>
              <a href="index.php?accion=tareas"><button class="btn btn-ghost" style="width:100%;justify-content:center;margin-bottom:6px;">+ Nueva Tarea</button></a>
              <a href="index.php?accion=evaluaciones"><button class="btn btn-ghost" style="width:100%;justify-content:center;">+ Nueva Evaluación</button></a>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>
</body>
</html>
