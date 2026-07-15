<?php
require_once __DIR__ . '/Database.php';

class Tarea
{
    
    public static function listarParaEstudiante(int $idEstudiante, string $busqueda = '', ?int $idCurso = null): array
    {
        $sql = 'SELECT t.*, c.nombre AS curso_nombre,
                       e.id_entrega, e.nota, e.estado AS estado_entrega, e.fecha_entrega
                FROM tareas t
                JOIN cursos c ON c.id_curso = t.id_curso
                JOIN inscripciones i ON i.id_curso = c.id_curso AND i.id_estudiante = :uid
                LEFT JOIN entregas e ON e.id_tarea = t.id_tarea AND e.id_estudiante = :uid2
                WHERE 1=1';
        $params = [':uid' => $idEstudiante, ':uid2' => $idEstudiante];
        if ($busqueda !== '') {
            $sql .= ' AND (t.titulo LIKE :busq OR c.nombre LIKE :busq)';
            $params[':busq'] = '%' . $busqueda . '%';
        }
        if ($idCurso) {
            $sql .= ' AND c.id_curso = :curso';
            $params[':curso'] = $idCurso;
        }
        $sql .= ' ORDER BY t.fecha_limite ASC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    
    public static function listarParaDocente(int $idDocente, string $busqueda = '', ?int $idCurso = null): array
    {
        $sql = 'SELECT t.*, c.nombre AS curso_nombre,
                       (SELECT COUNT(*) FROM entregas en WHERE en.id_tarea = t.id_tarea) AS total_entregas
                FROM tareas t
                JOIN cursos c ON c.id_curso = t.id_curso
                WHERE c.docente_id = :uid';
        $params = [':uid' => $idDocente];
        if ($busqueda !== '') {
            $sql .= ' AND (t.titulo LIKE :busq OR c.nombre LIKE :busq)';
            $params[':busq'] = '%' . $busqueda . '%';
        }
        if ($idCurso) {
            $sql .= ' AND c.id_curso = :curso';
            $params[':curso'] = $idCurso;
        }
        $sql .= ' ORDER BY t.fecha_limite ASC';
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId(int $id): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT t.*, c.nombre AS curso_nombre, c.docente_id
             FROM tareas t JOIN cursos c ON c.id_curso = t.id_curso
             WHERE t.id_tarea = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $t = $stmt->fetch();
        return $t ?: null;
    }

    public static function listarPorCurso(int $idCurso, ?int $idEstudiante = null): array
    {
        $sql = 'SELECT t.*';
        if ($idEstudiante) {
            $sql .= ', e.id_entrega, e.nota, e.estado AS estado_entrega';
        }
        $sql .= ' FROM tareas t';
        if ($idEstudiante) {
            $sql .= ' LEFT JOIN entregas e ON e.id_tarea = t.id_tarea AND e.id_estudiante = :est';
        }
        $sql .= ' WHERE t.id_curso = :c ORDER BY t.fecha_limite ASC';
        $stmt = Database::get()->prepare($sql);
        $params = [':c' => $idCurso];
        if ($idEstudiante) { $params[':est'] = $idEstudiante; }
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function crear(array $d): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO tareas (id_curso, titulo, instrucciones, ponderacion, fecha_limite)
             VALUES (:c, :t, :i, :p, :f)'
        );
        $stmt->execute([
            ':c' => $d['id_curso'], ':t' => $d['titulo'], ':i' => $d['instrucciones'],
            ':p' => $d['ponderacion'], ':f' => $d['fecha_limite'],
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function actualizar(int $id, array $d): bool
    {
        $stmt = Database::get()->prepare(
            'UPDATE tareas SET titulo = :t, instrucciones = :i, ponderacion = :p, fecha_limite = :f WHERE id_tarea = :id'
        );
        return $stmt->execute([
            ':t' => $d['titulo'], ':i' => $d['instrucciones'], ':p' => $d['ponderacion'],
            ':f' => $d['fecha_limite'], ':id' => $id,
        ]);
    }

    public static function eliminar(int $id): bool
    {
        $stmt = Database::get()->prepare('DELETE FROM tareas WHERE id_tarea = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function contarPendientes(int $idEstudiante): int
    {
        $stmt = Database::get()->prepare(
            'SELECT COUNT(*) AS n FROM tareas t
             JOIN inscripciones i ON i.id_curso = t.id_curso AND i.id_estudiante = :e
             LEFT JOIN entregas en ON en.id_tarea = t.id_tarea AND en.id_estudiante = :e2
             WHERE en.id_entrega IS NULL'
        );
        $stmt->execute([':e' => $idEstudiante, ':e2' => $idEstudiante]);
        return (int) $stmt->fetch()['n'];
    }
}
