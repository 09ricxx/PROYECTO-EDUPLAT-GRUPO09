<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Mis Cursos</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<div class="app-layout">

  <?php $activePage = 'cursos'; require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title"><?= $rol === 'docente' ? 'Mis Cursos (Docente)' : 'Cursos' ?></span>
      <div class="topbar-actions">
        <form class="search-bar" method="GET" action="index.php">
          <input type="hidden" name="accion" value="cursos" />
          <span class="search-icon">⌕</span>
          <input type="text" name="q" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar curso..." />
        </form>
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
        <a href="index.php?accion=perfil"><div class="avatar" style="width:30px;height:30px;font-size:0.68rem;cursor:pointer;"><?= htmlspecialchars(inicialesUsuario()) ?></div></a>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="section-header fade-in">
        <div>
          <h2><?= $rol === 'estudiante' ? 'Mis Cursos' : 'Gestión de Cursos' ?></h2>
          <p style="margin-top:3px;font-size:0.78rem;">
            <?= $rol === 'estudiante' ? 'Cursos en los que estás inscrito' : 'Crea, edita y administra tus cursos' ?>
          </p>
        </div>
        <?php if ($esGestor): ?>
          <button class="btn btn-primary" onclick="abrirModal('modal-crear')">+ Nuevo Curso</button>
        <?php endif; ?>
      </div>

      <div class="tab-nav fade-in fade-in-1">
        <a href="index.php?accion=cursos&q=<?= urlencode($busqueda) ?>"><button class="tab-btn <?= $filtroEstado === '' ? 'active' : '' ?>">Todos</button></a>
        <a href="index.php?accion=cursos&q=<?= urlencode($busqueda) ?>&estado=activo"><button class="tab-btn <?= $filtroEstado === 'activo' ? 'active' : '' ?>">Activos</button></a>
        <a href="index.php?accion=cursos&q=<?= urlencode($busqueda) ?>&estado=inactivo"><button class="tab-btn <?= $filtroEstado === 'inactivo' ? 'active' : '' ?>">Inactivos</button></a>
      </div>

      <div class="courses-grid fade-in fade-in-2">
        <?php if (empty($cursos)): ?>
          <p style="font-size:0.85rem;color:var(--text-muted);">No se encontraron cursos<?= $busqueda !== '' ? ' para "' . htmlspecialchars($busqueda) . '"' : '' ?>.</p>
        <?php endif; ?>
        <?php foreach ($cursos as $c): ?>
          <div class="course-card">
            <div class="course-thumb"><?= htmlspecialchars($c['icono']) ?></div>
            <div class="course-body">
              <div class="course-meta">
                <span class="badge <?= $c['estado'] === 'activo' ? 'badge-green' : 'badge-muted' ?>"><?= ucfirst($c['estado']) ?></span>
                <span style="font-size:0.7rem;color:var(--text-muted);font-family:var(--font-mono);"><?= htmlspecialchars($c['semestre'] ?? '') ?></span>
              </div>
              <div class="course-title"><?= htmlspecialchars($c['nombre']) ?></div>
              <div class="course-desc">Ing. <?= htmlspecialchars($c['docente_nombres'] . ' ' . $c['docente_apellidos']) ?> · <?= htmlspecialchars($c['descripcion'] ?? '') ?></div>

              <?php if ($rol === 'estudiante' && isset($c['id_inscripcion'])): ?>
                <div style="margin:8px 0 4px;">
                  <div class="progress-bar"><div class="progress-fill" style="width:<?= (int) $c['progreso'] ?>%"></div></div>
                  <div style="font-size:0.68rem;color:var(--text-muted);margin-top:3px;font-family:var(--font-mono);"><?= (int) $c['progreso'] ?>% completado</div>
                </div>
              <?php endif; ?>

              <div class="course-footer">
                <span><?= (int) $c['total_alumnos'] ?> alumnos</span>
                <div style="display:flex;gap:5px;">
                  <?php if ($rol === 'estudiante' && isset($c['id_inscripcion'])): ?>
                    <a href="index.php?accion=curso&id=<?= (int) $c['id_curso'] ?>"><button class="btn btn-primary btn-sm">→ Ingresar</button></a>
                    <form method="POST" action="index.php?accion=retirarCurso" onsubmit="return confirm('¿Retirarte de este curso?');">
                      <input type="hidden" name="id_curso" value="<?= (int) $c['id_curso'] ?>" />
                      <button class="btn btn-ghost btn-sm" type="submit">Retirarme</button>
                    </form>
                  <?php elseif ($esGestor): ?>
                    <a href="index.php?accion=curso&id=<?= (int) $c['id_curso'] ?>"><button class="btn btn-ghost btn-sm">→ Ver</button></a>
                    <?php if ($rol === 'admin' || $c['docente_id'] == $uid): ?>
                      <button class="btn btn-primary btn-sm" onclick='abrirEditar(<?= json_encode($c, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>✎ Editar</button>
                      <button class="btn btn-danger btn-sm" onclick="abrirEliminar(<?= (int) $c['id_curso'] ?>, '<?= htmlspecialchars($c['nombre'], ENT_QUOTES) ?>')">🗑</button>
                    <?php endif; ?>
                  <?php else: ?>
                    <a href="index.php?accion=curso&id=<?= (int) $c['id_curso'] ?>"><button class="btn btn-ghost btn-sm">→ Ver</button></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($rol === 'estudiante'): ?>
        <div class="section-header fade-in" style="margin-top:32px;">
          <div>
            <h2>Cursos disponibles</h2>
            <p style="margin-top:3px;font-size:0.78rem;">Explora e inscríbete en nuevos cursos</p>
          </div>
        </div>
        <div class="courses-grid fade-in">
          <?php if (empty($disponibles)): ?>
            <p style="font-size:0.85rem;color:var(--text-muted);">No hay más cursos disponibles por ahora.</p>
          <?php endif; ?>
          <?php foreach ($disponibles as $c): ?>
            <div class="course-card">
              <div class="course-thumb"><?= htmlspecialchars($c['icono']) ?></div>
              <div class="course-body">
                <div class="course-meta">
                  <span class="badge badge-green">Activo</span>
                  <span style="font-size:0.7rem;color:var(--text-muted);font-family:var(--font-mono);"><?= htmlspecialchars($c['semestre'] ?? '') ?></span>
                </div>
                <div class="course-title"><?= htmlspecialchars($c['nombre']) ?></div>
                <div class="course-desc">Ing. <?= htmlspecialchars($c['docente_nombres'] . ' ' . $c['docente_apellidos']) ?> · <?= htmlspecialchars($c['descripcion'] ?? '') ?></div>
                <div class="course-footer">
                  <span></span>
                  <form method="POST" action="index.php?accion=inscribirCurso">
                    <input type="hidden" name="id_curso" value="<?= (int) $c['id_curso'] ?>" />
                    <button class="btn btn-primary btn-sm" type="submit">+ Inscribirme</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<?php if ($esGestor): ?>
