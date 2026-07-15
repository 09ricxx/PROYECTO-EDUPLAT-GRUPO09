<?php
/**
 * config/auth.php
 * Manejo de sesión, control de acceso y mensajes flash.
 * Se incluye una sola vez desde index.php.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requerirSesion(): void
{
    if (empty($_SESSION['id_usuario'])) {
        header('Location: index.php?accion=login&error=sesion_requerida');
        exit;
    }
}

/** Restringe el acceso a un conjunto de roles. Ej: requerirRol(['docente','admin']) */
function requerirRol(array $roles): void
{
    requerirSesion();
    if (!in_array($_SESSION['rol'] ?? '', $roles, true)) {
        flash('error', 'No tienes permisos para acceder a esa sección.');
        header('Location: index.php?accion=dashboard');
        exit;
    }
}

function inicialesUsuario(): string
{
    $n = $_SESSION['nombres']   ?? '';
    $a = $_SESSION['apellidos'] ?? '';
    $ini = mb_strtoupper(mb_substr($n, 0, 1) . mb_substr($a, 0, 1));
    return $ini !== '' ? $ini : '??';
}

function rolUsuario(): string
{
    return ucfirst($_SESSION['rol'] ?? '');
}

/* ---------------------------------------------------------
 * Mensajes de confirmación / alerta (flash messages)
 * --------------------------------------------------------- */
function flash(string $tipo, string $mensaje): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function obtenerFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function pintarFlash(): void
{
    $f = obtenerFlash();
    if (!$f) return;
    $colores = [
        'success' => ['bg' => 'rgba(40,167,69,0.12)',  'bd' => '#28a745', 'tx' => '#28a745'],
        'error'   => ['bg' => 'rgba(220,53,69,0.12)',  'bd' => 'var(--danger)', 'tx' => 'var(--danger)'],
        'info'    => ['bg' => 'rgba(79,124,255,0.12)', 'bd' => 'var(--accent)', 'tx' => 'var(--accent)'],
    ];
    $c = $colores[$f['tipo']] ?? $colores['info'];
    echo '<div class="fade-in" style="background:' . $c['bg'] . ';border:1px solid ' . $c['bd'] . ';color:' . $c['tx'] . ';padding:11px 16px;border-radius:var(--radius-sm);font-size:0.82rem;margin-bottom:18px;">'
       . htmlspecialchars($f['mensaje']) . '</div>';
}
