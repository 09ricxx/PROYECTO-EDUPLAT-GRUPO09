<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/auth.php';

require_once __DIR__ . '/controlador/LoginControlador.php';
require_once __DIR__ . '/controlador/RegistroControlador.php';
require_once __DIR__ . '/controlador/LogoutControlador.php';
require_once __DIR__ . '/controlador/DashboardControlador.php';
require_once __DIR__ . '/controlador/CursoControlador.php';
require_once __DIR__ . '/controlador/TareaControlador.php';
require_once __DIR__ . '/controlador/EvaluacionControlador.php';
require_once __DIR__ . '/controlador/PerfilControlador.php';

$loginControlador      = new LoginControlador();
$registroControlador   = new RegistroControlador();
$logoutControlador     = new LogoutControlador();
$dashboardControlador  = new DashboardControlador();
$cursoControlador      = new CursoControlador();
$tareaControlador      = new TareaControlador();
$evaluacionControlador = new EvaluacionControlador();
$perfilControlador     = new PerfilControlador();

$accion = $_GET['accion'] ?? (!empty($_SESSION['id_usuario']) ? 'dashboard' : 'login');

$publicas = ['login', 'registro'];

if (!in_array($accion, $publicas, true) && empty($_SESSION['id_usuario'])) {
    header('Location: index.php?accion=login&error=sesion_requerida');
    exit;
}

switch ($accion) {

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loginControlador->procesarLogin();
        } else {
            require_once __DIR__ . '/vista/login.php';
        }
        break;

    case 'registro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $registroControlador->procesarRegistro();
        } else {
            $registroControlador->mostrar();
        }
        break;

    case 'logout':
        $logoutControlador->procesar();
        break;

    case 'dashboard':
        $dashboardControlador->mostrar();
        break;

    // ---- Cursos ----
    case 'cursos':
        $cursoControlador->listar();
        break;
    case 'curso':
        $cursoControlador->verDetalle();
        break;
    case 'crearCurso':
        $cursoControlador->crear();
        break;
    case 'editarCurso':
        $cursoControlador->editar();
        break;
    case 'eliminarCurso':
        $cursoControlador->eliminar();
        break;
    case 'inscribirCurso':
        $cursoControlador->inscribir();
        break;
    case 'retirarCurso':
        $cursoControlador->retirar();
        break;
    case 'crearMaterial':
        $cursoControlador->crearMaterial();
        break;
    case 'eliminarMaterial':
        $cursoControlador->eliminarMaterial();
        break;

    // ---- Tareas ----
    case 'tareas':
        $tareaControlador->listar();
        break;
    case 'buscarTareasJson':
        $tareaControlador->buscarJson();
        break;
    case 'crearTarea':
        $tareaControlador->crear();
        break;
    case 'editarTarea':
        $tareaControlador->editar();
        break;
    case 'eliminarTarea':
        $tareaControlador->eliminar();
        break;
    case 'entregarTarea':
        $tareaControlador->entregar();
        break;
    case 'eliminarEntrega':
        $tareaControlador->eliminarEntrega();
        break;
    case 'calificarTarea':
        $tareaControlador->calificar();
        break;

    // ---- Evaluaciones ----
    case 'evaluaciones':
        $evaluacionControlador->listar();
        break;
    case 'crearEvaluacion':
        $evaluacionControlador->crear();
        break;
    case 'editarEvaluacion':
        $evaluacionControlador->editar();
        break;
    case 'eliminarEvaluacion':
        $evaluacionControlador->eliminar();
        break;
    case 'calificarEvaluacion':
        $evaluacionControlador->calificar();
        break;

    // ---- Perfil ----
    case 'perfil':
        $perfilControlador->mostrar();
        break;
    case 'actualizarPerfil':
        $perfilControlador->actualizarDatos();
        break;
    case 'cambiarPassword':
        $perfilControlador->cambiarPassword();
        break;

    // ---- Estática ----
    case 'creditos':
        require_once __DIR__ . '/vista/creditos.php';
        break;

    default:
        http_response_code(404);
        echo 'Página no encontrada.';
}
