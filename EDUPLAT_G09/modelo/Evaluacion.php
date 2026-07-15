<?php
require_once __DIR__ . '/Database.php';

class Evaluacion
{
    public static function listarParaEstudiante(int $idEstudiante, string $busqueda = '', ?int $idCurso = null): array
    {
        $sql = 'SELECT ev.*, c.nombre AS curso_nombre,
                       r.nota, r.retroalimentacion, r.id_resultado
                FROM evaluaciones ev
                JOIN cursos c ON c.id_curso = ev.id_curso
                JOIN inscripciones i ON i.id_curso = c.id_curso AND i.id_estudiante = :uid
                LEFT JOIN resultados_evaluacion r ON r.id_evaluacion = ev.id_evaluacion AND r.id_estudiante = :uid2
                WHERE 1=1';
        $params = [':uid' => $idEstudiante, ':uid2' => $idEstudiante];
        if ($busqueda !== '') {
            $sql .= ' AND (ev.titulo LIKE :busq OR c.nombre LIKE :busq)';
            $params[':busq'] = '%' . $busqueda . '%';
        }
        if ($idCurso) { $sql .= ' AND c.id_curso = :curso'; $params[':curso'] = $idCurso; }
        $sql .= ' ORDER BY ev.fecha ASC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function listarParaDocente(int $idDocente, string $busqueda = '', ?int $idCurso = null): array
    {
        $sql = 'SELECT ev.*, c.nombre AS curso_nombre,
                       (SELECT COUNT(*) FROM resultados_evaluacion r WHERE r.id_evaluacion = ev.id_evaluacion) AS total_calificados
                FROM evaluaciones ev
                JOIN cursos c ON c.id_curso = ev.id_curso
                WHERE c.docente_id = :uid';
        $params = [':uid' => $idDocente];
        if ($busqueda !== '') {
            $sql .= ' AND (ev.titulo LIKE :busq OR c.nombre LIKE :busq)';
            $params[':busq'] = '%' . $busqueda . '%';
        }
        if ($idCurso) { $sql .= ' AND c.id_curso = :curso'; $params[':curso'] = $idCurso; }
        $sql .= ' ORDER BY ev.fecha ASC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId(int $id): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT ev.*, c.nombre AS curso_nombre, c.docente_id
             FROM evaluaciones ev JOIN cursos c ON c.id_curso = ev.id_curso
             WHERE ev.id_evaluacion = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public static function crear(array $d): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO evaluaciones (id_curso, titulo, tipo, temas, fecha, duracion_min, ponderacion, nota_minima)
             VALUES (:c, :t, :tp, :tm, :f, :d, :p, :nm)'
        );
        $stmt->execute([
            ':c' => $d['id_curso'], ':t' => $d['titulo'], ':tp' => $d['tipo'], ':tm' => $d['temas'],
            ':f' => $d['fecha'], ':d' => $d['duracion_min'], ':p' => $d['ponderacion'], ':nm' => $d['nota_minima'],
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function actualizar(int $id, array $d): bool
    {
        $stmt = Database::get()->prepare(
            'UPDATE evaluaciones SET titulo = :t, tipo = :tp, temas = :tm, fecha = :f,
                    duracion_min = :d, ponderacion = :p, nota_minima = :nm WHERE id_evaluacion = :id'
        );
        return $stmt->execute([
            ':t' => $d['titulo'], ':tp' => $d['tipo'], ':tm' => $d['temas'], ':f' => $d['fecha'],
            ':d' => $d['duracion_min'], ':p' => $d['ponderacion'], ':nm' => $d['nota_minima'], ':id' => $id,
        ]);
    }

    public static function eliminar(int $id): bool
    {
        $stmt = Database::get()->prepare('DELETE FROM evaluaciones WHERE id_evaluacion = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function listarResultados(int $idEvaluacion): array
    {
        $stmt = Database::get()->prepare(
            'SELECT i.id_estudiante, u.nombres, u.apellidos, u.correo, r.nota, r.retroalimentacion, r.id_resultado
             FROM inscripciones i
             JOIN usuarios u ON u.id_usuario = i.id_estudiante
             JOIN evaluaciones ev ON ev.id_curso = i.id_curso
             LEFT JOIN resultados_evaluacion r ON r.id_evaluacion = ev.id_evaluacion AND r.id_estudiante = i.id_estudiante
             WHERE ev.id_evaluacion = :id
             ORDER BY u.apellidos ASC'
        );
        $stmt->execute([':id' => $idEvaluacion]);
        return $stmt->fetchAll();
    }

    public static function guardarResultado(int $idEvaluacion, int $idEstudiante, float $nota, string $retro): bool
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO resultados_evaluacion (id_evaluacion, id_estudiante, nota, retroalimentacion)
             VALUES (:ev, :es, :n, :r)
             ON DUPLICATE KEY UPDATE nota = :n2, retroalimentacion = :r2'
        );
        return $stmt->execute([
            ':ev' => $idEvaluacion, ':es' => $idEstudiante, ':n' => $nota, ':r' => $retro,
            ':n2' => $nota, ':r2' => $retro,
        ]);
    }

    public static function promedioEstudiante(int $idEstudiante): ?float
    {
        $stmt = Database::get()->prepare('SELECT AVG(nota) AS p FROM resultados_evaluacion WHERE id_estudiante = :e AND nota IS NOT NULL');
        $stmt->execute([':e' => $idEstudiante]);
        $r = $stmt->fetch()['p'];
        return $r !== null ? round((float) $r, 1) : null;
    }
}
