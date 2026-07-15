<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Evaluaciones</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<div class="app-layout">

  <?php $activePage = 'evaluaciones'; require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title">Evaluaciones</span>
      <div class="topbar-actions">
        <form class="search-bar" method="GET" action="index.php">
          <input type="hidden" name="accion" value="evaluaciones" />
          <span class="search-icon">⌕</span>
          <input type="text" name="q" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar evaluación..." />
        </form>
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
        <a href="index.php?accion=perfil"><div class="avatar" style="width:30px;height:30px;font-size:0.68rem;cursor:pointer;"><?= htmlspecialchars(inicialesUsuario()) ?></div></a>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="section-header fade-in">
        <div>
          <h2><?= $esDocente ? 'Gestión de Evaluaciones' : 'Mis Evaluaciones' ?></h2>
          <p style="margin-top:4px;"><?= $esDocente ? 'Programa exámenes/quizzes y registra calificaciones' : 'Consulta fechas, temas y resultados' ?></p>
        </div>
        <?php if ($esDocente): ?>
          <button class="btn btn-primary" onclick="abrirModal('modal-crear')">+ Nueva Evaluación</button>
        <?php endif; ?>
      </div>

      <div style="display:flex; gap:12px; align-items:center; margin-bottom:20px; flex-wrap:wrap;" class="fade-in fade-in-1">
        <form method="GET" action="index.php" style="display:flex;gap:12px;align-items:center;">
          <input type="hidden" name="accion" value="evaluaciones" />
          <input type="hidden" name="q" value="<?= htmlspecialchars($busqueda) ?>" />
          <select class="form-control" name="curso" style="width:auto;padding:8px 14px;" onchange="this.form.submit()">
            <option value="">Todos los cursos</option>
            <?php foreach ($cursosFiltro as $c): ?>
              <option value="<?= (int) $c['id_curso'] ?>" <?= $idCursoFiltro == $c['id_curso'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <div class="card fade-in fade-in-2" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Evaluación</th><th>Curso</th><th>Tipo</th><th>Fecha</th><th>Ponderación</th>
                <?php if ($rol === 'estudiante'): ?><th>Mi Nota</th><?php else: ?><th>Calificados</th><?php endif; ?>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($evaluaciones)): ?>
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:24px;">No se encontraron evaluaciones<?= $busqueda !== '' ? ' para "' . htmlspecialchars($busqueda) . '"' : '' ?>.</td></tr>
              <?php endif; ?>
              <?php foreach ($evaluaciones as $e):
                $futura = strtotime($e['fecha']) > time();
              ?>
                <tr>
                  <td class="td-main"><?= htmlspecialchars($e['titulo']) ?></td>
                  <td><?= htmlspecialchars($e['curso_nombre']) ?></td>
                  <td><span class="badge badge-blue"><?= ucfirst($e['tipo']) ?></span></td>
                  <td><?= date('d M Y', strtotime($e['fecha'])) ?></td>
                  <td><?= htmlspecialchars($e['ponderacion']) ?>%</td>
                  <?php if ($rol === 'estudiante'): ?>
                    <td><?= $e['nota'] !== null ? '<strong style="color:' . ($e['nota'] >= $e['nota_minima'] ? 'var(--success)' : 'var(--warning)') . ';">' . htmlspecialchars($e['nota']) . '</strong>' : ($futura ? '<span class="badge badge-gold">Próxima</span>' : '<span style="color:var(--text-muted);">Pendiente</span>') ?></td>
                  <?php else: ?>
                    <td><?= (int) ($e['total_calificados'] ?? 0) ?></td>
                  <?php endif; ?>
                  <td><div class="table-actions">
                    <?php if ($esDocente): ?>
                      <a href="index.php?accion=evaluaciones&ver=<?= (int) $e['id_evaluacion'] ?>"><button class="btn btn-ghost btn-sm">📊 Notas</button></a>
                      <button class="btn btn-ghost btn-sm" onclick='abrirEditar(<?= json_encode($e, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✎</button>
                      <form method="POST" action="index.php?accion=eliminarEvaluacion" onsubmit="return confirm('¿Eliminar esta evaluación?');" style="display:inline;">
                        <input type="hidden" name="id_evaluacion" value="<?= (int) $e['id_evaluacion'] ?>" />
                        <button class="btn btn-danger btn-sm" type="submit">🗑</button>
                      </form>
                    <?php else: ?>
                      <button class="btn btn-ghost btn-sm" onclick='abrirTemas(<?= json_encode($e['titulo']) ?>, <?= json_encode($e['temas'] ?? '') ?>, <?= json_encode($e['retroalimentacion'] ?? '') ?>)'>👁️ Detalle</button>
                    <?php endif; ?>
                  </div></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if ($evalDetalle): ?>
      <div class="card fade-in" style="margin-top:20px;">
        <div class="card-header">
          <h3 class="card-title">Resultados — <?= htmlspecialchars($evalDetalle['titulo']) ?></h3>
          <a href="index.php?accion=evaluaciones"><button class="btn btn-ghost btn-sm">✕ Cerrar</button></a>
        </div>
        <div class="table-wrapper">
          <table>
            <thead><tr><th>Estudiante</th><th>Correo</th><th>Nota</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
              <?php if (empty($resultadosDetalle)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Aún no hay estudiantes inscritos en este curso.</td></tr>
              <?php endif; ?>
              <?php foreach ($resultadosDetalle as $r): ?>
                <tr>
                  <td class="td-main"><?= htmlspecialchars($r['nombres'] . ' ' . $r['apellidos']) ?></td>
                  <td><?= htmlspecialchars($r['correo']) ?></td>
                  <td><?= $r['nota'] !== null ? '<strong style="color:' . ($r['nota'] >= $evalDetalle['nota_minima'] ? 'var(--success)' : 'var(--warning)') . ';">' . htmlspecialchars($r['nota']) . '</strong>' : '<span style="color:var(--text-muted);">Sin calificar</span>' ?></td>
                  <td><?= $r['nota'] !== null ? ($r['nota'] >= $evalDetalle['nota_minima'] ? '<span class="badge badge-green">Aprobado</span>' : '<span class="badge badge-gold">Reprobado</span>') : '<span class="badge badge-muted">Pendiente</span>' ?></td>
                  <td><button class="btn btn-primary btn-sm" onclick='abrirCalificar(<?= (int) $evalDetalle['id_evaluacion'] ?>, <?= (int) $r['id_estudiante'] ?>, <?= json_encode($r['nombres'] . ' ' . $r['apellidos']) ?>, <?= $r['nota'] !== null ? $r['nota'] : 'null' ?>, <?= json_encode($r['retroalimentacion'] ?? '') ?>)'>📊 Calificar</button></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<?php if ($rol === 'estudiante'): ?>
<div class="modal-overlay" id="modal-temas">
  <div class="modal">
    <div class="modal-header"><div><h3 id="temas-titulo">Detalle</h3></div><button class="modal-close" onclick="cerrarModal('modal-temas')">✕</button></div>
    <p style="font-size:0.85rem;margin-bottom:14px;"><strong>Temas a evaluar:</strong></p>
    <p id="temas-contenido" style="font-size:0.85rem;color:var(--text-dim);margin-bottom:18px;"></p>
    <div id="temas-retro-wrap" style="display:none;">
      <p style="font-size:0.85rem;margin-bottom:6px;"><strong>Retroalimentación del docente:</strong></p>
      <p id="temas-retro" style="font-size:0.85rem;color:var(--text-dim);"></p>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="button" onclick="cerrarModal('modal-temas')">Cerrar</button></div>
  </div>
</div>
<?php endif; ?>

<?php if ($esDocente): ?>
<div class="modal-overlay" id="modal-crear">
  <div class="modal">
    <div class="modal-header"><div><h3>Nueva Evaluación</h3></div><button class="modal-close" onclick="cerrarModal('modal-crear')">✕</button></div>
    <form method="POST" action="index.php?accion=crearEvaluacion">
      <div class="form-group">
        <label class="form-label">Curso</label>
        <select class="form-control" name="id_curso" required>
          <?php foreach ($cursosFiltro as $c): ?>
            <option value="<?= (int) $c['id_curso'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Título</label><input class="form-control" type="text" name="titulo" required /></div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Tipo</label>
          <select class="form-control" name="tipo"><option value="examen">Examen</option><option value="quiz">Quiz</option></select>
        </div>
        <div class="form-group"><label class="form-label">Duración (min)</label><input class="form-control" type="number" name="duracion_min" value="60" required /></div>
      </div>
      <div class="form-group"><label class="form-label">Temas a evaluar</label><textarea class="form-control" name="temas"></textarea></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Fecha y hora</label><input class="form-control" type="datetime-local" name="fecha" required /></div>
        <div class="form-group"><label class="form-label">Ponderación (%)</label><input class="form-control" type="number" step="0.01" name="ponderacion" value="20" required /></div>
      </div>
      <div class="form-group"><label class="form-label">Nota mínima de aprobación</label><input class="form-control" type="number" step="0.1" min="0" max="10" name="nota_minima" value="7" required /></div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-crear')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Crear Evaluación</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-editar">
  <div class="modal">
    <div class="modal-header"><div><h3>Editar Evaluación</h3></div><button class="modal-close" onclick="cerrarModal('modal-editar')">✕</button></div>
    <form method="POST" action="index.php?accion=editarEvaluacion">
      <input type="hidden" name="id_evaluacion" id="edit-e-id" />
      <div class="form-group"><label class="form-label">Título</label><input class="form-control" type="text" name="titulo" id="edit-e-titulo" required /></div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Tipo</label>
          <select class="form-control" name="tipo" id="edit-e-tipo"><option value="examen">Examen</option><option value="quiz">Quiz</option></select>
        </div>
        <div class="form-group"><label class="form-label">Duración (min)</label><input class="form-control" type="number" name="duracion_min" id="edit-e-duracion" required /></div>
      </div>
      <div class="form-group"><label class="form-label">Temas a evaluar</label><textarea class="form-control" name="temas" id="edit-e-temas"></textarea></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Fecha y hora</label><input class="form-control" type="datetime-local" name="fecha" id="edit-e-fecha" required /></div>
        <div class="form-group"><label class="form-label">Ponderación (%)</label><input class="form-control" type="number" step="0.01" name="ponderacion" id="edit-e-ponderacion" required /></div>
      </div>
      <div class="form-group"><label class="form-label">Nota mínima de aprobación</label><input class="form-control" type="number" step="0.1" min="0" max="10" name="nota_minima" id="edit-e-notamin" required /></div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-editar')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-calificar">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header"><div><h3 id="calificar-nombre">Calificar</h3></div><button class="modal-close" onclick="cerrarModal('modal-calificar')">✕</button></div>
    <form method="POST" action="index.php?accion=calificarEvaluacion">
      <input type="hidden" name="id_evaluacion" id="calificar-eval" />
      <input type="hidden" name="id_estudiante" id="calificar-est" />
      <div class="form-group"><label class="form-label">Nota (0 - 10)</label><input class="form-control" type="number" step="0.1" min="0" max="10" name="nota" id="calificar-nota" required /></div>
      <div class="form-group"><label class="form-label">Retroalimentación</label><textarea class="form-control" name="retro" id="calificar-retro"></textarea></div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-calificar')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar Calificación</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script src="public/js/modales.js"></script>
<script>
  function abrirTemas(titulo, temas, retro) {
    document.getElementById('temas-titulo').textContent = titulo;
    document.getElementById('temas-contenido').textContent = temas || 'No especificado.';
    const wrap = document.getElementById('temas-retro-wrap');
    if (retro) { wrap.style.display = 'block'; document.getElementById('temas-retro').textContent = retro; }
    else { wrap.style.display = 'none'; }
    abrirModal('modal-temas');
  }
  function abrirEditar(e) {
    document.getElementById('edit-e-id').value = e.id_evaluacion;
    document.getElementById('edit-e-titulo').value = e.titulo;
    document.getElementById('edit-e-tipo').value = e.tipo;
    document.getElementById('edit-e-duracion').value = e.duracion_min;
    document.getElementById('edit-e-temas').value = e.temas || '';
    document.getElementById('edit-e-fecha').value = e.fecha.replace(' ', 'T').substring(0,16);
    document.getElementById('edit-e-ponderacion').value = e.ponderacion;
    document.getElementById('edit-e-notamin').value = e.nota_minima;
    abrirModal('modal-editar');
  }
  function abrirCalificar(idEval, idEst, nombre, nota, retro) {
    document.getElementById('calificar-eval').value = idEval;
    document.getElementById('calificar-est').value = idEst;
    document.getElementById('calificar-nombre').textContent = 'Calificar a ' + nombre;
    document.getElementById('calificar-nota').value = nota !== null ? nota : '';
    document.getElementById('calificar-retro').value = retro || '';
    abrirModal('modal-calificar');
  }
</script>
</body>
</html>