<!-- MODAL: Crear Curso -->
<div class="modal-overlay" id="modal-crear">
  <div class="modal">
    <div class="modal-header">
      <div><h3>Nuevo Curso</h3></div>
      <button class="modal-close" onclick="cerrarModal('modal-crear')">✕</button>
    </div>
    <form method="POST" action="index.php?accion=crearCurso">
      <div class="form-group"><label class="form-label">Nombre del curso</label><input class="form-control" type="text" name="nombre" required /></div>
      <div class="form-group"><label class="form-label">Descripción</label><input class="form-control" type="text" name="descripcion" placeholder="Breve descripción del curso" /></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Semestre</label><input class="form-control" type="text" name="semestre" placeholder="Ej. 6to Sem." /></div>
        <div class="form-group"><label class="form-label">Ícono (emoji)</label><input class="form-control" type="text" name="icono" placeholder="💻" maxlength="4" /></div>
      </div>
      <div class="form-group">
        <label class="form-label">Estado</label>
        <select class="form-control" name="estado">
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
      </div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-crear')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Crear Curso</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Editar Curso -->
<div class="modal-overlay" id="modal-editar">
  <div class="modal">
    <div class="modal-header">
      <div><h3>Editar Curso</h3></div>
      <button class="modal-close" onclick="cerrarModal('modal-editar')">✕</button>
    </div>
    <form method="POST" action="index.php?accion=editarCurso">
      <input type="hidden" name="id_curso" id="edit-id" />
      <div class="form-group"><label class="form-label">Nombre del curso</label><input class="form-control" type="text" name="nombre" id="edit-nombre" required /></div>
      <div class="form-group"><label class="form-label">Descripción</label><input class="form-control" type="text" name="descripcion" id="edit-descripcion" /></div>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Semestre</label><input class="form-control" type="text" name="semestre" id="edit-semestre" /></div>
        <div class="form-group"><label class="form-label">Ícono (emoji)</label><input class="form-control" type="text" name="icono" id="edit-icono" maxlength="4" /></div>
      </div>
      <div class="form-group">
        <label class="form-label">Estado</label>
        <select class="form-control" name="estado" id="edit-estado">
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
      </div>
      <div class="form-actions">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-editar')">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Eliminar Curso -->
<div class="modal-overlay" id="modal-eliminar">
  <div class="modal" style="max-width:400px;text-align:center;">
    <h3 style="margin-bottom:8px;">¿Eliminar <span id="del-nombre"></span>?</h3>
    <p style="font-size:0.88rem;margin-bottom:24px;">Esta acción eliminará también sus tareas, evaluaciones e inscripciones. No se puede deshacer.</p>
    <form method="POST" action="index.php?accion=eliminarCurso">
      <input type="hidden" name="id_curso" id="del-id" />
      <div style="display:flex;gap:12px;justify-content:center;">
        <button class="btn btn-ghost" type="button" onclick="cerrarModal('modal-eliminar')">Cancelar</button>
        <button class="btn btn-danger" type="submit">Eliminar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script src="public/js/modales.js"></script>
<script>
  function abrirEditar(c) {
    document.getElementById('edit-id').value = c.id_curso;
    document.getElementById('edit-nombre').value = c.nombre;
    document.getElementById('edit-descripcion').value = c.descripcion || '';
    document.getElementById('edit-semestre').value = c.semestre || '';
    document.getElementById('edit-icono').value = c.icono || '';
    document.getElementById('edit-estado').value = c.estado;
    abrirModal('modal-editar');
  }
  function abrirEliminar(id, nombre) {
    document.getElementById('del-id').value = id;
    document.getElementById('del-nombre').textContent = '"' + nombre + '"';
    abrirModal('modal-eliminar');
  }
</script>
</body>
</html>
