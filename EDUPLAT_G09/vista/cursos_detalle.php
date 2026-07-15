<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — <?= htmlspecialchars($curso['nombre']) ?></title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<div class="app-layout">

  <?php $activePage = 'cursos'; require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title">Detalle del Curso</span>
      <div class="topbar-actions">
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
        <a href="index.php?accion=perfil"><div class="avatar" style="width:30px;height:30px;font-size:0.68rem;cursor:pointer;"><?= htmlspecialchars(inicialesUsuario()) ?></div></a>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="breadcrumb fade-in">
        <a href="index.php?accion=cursos">Mis Cursos</a>
        <span>›</span>
        <span class="current"><?= htmlspecialchars($curso['nombre']) ?></span>
      </div>

      <!-- Course Header -->
      <div class="card fade-in" style="margin-bottom:24px; padding:0; overflow:hidden;">
        <div style="height:140px; background:linear-gradient(135deg,#1a2a6c,#4f7cff); display:flex; align-items:center; padding:32px; gap:20px; position:relative;">
          <div style="font-size:3.5rem; z-index:1;"><?= htmlspecialchars($curso['icono']) ?></div>
          <div style="z-index:1;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
              <h1 style="font-size:1.6rem; color:#fff;"><?= htmlspecialchars($curso['nombre']) ?></h1>
              <span class="badge <?= $curso['estado'] === 'activo' ? 'badge-green' : 'badge-muted' ?>"><?= ucfirst($curso['estado']) ?></span>
            </div>
            <p style="color:rgba(255,255,255,0.7); font-size:0.88rem;"><?= htmlspecialchars($curso['semestre'] ?? '') ?> · Ing. <?= htmlspecialchars($curso['docente_nombres'] . ' ' . $curso['docente_apellidos']) ?></p>
          </div>
        </div>
        <div style="padding:20px 32px; display:flex; gap:32px; flex-wrap:wrap;">
          <?php if ($rol === 'estudiante'): ?>
            <div>
              <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;">Mi progreso</div>
              <div class="progress-bar" style="width:200px;margin-top:8px;"><div class="progress-fill" style="width:<?= (int) $progreso ?>%"></div></div>
              <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;"><?= (int) $progreso ?>% completado</div>
            </div>
            <div style="border-left:1px solid var(--border);padding-left:32px;">
              <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;">Mi promedio</div>
              <div style="font-size:1.6rem;font-family:var(--font-head);font-weight:800;color:var(--success);line-height:1;margin-top:4px;"><?= $promedioCurso !== null ? $promedioCurso : '—' ?></div>
            </div>
          <?php endif; ?>
          <div style="border-left:<?= $rol === 'estudiante' ? '1px solid var(--border);padding-left:32px;' : 'none;' ?>">
            <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;">Tareas</div>
            <div style="font-size:1.6rem;font-family:var(--font-head);font-weight:800;color:var(--text);line-height:1;margin-top:4px;"><?= $rol === 'estudiante' ? "$statTareasEntregadas/$statTareasTotal" : $statTareasTotal ?></div>
          </div>
          <div style="border-left:1px solid var(--border);padding-left:32px;">
            <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:600;">Evaluaciones</div>
            <div style="font-size:1.6rem;font-family:var(--font-head);font-weight:800;color:var(--text);line-height:1;margin-top:4px;"><?= $rol === 'estudiante' ? "$statEvaluacionesRendidas/$statEvaluacionesTotal" : $statEvaluacionesTotal ?></div>
          </div>
        </div>
      </div>

      <div class="tab-nav fade-in fade-in-1" id="detalle-tabs">
        <button class="tab-btn active" onclick="showTab('contenidos')">📄 Contenidos</button>
        <button class="tab-btn" onclick="showTab('tareas')">📝 Tareas</button>
        <button class="tab-btn" onclick="showTab('notas')">📊 <?= $esPropietario ? 'Notas del Curso' : 'Mis Notas' ?></button>
      </div>

      <!-- Tab: Contenidos -->
      <div id="tab-contenidos" class="fade-in fade-in-2">
        <div class="section-header">
          <h3>Material del Curso</h3>
          <?php if ($esPropietario): ?>
            <button class="btn btn-primary btn-sm" onclick="abrirModal('modal-material')">+ Publicar Material</button>
          <?php endif; ?>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
          <?php if (empty($materiales)): ?>
            <p style="font-size:0.85rem;color:var(--text-muted);">Aún no hay material publicado en este curso.</p>
          <?php endif; ?>
          <?php $iconos = ['documento' => '📝', 'video' => '🎥', 'pdf' => '📄', 'enlace' => '🔗']; ?>
          <?php foreach ($materiales as $m): ?>
            <div class="card card-sm" style="display:flex;align-items:center;gap:16px;padding:16px;">
              <div style="width:40px;height:40px;border-radius:8px;background:rgba(79,124,255,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;"><?= $iconos[$m['tipo']] ?? '📄' ?></div>
              <div style="flex:1;">
                <div style="font-weight:600;font-size:0.9rem;color:var(--text);"><?= htmlspecialchars($m['titulo']) ?></div>
                <div style="font-size:0.78rem;color:var(--text-muted);">Publicado el <?= date('d M Y', strtotime($m['fecha_publicacion'])) ?> · <?= ucfirst($m['tipo']) ?></div>
              </div>
              <div style="display:flex;gap:6px;">
                <?php if ($m['enlace']): ?><a href="<?= htmlspecialchars($m['enlace']) ?>" target="_blank"><button class="btn btn-primary btn-sm">↗ Abrir</button></a><?php endif; ?>
                <?php if ($esPropietario): ?>
                  <form method="POST" action="index.php?accion=eliminarMaterial" onsubmit="return confirm('¿Eliminar este material?');">
                    <input type="hidden" name="id_material" value="<?= (int) $m['id_material'] ?>" />
                    <input type="hidden" name="id_curso" value="<?= $idCurso ?>" />
                    <button class="btn btn-ghost btn-sm" type="submit">🗑</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Tab: Tareas (solo lectura; la entrega/calificación se gestiona en la pestaña Tareas) -->
      <div id="tab-tareas" style="display:none;" class="fade-in">
        <div class="section-header">
          <h3>Tareas del Curso</h3>
          <a href="index.php?accion=tareas&curso=<?= $idCurso ?>"><button class="btn btn-primary btn-sm"><?= $esPropietario ? '+ Gestionar en Tareas' : '📤 Ir a Tareas' ?></button></a>
        </div>
        <div class="card" style="padding:0;">
          <div class="table-wrapper">
            <table>
              <thead>
                <tr>
                  <th>Tarea</th>
                  <th>Fecha Límite</th>
                  <?php if ($rol === 'estudiante'): ?><th>Mi Nota</th><?php endif; ?>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($tareas)): ?>
                  <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px;">No hay tareas registradas.</td></tr>
                <?php endif; ?>
                <?php foreach ($tareas as $t):
                  $vencida = strtotime($t['fecha_limite']) < time();
                  $tieneEntrega = !empty($t['id_entrega']);
                  $calificada = ($t['estado_entrega'] ?? '') === 'calificada';
                ?>
                  <tr>
                    <td class="td-main"><?= htmlspecialchars($t['titulo']) ?></td>
                    <td><?= date('d M Y', strtotime($t['fecha_limite'])) ?></td>
                    <?php if ($rol === 'estudiante'): ?>
                      <td><?= $t['nota'] !== null ? '<strong style="color:var(--success);">' . htmlspecialchars($t['nota']) . '</strong>' : '<span style="color:var(--text-muted);">—</span>' ?></td>
                    <?php endif; ?>
                    <td>
                      <?php if ($rol === 'estudiante'): ?>
                        <?php if ($calificada): ?><span class="badge badge-green">Calificada</span>
                        <?php elseif ($tieneEntrega): ?><span class="badge badge-blue">Entregada</span>
                        <?php elseif ($vencida): ?><span class="badge badge-muted">Vencida</span>
                        <?php else: ?><span class="badge badge-gold">Pendiente</span><?php endif; ?>
                      <?php else: ?>
                        <span class="badge badge-blue"><?= $vencida ? 'Cerrada' : 'Abierta' ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Tab: Notas -->
      <div id="tab-notas" style="display:none;" class="fade-in">
        <div class="section-header">
          <h3><?= $esPropietario ? 'Notas del Curso' : 'Mis Notas' ?></h3>
        </div>
        <?php if ($rol === 'estudiante'): ?>
        <div class="card" style="padding:0;">
          <div class="table-wrapper">
            <table>
              <thead>
                <tr><th>Evaluación / Tarea</th><th>Tipo</th><th>Ponderación</th><th>Mi Nota</th><th>Estado</th></tr>
              </thead>
              <tbody>
                <?php foreach ($evaluaciones as $e): ?>
                  <tr>
                    <td class="td-main"><?= htmlspecialchars($e['titulo']) ?></td>
                    <td><span class="badge badge-blue"><?= ucfirst($e['tipo']) ?></span></td>
                    <td><?= htmlspecialchars($e['ponderacion']) ?>%</td>
                    <td><?= $e['nota'] !== null ? '<strong style="color:' . ($e['nota'] >= $e['nota_minima'] ? 'var(--success)' : 'var(--warning)') . ';">' . htmlspecialchars($e['nota']) . '</strong>' : '<span style="color:var(--text-muted);">Pendiente</span>' ?></td>
                    <td><?= $e['nota'] !== null ? '<span class="badge ' . ($e['nota'] >= $e['nota_minima'] ? 'badge-green">Aprobado' : 'badge-gold">Reprobado') . '</span>' : '<span class="badge badge-gold">Próximo</span>' ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php foreach ($tareas as $t): ?>
                  <tr>
                    <td class="td-main"><?= htmlspecialchars($t['titulo']) ?></td>
                    <td><span class="badge badge-muted">Tarea</span></td>
                    <td><?= htmlspecialchars($t['ponderacion']) ?>%</td>
                    <td><?= $t['nota'] !== null ? '<strong style="color:var(--success);">' . htmlspecialchars($t['nota']) . '</strong>' : '<span style="color:var(--text-muted);">' . (!empty($t['id_entrega']) ? 'Entregada' : 'Pendiente') . '</span>' ?></td>
                    <td><?= ($t['estado_entrega'] ?? '') === 'calificada' ? '<span class="badge badge-green">Calificada</span>' : '<span class="badge badge-gold">' . (!empty($t['id_entrega']) ? 'En revisión' : 'Pendiente') . '</span>' ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div style="margin-top:12px;padding:14px 18px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:space-between;">
          <span style="font-size:0.82rem;color:var(--text-muted);">Promedio actual del curso</span>
          <span style="font-size:1.3rem;font-weight:800;font-family:var(--font-head);color:var(--success);"><?= $promedioCurso !== null ? $promedioCurso : '—' ?></span>
        </div>
        <?php else: ?>
          <p style="font-size:0.85rem;color:var(--text-muted);">Gestiona las calificaciones de tareas y evaluaciones desde <a href="index.php?accion=tareas&curso=<?= $idCurso ?>">Tareas</a> y <a href="index.php?accion=evaluaciones&curso=<?= $idCurso ?>">Evaluaciones</a>.</p>
        <?php endif; ?>
      </div>

    </div>
  </main>
</div>

<?php if ($esPropietario): ?>
<!-- MODAL: Publicar Material -->
<div class="modal-overlay" id="modal-material">
  <div class="modal">
    <div class="modal-header">
      <div><h3>Publicar Material</h3></div>
      <button class="modal-close" onclick="cerrarModal('modal-material')">✕</button>
    </div>
    <form method="POST" action="index.php?accion=crearMaterial">
      <input type="hidden" name="id_curso" value="<?= $idCurso ?>" />
      <div class="form-group"><label class="form-label">Título</label><input class="form-control" type="text" name="titulo" required /></div>
      <div class="form-group">
        <label class="form-label">Tipo</label>
        <select class="form-control" name="tipo">
          <option value="documento">Documento</option>
          <option value="pdf">PDF</option>
          <option value="video">Video</option>
          <option value="enlace">Enlace</option>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Enlace (URL)</label><input class="form-control" type="url" name="enlace" placeholder="https://..." /></div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-material')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Publicar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script src="public/js/modales.js"></script>
<script src="public/js/tabs.js"></script>
<?php if (isset($_GET['tab'])): ?>
<script>showTab('<?= htmlspecialchars($_GET['tab']) ?>');</script>
<?php endif; ?>
</body>
</html>
