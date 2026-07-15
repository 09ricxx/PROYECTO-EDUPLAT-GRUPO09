<?php
require_once __DIR__ . '/../modelo/Usuario.php';
require_once __DIR__ . '/../modelo/Curso.php';
require_once __DIR__ . '/../modelo/Entrega.php';
require_once __DIR__ . '/../modelo/Evaluacion.php';

class PerfilControlador
{
    
    public function mostrar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $usuario = Usuario::buscarPorId($uid);

        if ($rol === 'estudiante') {
            $statCursos = Curso::contarInscritos($uid, null);
            $statTareas = Entrega::contarEntregadasPorEstudiante($uid);
            $promTareas = Entrega::promedioEstudiante($uid);
            $promEval   = Evaluacion::promedioEstudiante($uid);
            $statPromedio = null;
            if ($promTareas !== null && $promEval !== null) $statPromedio = round(($promTareas + $promEval) / 2, 1);
            elseif ($promTareas !== null) $statPromedio = $promTareas;
            elseif ($promEval !== null) $statPromedio = $promEval;
        } elseif ($rol === 'docente') {
            $statCursos = Curso::contarPorDocente($uid, null);
            $statTareas = null;
            $statPromedio = null;
        } else {
            $statCursos = null; $statTareas = null; $statPromedio = null;
        }

        require __DIR__ . '/../vista/perfil.php';
    }

    
    public function actualizarDatos(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $nombres   = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');

        if ($nombres === '' || $apellidos === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Verifica los datos ingresados. El correo debe ser válido.');
        } else {
            $existente = Usuario::buscarPorCorreo($correo);
            if ($existente && (int) $existente['id_usuario'] !== $uid) {
                flash('error', 'Ese correo ya está en uso por otra cuenta.');
            } else {
                Usuario::actualizarPerfil($uid, $nombres, $apellidos, $correo);
                $_SESSION['nombres']   = $nombres;
                $_SESSION['apellidos'] = $apellidos;
                $_SESSION['correo']    = $correo;
                flash('success', 'Perfil actualizado correctamente.');
            }
        }
        header('Location: index.php?accion=perfil');
        exit;
    }

    
    public function cambiarPassword(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $actual  = $_POST['password_actual'] ?? '';
        $nueva   = $_POST['password_nueva'] ?? '';
        $repetir = $_POST['password_repetir'] ?? '';

        if (!Usuario::verificarPassword($uid, $actual)) {
            flash('error', 'La contraseña actual no es correcta.');
        } elseif (strlen($nueva) < 8) {
            flash('error', 'La nueva contraseña debe tener al menos 8 caracteres.');
        } elseif ($nueva !== $repetir) {
            flash('error', 'Las contraseñas nuevas no coinciden.');
        } else {
            Usuario::actualizarPassword($uid, $nueva);
            flash('success', 'Contraseña actualizada correctamente.');
        }
        header('Location: index.php?accion=perfil');
        exit;
    }
}
