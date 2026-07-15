<?php
class LogoutControlador
{
    public function procesar(): void
    {
        $_SESSION = [];
        session_unset();
        session_destroy();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        header('Location: index.php?accion=login&logout=1');
        exit;
    }
}
