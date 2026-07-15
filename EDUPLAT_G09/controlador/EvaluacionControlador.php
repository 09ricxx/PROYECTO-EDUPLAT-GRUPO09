<?php
require_once __DIR__ . '/../modelo/Evaluacion.php';
require_once __DIR__ . '/../modelo/Curso.php';

class EvaluacionControlador
{
    
    public function listar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $esDocente = $rol === 'docente';

        $busqueda = trim($_GET['q'] ?? '');
        $idCursoFiltro = isset($_GET['curso']) ? (int) $_GET['curso'] : null;

        if ($rol === 'estudiante') {
            $evaluaciones = Evaluacion::listarParaEstudiante($uid, $busqueda, $idCursoFiltro);
            $cursosFiltro = Curso::listarParaUsuario($uid, 'estudiante');
        } elseif ($esDocente) {
            $evaluaciones = Evaluacion::listarParaDocente($uid, $busqueda, $idCursoFiltro);
            $cursosFiltro = Curso::listarParaUsuario($uid, 'docente');
        } else {
            $evaluaciones = [];
            $cursosFiltro = Curso::listarParaUsuario($uid, 'admin');
            foreach ($cursosFiltro as $c) {
                $evaluaciones = array_merge($evaluaciones, Evaluacion::listarParaDocente((int) $c['docente_id'], '', (int) $c['id_curso']));
            }
        }

        $evalDetalle = null;
        $resultadosDetalle = [];
        if ($esDocente && isset($_GET['ver'])) {
            $evalDetalle = Evaluacion::obtenerPorId((int) $_GET['ver']);
            if ($evalDetalle && (int) $evalDetalle['docente_id'] === $uid) {
                $resultadosDetalle = Evaluacion::listarResultados((int) $_GET['ver']);
            } else {
                $evalDetalle = null;
            }
        }

        require __DIR__ . '/../vista/evaluaciones.php';
    }

    
    public function crear(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'docente') { header('Location: index.php?accion=evaluaciones'); exit; }

        $idCurso = (int) ($_POST['id_curso'] ?? 0);
        if (!Curso::esPropietario($idCurso, $uid)) {
            flash('error', 'No puedes crear evaluaciones en un curso que no dictas.');
        } else {
            Evaluacion::crear([
                'id_curso'     => $idCurso,
                'titulo'       => trim($_POST['titulo'] ?? ''),
                'tipo'         => in_array($_POST['tipo'] ?? '', ['examen', 'quiz'], true) ? $_POST['tipo'] : 'examen',
                'temas'        => trim($_POST['temas'] ?? ''),
                'fecha'        => $_POST['fecha'] ?? date('Y-m-d H:i:s', strtotime('+7 days')),
                'duracion_min' => (int) ($_POST['duracion_min'] ?? 60),
                'ponderacion'  => (float) ($_POST['ponderacion'] ?? 0),
                'nota_minima'  => (float) ($_POST['nota_minima'] ?? 7),
            ]);
            flash('success', 'Evaluación creada correctamente.');
        }
        header('Location: index.php?accion=evaluaciones');
        exit;
    }

    
    public function editar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'docente') { header('Location: index.php?accion=evaluaciones'); exit; }

        $id = (int) ($_POST['id_evaluacion'] ?? 0);
        $ev = Evaluacion::obtenerPorId($id);
        if (!$ev || (int) $ev['docente_id'] !== $uid) {
            flash('error', 'No tienes permiso para editar esta evaluación.');
        } else {
            Evaluacion::actualizar($id, [
                'titulo'       => trim($_POST['titulo'] ?? ''),
                'tipo'         => in_array($_POST['tipo'] ?? '', ['examen', 'quiz'], true) ? $_POST['tipo'] : 'examen',
                'temas'        => trim($_POST['temas'] ?? ''),
                'fecha'        => $_POST['fecha'] ?? $ev['fecha'],
                'duracion_min' => (int) ($_POST['duracion_min'] ?? 60),
                'ponderacion'  => (float) ($_POST['ponderacion'] ?? 0),
                'nota_minima'  => (float) ($_POST['nota_minima'] ?? 7),
            ]);
            flash('success', 'Evaluación actualizada correctamente.');
        }
        header('Location: index.php?accion=evaluaciones');
        exit;
    }

    
    public function eliminar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] !== 'docente') { header('Location: index.php?accion=evaluaciones'); exit; }

        $id = (int) ($_POST['id_evaluacion'] ?? 0);
        $ev = Evaluacion::obtenerPorId($id);
        if (!$ev || (int) $ev['docente_id'] !== $uid) {
            flash('error', 'No tienes permiso para eliminar esta evaluación.');
        } else {
            Evaluacion::eliminar($id);
            flash('success', 'Evaluación eliminada correctamente.');
        }
        header('Location: index.php?accion=evaluaciones');
        exit;
    }

    
    public function calificar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $idEval = (int) ($_POST['id_evaluacion'] ?? 0);

        if ($_SESSION['rol'] === 'docente') {
            $ev = Evaluacion::obtenerPorId($idEval);
            if ($ev && (int) $ev['docente_id'] === $uid) {
                Evaluacion::guardarResultado($idEval, (int) ($_POST['id_estudiante'] ?? 0), (float) ($_POST['nota'] ?? 0), trim($_POST['retro'] ?? ''));
                flash('success', 'Calificación guardada.');
            } else {
                flash('error', 'No tienes permiso para calificar esta evaluación.');
            }
        }
        header('Location: index.php?accion=evaluaciones&ver=' . $idEval);
        exit;
    }
}
