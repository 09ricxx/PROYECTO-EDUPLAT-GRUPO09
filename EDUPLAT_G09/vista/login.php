<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Iniciar Sesión</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
  <div class="login-page">

    <div class="login-left">
      <div class="login-box fade-in">
        <div class="login-logo">Edu<em>Plat</em></div>
        <p class="login-tagline">Plataforma de Cursos en Línea · G09</p>

        <h2 style="margin-bottom:5px;font-size:1.2rem;font-weight:400;">Bienvenido de nuevo</h2>
        <p style="font-size:0.8rem;margin-bottom:26px;">Ingresa tus credenciales para continuar</p>

        <?php if (!empty($error)): ?>
          <div style="background:rgba(220,53,69,0.12);border:1px solid var(--danger);color:var(--danger);padding:9px 12px;border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:16px;">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'sesion_requerida'): ?>
          <div style="background:rgba(220,53,69,0.12);border:1px solid var(--danger);color:var(--danger);padding:9px 12px;border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:16px;">
            Debes iniciar sesión para acceder a esa página.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['registro']) && $_GET['registro'] === 'ok'): ?>
          <div style="background:rgba(40,167,69,0.12);border:1px solid #28a745;color:#28a745;padding:9px 12px;border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:16px;">
            Cuenta creada correctamente. Ya puedes iniciar sesión.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
          <div style="background:rgba(79,124,255,0.12);border:1px solid var(--accent);color:var(--accent);padding:9px 12px;border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:16px;">
            Sesión cerrada correctamente.
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?accion=login" autocomplete="off">
          <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <input class="form-control" type="email" name="correo" placeholder="usuario@universidad.edu"
                   value="<?= htmlspecialchars($correoIngresado ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label class="form-label">Contraseña</label>
            <input class="form-control" type="password" name="password" placeholder="••••••••" required />
          </div>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <label style="display:flex;align-items:center;gap:7px;font-size:0.78rem;color:var(--text-dim);cursor:pointer;">
              <input type="checkbox" style="accent-color:var(--accent);" />
              Recordarme
            </label>
            <a href="#" style="font-size:0.78rem;color:var(--text-dim);">¿Olvidaste tu contraseña?</a>
          </div>

          <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center;padding:10px;">
            Iniciar Sesión
          </button>
        </form>

        <div class="divider"></div>

        <p style="font-size:0.78rem;text-align:center;color:var(--text-muted);">
          ¿Eres nuevo? <a href="index.php?accion=registro">Crear una cuenta</a>
        </p>

        <p style="font-size:0.7rem;text-align:center;color:var(--text-muted);margin-top:14px;">
          Demo: docente@eduplat.edu · estudiante@eduplat.edu · admin@eduplat.edu
        </p>
      </div>
    </div>

    <div class="login-right">
      <div class="login-hero-text fade-in">
        <h2>Aprende. Enseña. Crece.</h2>
        <p>Una plataforma para gestionar cursos, tareas y evaluaciones en un solo lugar.</p>

        <div class="feature-list">
          <div class="feature-item">
            <span class="fi">≡</span>
            <p>Accede a contenidos de tus cursos en cualquier momento</p>
          </div>
          <div class="feature-item">
            <span class="fi">✓</span>
            <p>Entrega tareas y revisa calificaciones en tiempo real</p>
          </div>
          <div class="feature-item">
            <span class="fi">◫</span>
            <p>Seguimiento académico para docentes y estudiantes</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>
</html>
