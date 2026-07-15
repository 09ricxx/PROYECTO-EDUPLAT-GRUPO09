<?php
require_once __DIR__ . '/../modelo/Tarea.php';
require_once __DIR__ . '/../modelo/Entrega.php';
require_once __DIR__ . '/../modelo/Curso.php';

class TareaControlador
{
    
    public function listar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $esDocente = $rol === 'docente';

        $busqueda = trim($_GET['q'] ?? '');
        $idCursoFiltro = isset($_GET['curso']) ? (int) $_GET['curso'] : null;

        if ($rol === 'estudiante') {
            $tareas = Tarea::listarParaEstudiante($uid, $busqueda, $idCursoFiltro);
            $cursosFiltro = Curso::listarParaUsuario($uid, 'estudiante');
        } elseif ($esDocente) {
            $tareas = Tarea::listarParaDocente($uid, $busqueda, $idCursoFiltro);
            $cursosFiltro = Curso::listarParaUsuario($uid, 'docente');
        } else { 
            $tareas = [];
            $cursosFiltro = Curso::listarParaUsuario($uid, 'admin');
            foreach ($cursosFiltro as $c) {
                $tareas = array_merge($tareas, Tarea::listarPorCurso((int) $c['id_curso']));
            }
        }

        
        $tareaDetalle = null;
        $entregasDetalle = [];
        if ($esDocente && isset($_GET['ver'])) {
            $tareaDetalle = Tarea::obtenerPorId((int) $_GET['ver']);
            if ($tareaDetalle && (int) $tareaDetalle['docente_id'] === $uid) {
                $entregasDetalle = Entrega::listarPorTarea((int) $_GET['ver']);
            } else {
                $tareaDetalle = null;
            }
        }

