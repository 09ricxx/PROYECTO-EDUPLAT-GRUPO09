<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Mi Perfil</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
<div class="app-layout">

  <?php $activePage = 'perfil'; require __DIR__ . '/partials/sidebar.php'; ?>

  <main class="main-content">
    <div class="topbar">
      <span class="topbar-title">Mi Perfil</span>
      <div class="topbar-actions">
        <div class="notif-btn">🔔<span class="notif-dot"></span></div>
        <div class="avatar" style="width:30px;height:30px;font-size:0.68rem;"><?= htmlspecialchars(inicialesUsuario()) ?></div>
      </div>
    </div>

    <div class="page-body">
      <?php pintarFlash(); ?>

      <div class="fade-in" style="margin-bottom:24px;">
        <h1>Mi Perfil</h1>
        <p style="margin-top:3px;font-size:0.8rem;">Administra tu información personal y tu contraseña.</p>
      </div>

      <div style="display:grid; grid-template-columns:280px 1fr; gap:20px; align-items:start;">

        <div class="card fade-in fade-in-1" style="text-align:center;">
          <div class="avatar" style="width:80px;height:80px;font-size:1.6rem;margin:0 auto 14px;"><?= htmlspecialchars(inicialesUsuario()) ?></div>
          <h3 style="margin-bottom:2px;"><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></h3>
          <p style="font-size:0.8rem;margin-bottom:10px;"><?= htmlspecialchars($usuario['correo']) ?></p>
          <span class="badge badge-blue"><?= ucfirst($usuario['rol']) ?></span>

          <div class="divider"></div>

          <?php if ($rol === 'estudiante'): ?>
            <div style="display:flex;flex-direction:column;gap:10px;text-align:left;">
              <div style="display:flex;justify-content:space-between;font-size:0.8rem;"><span style="color:var(--text-muted);">Cursos inscritos</span><strong><?= $statCursos ?></strong></div>
              <div style="display:flex;justify-content:space-between;font-size:0.8rem;"><span style="color:var(--text-muted);">Tareas entregadas</span><strong><?= $statTareas ?></strong></div>
              <div style="display:flex;justify-content:space-between;font-size:0.8rem;"><span style="color:var(--text-muted);">Promedio general</span><strong style="color:var(--success);"><?= $statPromedio !== null ? $statPromedio : '—' ?></strong></div>
            </div>
          <?php elseif ($rol === 'docente'): ?>
            <div style="display:flex;flex-direction:column;gap:10px;text-align:left;">
              <div style="display:flex;justify-content:space-between;font-size:0.8rem;"><span style="color:var(--text-muted);">Cursos dictados</span><strong><?= $statCursos ?></strong></div>
            </div>
          <?php endif; ?>

          <div class="divider"></div>
          <p style="font-size:0.7rem;color:var(--text-muted);">Miembro desde <?= date('d M Y', strtotime($usuario['fecha_registro'])) ?></p>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px;">
          <div class="card fade-in fade-in-2">
            <div class="card-header"><h3 class="card-title">Información Personal</h3></div>
            <form method="POST" action="index.php?accion=actualizarPerfil">
              <div class="form-grid">
                <div class="form-group"><label class="form-label">Nombres</label><input class="form-control" type="text" name="nombres" value="<?= htmlspecialchars($usuario['nombres']) ?>" required /></div>
                <div class="form-group"><label class="form-label">Apellidos</label><input class="form-control" type="text" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos']) ?>" required /></div>
              </div>
              <div class="form-group"><label class="form-label">Correo electrónico</label><input class="form-control" type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required /></div>
              <div class="form-actions" style="justify-content:flex-start;">
                <button class="btn btn-primary" type="submit">Guardar Cambios</button>
              </div>
            </form>
          </div>

          <div class="card fade-in fade-in-3">
            <div class="card-header"><h3 class="card-title">Cambiar Contraseña</h3></div>
            <form method="POST" action="index.php?accion=cambiarPassword">
              <div class="form-group"><label class="form-label">Contraseña actual</label><input class="form-control" type="password" name="password_actual" required /></div>
              <div class="form-grid">
                <div class="form-group"><label class="form-label">Nueva contraseña</label><input class="form-control" type="password" name="password_nueva" placeholder="Mínimo 8 caracteres" required /></div>
                <div class="form-group"><label class="form-label">Repetir nueva contraseña</label><input class="form-control" type="password" name="password_repetir" required /></div>
              </div>
              <div class="form-actions" style="justify-content:flex-start;">
                <button class="btn btn-primary" type="submit">Actualizar Contraseña</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </main>
</div>
</body>
</html>
