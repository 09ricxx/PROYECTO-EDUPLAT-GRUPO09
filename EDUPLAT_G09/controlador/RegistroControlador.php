<?php
require_once __DIR__ . '/../modelo/Usuario.php';

class RegistroControlador
{
    /** Muestra el formulario vacío. */
    public function mostrar(): void
    {
        $errores = [];
        $valores = ['nombres' => '', 'apellidos' => '', 'correo' => '', 'rol' => 'estudiante'];
        require __DIR__ . '/../vista/registro.php';
    }

    public function procesarRegistro(): void
    {
        $errores = [];
        $valores = [
            'nombres'   => trim($_POST['nombres'] ?? ''),
            'apellidos' => trim($_POST['apellidos'] ?? ''),
            'correo'    => trim($_POST['correo'] ?? ''),
            'rol'       => in_array($_POST['rol'] ?? '', ['estudiante', 'docente'], true) ? $_POST['rol'] : 'estudiante',
        ];
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($valores['nombres'] === '' || $valores['apellidos'] === '' || $valores['correo'] === '') {
            $errores[] = 'Todos los campos son obligatorios.';
        }
        if (!filter_var($valores['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Ingresa un correo electrónico válido.';
        }
        if (strlen($password) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        }
        if ($password !== $password2) {
            $errores[] = 'Las contraseñas no coinciden.';
        }
        if (empty($errores) && Usuario::buscarPorCorreo($valores['correo'])) {
            $errores[] = 'Ya existe una cuenta registrada con ese correo.';
        }

        if (empty($errores)) {
            Usuario::crear($valores['nombres'], $valores['apellidos'], $valores['correo'], $password, $valores['rol']);
            flash('success', 'Cuenta creada correctamente. Ya puedes iniciar sesión.');
            header('Location: index.php?accion=login&registro=ok');
            exit;
        }

        require __DIR__ . '/../vista/registro.php';
    }
}
