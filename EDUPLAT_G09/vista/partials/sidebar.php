<?php $activePage = $activePage ?? ''; ?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-mark">EduPlat <span>G09</span></div>
    <p>Cursos en Línea</p>
  </div>
  <nav class="sidebar-nav">
    <span class="nav-section-label">Principal</span>
    <a href="index.php?accion=dashboard" class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>"><span class="icon">⌂</span> Dashboard</a>
    <a href="index.php?accion=cursos" class="nav-item <?= $activePage === 'cursos' ? 'active' : '' ?>"><span class="icon">≡</span> <?= ($_SESSION['rol'] ?? '') === 'docente' ? 'Mis Cursos (Docente)' : 'Mis Cursos' ?></a>
    <a href="index.php?accion=tareas" class="nav-item <?= $activePage === 'tareas' ? 'active' : '' ?>"><span class="icon">✎</span> Tareas</a>
    <a href="index.php?accion=evaluaciones" class="nav-item <?= $activePage === 'evaluaciones' ? 'active' : '' ?>"><span class="icon">◫</span> Evaluaciones</a>
    <span class="nav-section-label">Cuenta</span>
    <a href="index.php?accion=perfil" class="nav-item <?= $activePage === 'perfil' ? 'active' : '' ?>"><span class="icon">◯</span> Mi Perfil</a>
    <a href="index.php?accion=creditos" class="nav-item <?= $activePage === 'creditos' ? 'active' : '' ?>"><span class="icon">·</span> Créditos</a>
    <a href="index.php?accion=logout" class="nav-item" style="color:var(--danger);"><span class="icon">←</span> Cerrar Sesión</a>
  </nav>
  <div class="sidebar-user">
    <div class="avatar"><?= htmlspecialchars(inicialesUsuario()) ?></div>
    <div class="sidebar-user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['nombres'] . ' ' . $_SESSION['apellidos']) ?></div>
      <div class="role"><?= htmlspecialchars(rolUsuario()) ?></div>
    </div>
  </div>
</aside>