        require __DIR__ . '/../vista/tareas.php';
    }

    public function buscarJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];

        $busqueda = trim($_GET['q'] ?? '');
        $idCursoFiltro = isset($_GET['curso']) && $_GET['curso'] !== '' ? (int) $_GET['curso'] : null;

        if ($rol === 'estudiante') {
            $tareas = Tarea::listarParaEstudiante($uid, $busqueda, $idCursoFiltro);
        } elseif ($rol === 'docente') {
            $tareas = Tarea::listarParaDocente($uid, $busqueda, $idCursoFiltro);
        } else { 
            $tareas = [];
            $cursos = Curso::listarParaUsuario($uid, 'admin');
            foreach ($cursos as $c) {
                if ($idCursoFiltro && (int) $c['id_curso'] !== $idCursoFiltro) continue;
                $tareas = array_merge($tareas, Tarea::listarPorCurso((int) $c['id_curso']));
            }
            if ($busqueda !== '') {
                $tareas = array_values(array_filter($tareas, function ($t) use ($busqueda) {
                    return stripos($t['titulo'], $busqueda) !== false;
                }));
            }
        }

        $ahora = time();
        $resultado = array_map(function ($t) use ($ahora) {
            return [
                'id_tarea'        => (int) $t['id_tarea'],
                'titulo'          => $t['titulo'],
                'curso_nombre'    => $t['curso_nombre'] ?? '',
                'instrucciones'   => $t['instrucciones'] ?? '',
                'ponderacion'     => (float) $t['ponderacion'],
                'fecha_limite'    => $t['fecha_limite'],
                'fecha_limite_fmt'=> date('d M Y', strtotime($t['fecha_limite'])),
                'vencida'         => strtotime($t['fecha_limite']) < $ahora,
                'nota'            => isset($t['nota']) && $t['nota'] !== null ? (float) $t['nota'] : null,
                'id_entrega'      => isset($t['id_entrega']) && $t['id_entrega'] !== null ? (int) $t['id_entrega'] : null,
                'estado_entrega'  => $t['estado_entrega'] ?? null,
                'total_entregas'  => isset($t['total_entregas']) ? (int) $t['total_entregas'] : 0,
            ];
        }, $tareas);

        echo json_encode([
            'ok'      => true,
            'total'   => count($resultado),
            'q'       => $busqueda,
            'tareas'  => array_values($resultado),
        ]);
        exit;
    }

    
    public function crear(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'docente') { header('Location: index.php?accion=tareas'); exit; }

        $idCurso = (int) ($_POST['id_curso'] ?? 0);
        if (!Curso::esPropietario($idCurso, $uid)) {
            flash('error', 'No puedes crear tareas en un curso que no dictas.');
        } else {
            Tarea::crear([
                'id_curso'      => $idCurso,
                'titulo'        => trim($_POST['titulo'] ?? ''),
                'instrucciones' => trim($_POST['instrucciones'] ?? ''),
                'ponderacion'   => (float) ($_POST['ponderacion'] ?? 0),
                'fecha_limite'  => $_POST['fecha_limite'] ?? date('Y-m-d H:i:s', strtotime('+7 days')),
            ]);
            flash('success', 'Tarea creada correctamente.');
        }
        header('Location: index.php?accion=tareas');
        exit;
    }

    
    public function editar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'docente') { header('Location: index.php?accion=tareas'); exit; }

        $id = (int) ($_POST['id_tarea'] ?? 0);
        $t = Tarea::obtenerPorId($id);
        if (!$t || (int) $t['docente_id'] !== $uid) {
            flash('error', 'No tienes permiso para editar esta tarea.');
        } else {
            Tarea::actualizar($id, [
                'titulo'        => trim($_POST['titulo'] ?? ''),
                'instrucciones' => trim($_POST['instrucciones'] ?? ''),
                'ponderacion'   => (float) ($_POST['ponderacion'] ?? 0),
                'fecha_limite'  => $_POST['fecha_limite'] ?? $t['fecha_limite'],
            ]);
            flash('success', 'Tarea actualizada correctamente.');
        }
        header('Location: index.php?accion=tareas');
        exit;
    }

    
    public function eliminar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'docente') { header('Location: index.php?accion=tareas'); exit; }

        $id = (int) ($_POST['id_tarea'] ?? 0);
        $t = Tarea::obtenerPorId($id);
        if (!$t || (int) $t['docente_id'] !== $uid) {
            flash('error', 'No tienes permiso para eliminar esta tarea.');
        } else {
            Tarea::eliminar($id);
            flash('success', 'Tarea eliminada correctamente.');
        }
        header('Location: index.php?accion=tareas');
        exit;
    }

    
    public function entregar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'estudiante') { header('Location: index.php?accion=tareas'); exit; }

        $idTarea = (int) ($_POST['id_tarea'] ?? 0);
        $existente = Entrega::obtenerDeEstudiante($idTarea, $uid);
        if ($existente) {
            Entrega::actualizar($existente['id_entrega'], trim($_POST['comentario'] ?? ''), trim($_POST['enlace'] ?? ''), null);
            flash('success', 'Entrega actualizada correctamente.');
        } else {
            Entrega::crear($idTarea, $uid, trim($_POST['comentario'] ?? ''), trim($_POST['enlace'] ?? ''), null);
            flash('success', 'Tarea entregada correctamente.');
        }
        header('Location: index.php?accion=tareas');
        exit;
    }

    
    public function eliminarEntrega(): void
    {
        if ($_SESSION['rol'] !== 'estudiante') { header('Location: index.php?accion=tareas'); exit; }
        Entrega::eliminar((int) ($_POST['id_entrega'] ?? 0));
        flash('info', 'Retiraste tu entrega.');
        header('Location: index.php?accion=tareas');
        exit;
    }

    
    public function calificar(): void
    {
        $idTarea = (int) ($_POST['id_tarea'] ?? 0);
        if ($_SESSION['rol'] === 'docente') {
            Entrega::calificar((int) ($_POST['id_entrega'] ?? 0), (float) ($_POST['nota'] ?? 0), trim($_POST['retro'] ?? ''));
            flash('success', 'Calificación guardada.');
        }
        header('Location: index.php?accion=tareas&ver=' . $idTarea);
        exit;
    }
}
