<?php
require_once __DIR__ . '/../modelo/Curso.php';
require_once __DIR__ . '/../modelo/Material.php';
require_once __DIR__ . '/../modelo/Tarea.php';
require_once __DIR__ . '/../modelo/Evaluacion.php';

class CursoControlador
{
   
    public function listar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $esGestor = in_array($rol, ['docente', 'admin'], true);

        $busqueda = trim($_GET['q'] ?? '');
        $filtroEstado = in_array($_GET['estado'] ?? '', ['activo', 'inactivo'], true) ? $_GET['estado'] : '';

        $cursos = Curso::listarParaUsuario($uid, $rol, $busqueda, $filtroEstado);
        $disponibles = $rol === 'estudiante' ? Curso::disponiblesParaInscripcion($uid, $busqueda) : [];

        require __DIR__ . '/../vista/cursos.php';
    }

    
    public function verDetalle(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $esGestor = in_array($rol, ['docente', 'admin'], true);

        $idCurso = (int) ($_GET['id'] ?? 0);
        $curso = Curso::obtenerPorId($idCurso);

        if (!$curso) {
            flash('error', 'El curso solicitado no existe.');
            header('Location: index.php?accion=cursos');
            exit;
        }

        $esPropietario = $rol === 'admin' || ($rol === 'docente' && (int) $curso['docente_id'] === $uid);
        $inscrito = $rol === 'estudiante' && Curso::estaInscrito($idCurso, $uid);

        if ($rol === 'estudiante' && !$inscrito) {
            flash('error', 'Debes inscribirte en este curso para ver su contenido.');
            header('Location: index.php?accion=cursos');
            exit;
        }
        if ($rol === 'docente' && !$esPropietario) {
            flash('error', 'No tienes acceso a este curso.');
            header('Location: index.php?accion=cursos');
            exit;
        }

        $materiales = Material::listarPorCurso($idCurso);
        $tareas = Tarea::listarPorCurso($idCurso, $rol === 'estudiante' ? $uid : null);
        $evaluaciones = Evaluacion::listarParaEstudiante($uid, '', $idCurso);

        $progreso = $rol === 'estudiante' ? Curso::progresoEstudiante($idCurso, $uid) : null;

        $statTareasEntregadas = 0;
        $statTareasTotal = count($tareas);
        $notasParaPromedio = [];
        if ($rol === 'estudiante') {
            foreach ($tareas as $t) {
                if (!empty($t['id_entrega'])) $statTareasEntregadas++;
                if (isset($t['nota']) && $t['nota'] !== null) $notasParaPromedio[] = (float) $t['nota'];
            }
            foreach ($evaluaciones as $e) {
                if (isset($e['nota']) && $e['nota'] !== null) $notasParaPromedio[] = (float) $e['nota'];
            }
        }
        $promedioCurso = !empty($notasParaPromedio) ? round(array_sum($notasParaPromedio) / count($notasParaPromedio), 1) : null;
        $statEvaluacionesTotal = count($evaluaciones);
        $statEvaluacionesRendidas = count(array_filter($evaluaciones, fn($e) => isset($e['nota']) && $e['nota'] !== null));

        require __DIR__ . '/../vista/cursos_detalle.php';
    }

    
    public function crear(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];

        if (!in_array($rol, ['docente', 'admin'], true)) {
            flash('error', 'No tienes permisos para crear cursos.');
            header('Location: index.php?accion=cursos');
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            flash('error', 'El nombre del curso es obligatorio.');
        } else {
            Curso::crear([
                'nombre'      => $nombre,
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'docente_id'  => $uid,
                'semestre'    => trim($_POST['semestre'] ?? ''),
                'icono'       => trim($_POST['icono'] ?? '📘'),
                'estado'      => in_array($_POST['estado'] ?? '', ['activo', 'inactivo'], true) ? $_POST['estado'] : 'activo',
            ]);
            flash('success', 'Curso "' . $nombre . '" creado correctamente.');
        }
        header('Location: index.php?accion=cursos');
        exit;
    }

    
    public function editar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $id = (int) ($_POST['id_curso'] ?? 0);

        if (!in_array($rol, ['docente', 'admin'], true) || ($rol === 'docente' && !Curso::esPropietario($id, $uid))) {
            flash('error', 'No puedes editar un curso que no te pertenece.');
        } else {
            Curso::actualizar($id, [
                'nombre'      => trim($_POST['nombre'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'semestre'    => trim($_POST['semestre'] ?? ''),
                'icono'       => trim($_POST['icono'] ?? '📘'),
                'estado'      => in_array($_POST['estado'] ?? '', ['activo', 'inactivo'], true) ? $_POST['estado'] : 'activo',
            ]);
            flash('success', 'Curso actualizado correctamente.');
        }
        header('Location: index.php?accion=cursos');
        exit;
    }

   
    public function eliminar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $id = (int) ($_POST['id_curso'] ?? 0);

        if (!in_array($rol, ['docente', 'admin'], true) || ($rol === 'docente' && !Curso::esPropietario($id, $uid))) {
            flash('error', 'No puedes eliminar un curso que no te pertenece.');
        } else {
            Curso::eliminar($id);
            flash('success', 'Curso eliminado correctamente.');
        }
        header('Location: index.php?accion=cursos');
        exit;
    }

    
    public function inscribir(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] === 'estudiante') {
            Curso::inscribir((int) ($_POST['id_curso'] ?? 0), $uid);
            flash('success', 'Te inscribiste correctamente en el curso.');
        }
        header('Location: index.php?accion=cursos');
        exit;
    }

    
    public function retirar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        if ($_SESSION['rol'] === 'estudiante') {
            Curso::retirar((int) ($_POST['id_curso'] ?? 0), $uid);
            flash('info', 'Te retiraste del curso.');
        }
        header('Location: index.php?accion=cursos');
        exit;
    }

    
    public function crearMaterial(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $idCurso = (int) ($_POST['id_curso'] ?? 0);
        $esPropietario = $rol === 'admin' || ($rol === 'docente' && Curso::esPropietario($idCurso, $uid));

        if ($esPropietario) {
            Material::crear($idCurso, trim($_POST['titulo'] ?? ''), $_POST['tipo'] ?? 'documento', trim($_POST['enlace'] ?? '') ?: null);
            flash('success', 'Material publicado correctamente.');
        } else {
            flash('error', 'No tienes permisos para publicar material en este curso.');
        }
        header('Location: index.php?accion=curso&id=' . $idCurso . '&tab=contenidos');
        exit;
    }

    
    public function eliminarMaterial(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];
        $idCurso = (int) ($_POST['id_curso'] ?? 0);
        $esPropietario = $rol === 'admin' || ($rol === 'docente' && Curso::esPropietario($idCurso, $uid));

        if ($esPropietario) {
            Material::eliminar((int) ($_POST['id_material'] ?? 0));
            flash('success', 'Material eliminado.');
        } else {
            flash('error', 'No tienes permisos para eliminar este material.');
        }
        header('Location: index.php?accion=curso&id=' . $idCurso . '&tab=contenidos');
        exit;
    }
}
