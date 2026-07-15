<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Tareas</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<div class="app-layout">

  <?php $activePage = 'tareas'; require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title"><?= $esDocente ? 'Gestión de Tareas' : 'Mis Tareas' ?></span>
      <div class="topbar-actions">
        <form class="search-bar" method="GET" action="index.php" id="form-buscar-tareas">
          <input type="hidden" name="accion" value="tareas" />
          <span class="search-icon">⌕</span>
          <input type="text" name="q" id="input-buscar-tareas" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar tarea..." autocomplete="off" />
          <?php if ($idCursoFiltro): ?><input type="hidden" name="curso" value="<?= $idCursoFiltro ?>" /><?php endif; ?>
        </form>
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
        <a href="index.php?accion=perfil"><div class="avatar" style="width:30px;height:30px;font-size:0.68rem;cursor:pointer;"><?= htmlspecialchars(inicialesUsuario()) ?></div></a>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="section-header fade-in">
        <div>
          <h2><?= $esDocente ? 'Gestión de Tareas' : 'Mis Tareas' ?></h2>
          <p style="margin-top:4px;"><?= $esDocente ? 'Crea y califica las tareas de tus cursos' : 'Revisa y entrega las tareas de tus cursos' ?></p>
        </div>
        <?php if ($esDocente): ?>
          <button class="btn btn-primary" onclick="abrirModal('modal-crear')">+ Nueva Tarea</button>
        <?php endif; ?>
      </div>

      <?php
        $pendientes = 0; $entregadas = 0; $vencidas = 0;
        foreach ($tareas as $t) {
          $tieneEntrega = !empty($t['id_entrega']);
          $venc = strtotime($t['fecha_limite']) < time();
          if ($rol === 'estudiante') {
            if ($tieneEntrega) $entregadas++;
            elseif ($venc) $vencidas++;
            else $pendientes++;
          }
        }
      ?>
      <?php if ($rol === 'estudiante'): ?>
      <div class="stats-grid fade-in fade-in-1" style="grid-template-columns:repeat(4,1fr);">
        <div class="stat-card"><div class="stat-icon gold">📝</div><div class="stat-value" id="stat-pendientes"><?= $pendientes ?></div><div class="stat-label">Pendientes</div></div>
        <div class="stat-card"><div class="stat-icon blue">🔄</div><div class="stat-value" id="stat-revision"><?= count(array_filter($tareas, fn($t) => !empty($t['id_entrega']) && $t['estado_entrega'] === 'entregada')) ?></div><div class="stat-label">En revisión</div></div>
        <div class="stat-card"><div class="stat-icon green">✅</div><div class="stat-value" id="stat-entregadas"><?= $entregadas ?></div><div class="stat-label">Entregadas</div></div>
        <div class="stat-card"><div class="stat-icon red">⏰</div><div class="stat-value" id="stat-vencidas"><?= $vencidas ?></div><div class="stat-label">Vencidas</div></div>
      </div>
      <?php endif; ?>

      <div style="display:flex; gap:12px; align-items:center; margin-bottom:20px; flex-wrap:wrap;" class="fade-in fade-in-2">
        <form method="GET" action="index.php" style="display:flex;gap:12px;align-items:center;" id="form-filtro-curso">
          <input type="hidden" name="accion" value="tareas" />
          <input type="hidden" name="q" value="<?= htmlspecialchars($busqueda) ?>" />
          <select class="form-control" name="curso" id="select-filtro-curso" style="width:auto;padding:8px 14px;">
            <option value="">Todos los cursos</option>
            <?php foreach ($cursosFiltro as $c): ?>
              <option value="<?= (int) $c['id_curso'] ?>" <?= $idCursoFiltro == $c['id_curso'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <div class="card fade-in fade-in-3" style="padding:0;" id="card-tareas" data-rol="<?= htmlspecialchars($rol) ?>" data-es-docente="<?= $esDocente ? '1' : '0' ?>">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Tarea</th>
                <th>Curso</th>
                <th>Fecha Límite</th>
                <th>Ponderación</th>
                <?php if ($rol === 'estudiante'): ?><th>Mi Nota</th><?php else: ?><th>Entregas</th><?php endif; ?>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tareas-tbody">
              <?php if (empty($tareas)): ?>
                <tr id="fila-sin-resultados"><td colspan="7" style="text-align:center;color:var(--text-muted);padding:24px;">No se encontraron tareas<?= $busqueda !== '' ? ' para "' . htmlspecialchars($busqueda) . '"' : '' ?>.</td></tr>
              <?php endif; ?>
              <?php foreach ($tareas as $t):
                $vencida = strtotime($t['fecha_limite']) < time();
                $tieneEntrega = !empty($t['id_entrega']);
                $calificada = ($t['estado_entrega'] ?? '') === 'calificada';
              ?>
                <tr>
                  <td class="td-main"><?= htmlspecialchars($t['titulo']) ?></td>
                  <td><?= htmlspecialchars($t['curso_nombre']) ?></td>
                  <td><?= date('d M Y', strtotime($t['fecha_limite'])) ?></td>
                  <td><?= htmlspecialchars($t['ponderacion']) ?>%</td>
                  <?php if ($rol === 'estudiante'): ?>
                    <td><?= $t['nota'] !== null ? '<strong style="color:var(--success);">' . htmlspecialchars($t['nota']) . '</strong>' : '—' ?></td>
                  <?php else: ?>
                    <td><?= (int) ($t['total_entregas'] ?? 0) ?></td>
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
                  <td><div class="table-actions">
                    <?php if ($rol === 'estudiante'): ?>
                      <?php if (!$tieneEntrega && !$vencida): ?>
                        <button class="btn btn-primary btn-sm" onclick='abrirEntregar(<?= (int) $t['id_tarea'] ?>, <?= json_encode($t['titulo']) ?>, <?= json_encode($t['curso_nombre']) ?>)'>📤 Entregar</button>
                      <?php elseif ($tieneEntrega && !$calificada): ?>
                        <button class="btn btn-ghost btn-sm" onclick='abrirEntregar(<?= (int) $t['id_tarea'] ?>, <?= json_encode($t['titulo']) ?>, <?= json_encode($t['curso_nombre']) ?>)'>✎ Editar</button>
                        <form method="POST" action="index.php?accion=eliminarEntrega" onsubmit="return confirm('¿Retirar tu entrega?');" style="display:inline;">
                          <input type="hidden" name="id_entrega" value="<?= (int) $t['id_entrega'] ?>" />
                          <button class="btn btn-ghost btn-sm" type="submit">🗑</button>
                        </form>
                      <?php else: ?>
                        <span style="font-size:0.78rem;color:var(--text-muted);">—</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <a href="index.php?accion=tareas&ver=<?= (int) $t['id_tarea'] ?>"><button class="btn btn-ghost btn-sm">👁️ Entregas</button></a>
                      <button class="btn btn-ghost btn-sm" onclick='abrirEditar(<?= json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✎</button>
                      <form method="POST" action="index.php?accion=eliminarTarea" onsubmit="return confirm('¿Eliminar esta tarea? Se eliminarán también sus entregas.');" style="display:inline;">
                        <input type="hidden" name="id_tarea" value="<?= (int) $t['id_tarea'] ?>" />
                        <button class="btn btn-danger btn-sm" type="submit">🗑</button>
                      </form>
                    <?php endif; ?>
                  </div></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if ($tareaDetalle): ?>
      <div class="card fade-in" style="margin-top:20px;">
        <div class="card-header">
          <h3 class="card-title">Entregas — <?= htmlspecialchars($tareaDetalle['titulo']) ?></h3>
          <a href="index.php?accion=tareas"><button class="btn btn-ghost btn-sm">✕ Cerrar</button></a>
        </div>
        <div class="table-wrapper">
          <table>
            <thead><tr><th>Estudiante</th><th>Fecha entrega</th><th>Enlace</th><th>Nota</th><th>Acciones</th></tr></thead>
            <tbody>
              <?php if (empty($entregasDetalle)): ?>
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:20px;">Aún no hay entregas para esta tarea.</td></tr>
              <?php endif; ?>
              <?php foreach ($entregasDetalle as $en): ?>
                <tr>
                  <td class="td-main"><?= htmlspecialchars($en['nombres'] . ' ' . $en['apellidos']) ?></td>
                  <td><?= date('d M Y H:i', strtotime($en['fecha_entrega'])) ?></td>
                  <td><?php if ($en['enlace']): ?><a href="<?= htmlspecialchars($en['enlace']) ?>" target="_blank">Ver enlace ↗</a><?php else: ?>—<?php endif; ?></td>
                  <td><?= $en['nota'] !== null ? '<strong style="color:var(--success);">' . htmlspecialchars($en['nota']) . '</strong>' : '<span style="color:var(--text-muted);">Sin calificar</span>' ?></td>
                  <td><button class="btn btn-primary btn-sm" onclick='abrirCalificar(<?= (int) $en['id_entrega'] ?>, <?= (int) $tareaDetalle['id_tarea'] ?>, <?= json_encode($en['nombres'] . ' ' . $en['apellidos']) ?>, <?= $en['nota'] !== null ? $en['nota'] : 'null' ?>, <?= json_encode($en['retroalimentacion'] ?? '') ?>)'>📊 Calificar</button></td>
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
<!-- MODAL: Entregar/Editar -->
<div class="modal-overlay" id="modal-entregar">
  <div class="modal">
    <div class="modal-header">
      <div><h3 id="entregar-titulo">Entregar Tarea</h3><p style="font-size:0.82rem;margin-top:2px;" id="entregar-curso"></p></div>
      <button class="modal-close" onclick="cerrarModal('modal-entregar')">✕</button>
    </div>
    <form method="POST" action="index.php?accion=entregarTarea">
      <input type="hidden" name="id_tarea" id="entregar-id" />
      <div class="form-group"><label class="form-label">Comentario (opcional)</label><textarea class="form-control" name="comentario" placeholder="Agrega una nota para tu docente..."></textarea></div>
      <div class="form-group"><label class="form-label">Enlace de entrega (URL o repositorio)</label><input class="form-control" type="url" name="enlace" required placeholder="https://github.com/tu-usuario/practica" /></div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-entregar')">Cancelar</button>
        <button class="btn btn-primary" type="submit">✅ Confirmar Entrega</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($esDocente): ?>
<!-- MODAL: Crear Tarea -->
<div class="modal-overlay" id="modal-crear">
  <div class="modal">
    <div class="modal-header"><div><h3>Nueva Tarea</h3></div><button class="modal-close" onclick="cerrarModal('modal-crear')">✕</button></div>
    <form method="POST" action="index.php?accion=crearTarea">
      <div class="form-group">
        <label class="form-label">Curso</label>
        <select class="form-control" name="id_curso" required>
          <?php foreach ($cursosFiltro as $c): ?>
            <option value="<?= (int) $c['id_curso'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Título</label><input class="form-control" type="text" name="titulo" required /></div>
      <div class="form-group"><label class="form-label">Instrucciones</label><textarea class="form-control" name="instrucciones"></textarea></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Ponderación (%)</label><input class="form-control" type="number" step="0.01" min="0" max="100" name="ponderacion" value="10" required /></div>
        <div class="form-group"><label class="form-label">Fecha límite</label><input class="form-control" type="datetime-local" name="fecha_limite" required /></div>
      </div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-crear')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Crear Tarea</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Editar Tarea -->
<div class="modal-overlay" id="modal-editar">
  <div class="modal">
    <div class="modal-header"><div><h3>Editar Tarea</h3></div><button class="modal-close" onclick="cerrarModal('modal-editar')">✕</button></div>
    <form method="POST" action="index.php?accion=editarTarea">
      <input type="hidden" name="id_tarea" id="edit-t-id" />
      <div class="form-group"><label class="form-label">Título</label><input class="form-control" type="text" name="titulo" id="edit-t-titulo" required /></div>
      <div class="form-group"><label class="form-label">Instrucciones</label><textarea class="form-control" name="instrucciones" id="edit-t-instrucciones"></textarea></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Ponderación (%)</label><input class="form-control" type="number" step="0.01" min="0" max="100" name="ponderacion" id="edit-t-ponderacion" required /></div>
        <div class="form-group"><label class="form-label">Fecha límite</label><input class="form-control" type="datetime-local" name="fecha_limite" id="edit-t-fecha" required /></div>
      </div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-editar')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Calificar -->
<div class="modal-overlay" id="modal-calificar">
  <div class="modal" style="max-width:420px;">
    <div class="modal-header"><div><h3 id="calificar-nombre">Calificar</h3></div><button class="modal-close" onclick="cerrarModal('modal-calificar')">✕</button></div>
    <form method="POST" action="index.php?accion=calificarTarea">
      <input type="hidden" name="id_entrega" id="calificar-id" />
      <input type="hidden" name="id_tarea" id="calificar-id-tarea" />
      <div class="form-group"><label class="form-label">Nota (0 - 10)</label><input class="form-control" type="number" step="0.1" min="0" max="10" name="nota" id="calificar-nota" required /></div>
      <div class="form-group"><label class="form-label">Retroalimentación</label><textarea class="form-control" name="retro" id="calificar-retro" placeholder="Comentarios para el estudiante..."></textarea></div>
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
  function abrirEntregar(id, titulo, curso) {
    document.getElementById('entregar-id').value = id;
    document.getElementById('entregar-titulo').textContent = titulo;
    document.getElementById('entregar-curso').textContent = curso;
    abrirModal('modal-entregar');
  }
  function abrirEditar(t) {
    document.getElementById('edit-t-id').value = t.id_tarea;
    document.getElementById('edit-t-titulo').value = t.titulo;
    document.getElementById('edit-t-instrucciones').value = t.instrucciones || '';
    document.getElementById('edit-t-ponderacion').value = t.ponderacion;
    document.getElementById('edit-t-fecha').value = t.fecha_limite.replace(' ', 'T').substring(0,16);
    abrirModal('modal-editar');
  }
  function abrirCalificar(id, idTarea, nombre, nota, retro) {
    document.getElementById('calificar-id').value = id;
    document.getElementById('calificar-id-tarea').value = idTarea;
    document.getElementById('calificar-nombre').textContent = 'Calificar a ' + nombre;
    document.getElementById('calificar-nota').value = nota !== null ? nota : '';
    document.getElementById('calificar-retro').value = retro || '';
    abrirModal('modal-calificar');
  }
</script>
<script src="public/js/tareas-buscar.js"></script>
</body>
</html>
