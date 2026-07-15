<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Crear Cuenta</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
  <div class="login-page">

    <div class="login-left">
      <div class="login-box fade-in">
        <div class="login-logo">Edu<em>Plat</em></div>
        <p class="login-tagline">Plataforma de Cursos en Línea · G09</p>

        <h2 style="margin-bottom:5px;font-size:1.2rem;font-weight:400;">Crear una cuenta</h2>
        <p style="font-size:0.8rem;margin-bottom:26px;">Regístrate para empezar a aprender</p>

        <?php if (!empty($errores)): ?>
          <div style="background:rgba(220,53,69,0.12);border:1px solid var(--danger);color:var(--danger);padding:9px 12px;border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:16px;">
            <?php foreach ($errores as $e): ?>
              <div>• <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?accion=registro" autocomplete="off">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Nombres</label>
              <input class="form-control" type="text" name="nombres" value="<?= htmlspecialchars($valores['nombres']) ?>" required />
            </div>
            <div class="form-group">
              <label class="form-label">Apellidos</label>
              <input class="form-control" type="text" name="apellidos" value="<?= htmlspecialchars($valores['apellidos']) ?>" required />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <input class="form-control" type="email" name="correo" placeholder="usuario@universidad.edu" value="<?= htmlspecialchars($valores['correo']) ?>" required />
          </div>
          <div class="form-group">
            <label class="form-label">Quiero registrarme como</label>
            <select class="form-control" name="rol">
              <option value="estudiante" <?= $valores['rol'] === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
              <option value="docente" <?= $valores['rol'] === 'docente' ? 'selected' : '' ?>>Docente</option>
            </select>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Contraseña</label>
              <input class="form-control" type="password" name="password" placeholder="Mínimo 8 caracteres" required />
            </div>
            <div class="form-group">
              <label class="form-label">Confirmar contraseña</label>
              <input class="form-control" type="password" name="password2" placeholder="Repite la contraseña" required />
            </div>
          </div>

          <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center;padding:10px;margin-top:6px;">
            Crear Cuenta
          </button>
        </form>

        <div class="divider"></div>

        <p style="font-size:0.78rem;text-align:center;color:var(--text-muted);">
          ¿Ya tienes cuenta? <a href="index.php?accion=login">Iniciar sesión</a>
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
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduPlat — Crear Cuenta</title>
  <link rel="stylesheet" href="public/css/styles.css" />
</head>
<body>
  <div class="login-page">

    <div class="login-left">
      <div class="login-box fade-in">
        <div class="login-logo">Edu<em>Plat</em></div>
        <p class="login-tagline">Plataforma de Cursos en Línea · G09</p>

        <h2 style="margin-bottom:5px;font-size:1.2rem;font-weight:400;">Crear una cuenta</h2>
        <p style="font-size:0.8rem;margin-bottom:26px;">Regístrate para empezar a aprender</p>

        <?php if (!empty($errores)): ?>
          <div style="background:rgba(220,53,69,0.12);border:1px solid var(--danger);color:var(--danger);padding:9px 12px;border-radius:var(--radius-sm);font-size:0.78rem;margin-bottom:16px;">
            <?php foreach ($errores as $e): ?>
              <div>• <?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?accion=registro" autocomplete="off">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Nombres</label>
              <input class="form-control" type="text" name="nombres" value="<?= htmlspecialchars($valores['nombres']) ?>" required />
            </div>
            <div class="form-group">
              <label class="form-label">Apellidos</label>
              <input class="form-control" type="text" name="apellidos" value="<?= htmlspecialchars($valores['apellidos']) ?>" required />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Correo electrónico</label>
            <input class="form-control" type="email" name="correo" placeholder="usuario@universidad.edu" value="<?= htmlspecialchars($valores['correo']) ?>" required />
          </div>
          <div class="form-group">
            <label class="form-label">Quiero registrarme como</label>
            <select class="form-control" name="rol">
              <option value="estudiante" <?= $valores['rol'] === 'estudiante' ? 'selected' : '' ?>>Estudiante</option>
              <option value="docente" <?= $valores['rol'] === 'docente' ? 'selected' : '' ?>>Docente</option>
            </select>
          </div>
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label">Contraseña</label>
              <input class="form-control" type="password" name="password" placeholder="Mínimo 8 caracteres" required />
            </div>
            <div class="form-group">
              <label class="form-label">Confirmar contraseña</label>
              <input class="form-control" type="password" name="password2" placeholder="Repite la contraseña" required />
            </div>
          </div>

          <button class="btn btn-primary" type="submit" style="width:100%;justify-content:center;padding:10px;margin-top:6px;">
            Crear Cuenta
          </button>
        </form>

        <div class="divider"></div>

        <p style="font-size:0.78rem;text-align:center;color:var(--text-muted);">
          ¿Ya tienes cuenta? <a href="index.php?accion=login">Iniciar sesión</a>
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
