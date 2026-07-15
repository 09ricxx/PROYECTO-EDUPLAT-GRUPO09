<?php
require_once __DIR__ . '/../modelo/Usuario.php';

class LoginControlador
{
    
    public function procesarLogin(): void
    {
        $correo   = trim($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';
        $error    = '';

        if ($correo === '' || $password === '') {
            $error = 'Ingresa tu correo y tu contraseña.';
        } else {
            $usuario = Usuario::buscarPorCorreo($correo);
            $exitoso = false;

            if ($usuario && (int) $usuario['estado'] === 1 && password_verify($password, $usuario['password_hash'])) {
                $exitoso = true;
                session_regenerate_id(true);
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombres']    = $usuario['nombres'];
                $_SESSION['apellidos']  = $usuario['apellidos'];
                $_SESSION['correo']     = $usuario['correo'];
                $_SESSION['rol']        = $usuario['rol'];
            } else {
                $error = 'Correo o contraseña incorrectos.';
            }

            Usuario::registrarLogin($usuario['id_usuario'] ?? null, $correo, $exitoso);

            if ($exitoso) {
                flash('success', 'Bienvenido de nuevo, ' . $usuario['nombres'] . '.');
                header('Location: index.php?accion=dashboard');
                exit;
            }
        }

        // Login fallido: volvemos a mostrar el formulario con el error
        $correoIngresado = $correo;
        require __DIR__ . '/../vista/login.php';
    }
}
