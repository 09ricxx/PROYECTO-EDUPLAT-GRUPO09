<?php
require_once __DIR__ . '/../modelo/Curso.php';
require_once __DIR__ . '/../modelo/Tarea.php';
require_once __DIR__ . '/../modelo/Entrega.php';
require_once __DIR__ . '/../modelo/Evaluacion.php';
require_once __DIR__ . '/../modelo/Database.php';

class DashboardControlador
{
    public function mostrar(): void
    {
        $uid = (int) $_SESSION['id_usuario'];
        $rol = $_SESSION['rol'];

        if ($rol === 'estudiante') {
            $cursosActivos    = Curso::contarInscritos($uid, 'activo');
            $tareasPendientes = Tarea::contarPendientes($uid);
            $tareasEntregadas = Entrega::contarEntregadasPorEstudiante($uid);
            $promTareas = Entrega::promedioEstudiante($uid);
            $promEval   = Evaluacion::promedioEstudiante($uid);
            $promedio = null;
            if ($promTareas !== null && $promEval !== null) $promedio = round(($promTareas + $promEval) / 2, 1);
            elseif ($promTareas !== null) $promedio = $promTareas;
            elseif ($promEval !== null) $promedio = $promEval;

            $misCursos = array_slice(Curso::listarParaUsuario($uid, 'estudiante'), 0, 3);

            $stmt = Database::get()->prepare(
                'SELECT t.titulo, c.nombre AS curso_nombre, t.fecha_limite
                 FROM tareas t
                 JOIN cursos c ON c.id_curso = t.id_curso
                 JOIN inscripciones i ON i.id_curso = c.id_curso AND i.id_estudiante = :e
                 LEFT JOIN entregas en ON en.id_tarea = t.id_tarea AND en.id_estudiante = :e2
                 WHERE en.id_entrega IS NULL AND t.fecha_limite >= NOW()
                 ORDER BY t.fecha_limite ASC LIMIT 4'
            );
            $stmt->execute([':e' => $uid, ':e2' => $uid]);
            $proximasEntregas = $stmt->fetchAll();
        } elseif ($rol === 'docente') {
            $cursosActivos = Curso::contarPorDocente($uid, 'activo');
            $stmt = Database::get()->prepare('SELECT COUNT(*) AS n FROM tareas t JOIN cursos c ON c.id_curso=t.id_curso WHERE c.docente_id=:d');
            $stmt->execute([':d' => $uid]); $totalTareas = (int) $stmt->fetch()['n'];
            $stmt = Database::get()->prepare('SELECT COUNT(*) AS n FROM entregas en JOIN tareas t ON t.id_tarea=en.id_tarea JOIN cursos c ON c.id_curso=t.id_curso WHERE c.docente_id=:d AND en.estado="entregada"');
            $stmt->execute([':d' => $uid]); $porCalificar = (int) $stmt->fetch()['n'];
            $stmt = Database::get()->prepare('SELECT COUNT(DISTINCT i.id_estudiante) AS n FROM inscripciones i JOIN cursos c ON c.id_curso=i.id_curso WHERE c.docente_id=:d');
            $stmt->execute([':d' => $uid]); $totalAlumnos = (int) $stmt->fetch()['n'];

            $misCursos = array_slice(Curso::listarParaUsuario($uid, 'docente'), 0, 3);
            $proximasEntregas = [];
            $tareasPendientes = $totalTareas;
            $tareasEntregadas = $porCalificar;
            $promedio = $totalAlumnos;
        } else {
            $stmt = Database::get()->query('SELECT COUNT(*) AS n FROM cursos');
            $cursosActivos = (int) $stmt->fetch()['n'];
            $stmt = Database::get()->query('SELECT COUNT(*) AS n FROM usuarios WHERE rol = "estudiante"');
            $tareasPendientes = (int) $stmt->fetch()['n'];
            $stmt = Database::get()->query('SELECT COUNT(*) AS n FROM usuarios WHERE rol = "docente"');
            $tareasEntregadas = (int) $stmt->fetch()['n'];
            $stmt = Database::get()->query('SELECT COUNT(*) AS n FROM inscripciones');
            $promedio = (int) $stmt->fetch()['n'];
            $misCursos = array_slice(Curso::listarParaUsuario($uid, 'admin'), 0, 3);
            $proximasEntregas = [];
        }

        require __DIR__ . '/../vista/dashboard.php';
    }
}
