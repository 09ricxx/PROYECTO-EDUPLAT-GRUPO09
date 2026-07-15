<?php
require_once __DIR__ . '/Database.php';

class Curso
{
    
    public static function listarParaUsuario(int $idUsuario, string $rol, string $busqueda = '', string $filtroEstado = ''): array
    {
        $pdo = Database::get();
        $params = [];
        $where = [];

        if ($rol === 'estudiante') {
            $sql = 'SELECT c.*, u.nombres AS docente_nombres, u.apellidos AS docente_apellidos,
                           i.progreso, i.id_inscripcion,
                           (SELECT COUNT(*) FROM inscripciones i2 WHERE i2.id_curso = c.id_curso) AS total_alumnos
                    FROM cursos c
                    JOIN usuarios u ON u.id_usuario = c.docente_id
                    LEFT JOIN inscripciones i ON i.id_curso = c.id_curso AND i.id_estudiante = :uid
                    WHERE 1=1';
            $params[':uid'] = $idUsuario;
        } elseif ($rol === 'docente') {
            $sql = 'SELECT c.*, u.nombres AS docente_nombres, u.apellidos AS docente_apellidos,
                           (SELECT COUNT(*) FROM inscripciones i2 WHERE i2.id_curso = c.id_curso) AS total_alumnos
                    FROM cursos c
                    JOIN usuarios u ON u.id_usuario = c.docente_id
                    WHERE c.docente_id = :uid';
            $params[':uid'] = $idUsuario;
        } else { 
            $sql = 'SELECT c.*, u.nombres AS docente_nombres, u.apellidos AS docente_apellidos,
                           (SELECT COUNT(*) FROM inscripciones i2 WHERE i2.id_curso = c.id_curso) AS total_alumnos
                    FROM cursos c
                    JOIN usuarios u ON u.id_usuario = c.docente_id
                    WHERE 1=1';
        }

        if ($busqueda !== '') {
            $sql .= ' AND (c.nombre LIKE :busq OR c.descripcion LIKE :busq OR u.nombres LIKE :busq OR u.apellidos LIKE :busq)';
            $params[':busq'] = '%' . $busqueda . '%';
        }
        if ($filtroEstado !== '') {
            $sql .= ' AND c.estado = :estado';
            $params[':estado'] = $filtroEstado;
        }

        $sql .= ' ORDER BY c.fecha_creacion DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    
    public static function disponiblesParaInscripcion(int $idEstudiante, string $busqueda = ''): array
    {
        $pdo = Database::get();
        $sql = 'SELECT c.*, u.nombres AS docente_nombres, u.apellidos AS docente_apellidos
                FROM cursos c
                JOIN usuarios u ON u.id_usuario = c.docente_id
                WHERE c.estado = "activo"
                  AND c.id_curso NOT IN (SELECT id_curso FROM inscripciones WHERE id_estudiante = :uid)';
        $params = [':uid' => $idEstudiante];
        if ($busqueda !== '') {
            $sql .= ' AND (c.nombre LIKE :busq OR c.descripcion LIKE :busq)';
            $params[':busq'] = '%' . $busqueda . '%';
        }
        $sql .= ' ORDER BY c.nombre ASC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function obtenerPorId(int $id): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT c.*, u.nombres AS docente_nombres, u.apellidos AS docente_apellidos
             FROM cursos c JOIN usuarios u ON u.id_usuario = c.docente_id
             WHERE c.id_curso = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $c = $stmt->fetch();
        return $c ?: null;
    }

    public static function crear(array $datos): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO cursos (nombre, descripcion, docente_id, semestre, icono, estado)
             VALUES (:nombre, :descripcion, :docente_id, :semestre, :icono, :estado)'
        );
        $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':docente_id'  => $datos['docente_id'],
            ':semestre'    => $datos['semestre'],
            ':icono'       => $datos['icono'] ?: '📘',
            ':estado'      => $datos['estado'],
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function actualizar(int $id, array $datos): bool
    {
        $stmt = Database::get()->prepare(
            'UPDATE cursos SET nombre = :nombre, descripcion = :descripcion, semestre = :semestre,
                    icono = :icono, estado = :estado WHERE id_curso = :id'
        );
        return $stmt->execute([
            ':nombre'      => $datos['nombre'],
            ':descripcion' => $datos['descripcion'],
            ':semestre'    => $datos['semestre'],
            ':icono'       => $datos['icono'] ?: '📘',
            ':estado'      => $datos['estado'],
            ':id'          => $id,
        ]);
    }

    public static function eliminar(int $id): bool
    {
        $stmt = Database::get()->prepare('DELETE FROM cursos WHERE id_curso = :id');
        return $stmt->execute([':id' => $id]);
    }

    public static function esPropietario(int $idCurso, int $idDocente): bool
    {
        $stmt = Database::get()->prepare('SELECT 1 FROM cursos WHERE id_curso = :c AND docente_id = :d');
        $stmt->execute([':c' => $idCurso, ':d' => $idDocente]);
        return (bool) $stmt->fetch();
    }

    public static function inscribir(int $idCurso, int $idEstudiante): bool
    {
        $stmt = Database::get()->prepare(
            'INSERT IGNORE INTO inscripciones (id_curso, id_estudiante, progreso) VALUES (:c, :e, 0)'
        );
        return $stmt->execute([':c' => $idCurso, ':e' => $idEstudiante]);
    }

    public static function retirar(int $idCurso, int $idEstudiante): bool
    {
        $stmt = Database::get()->prepare('DELETE FROM inscripciones WHERE id_curso = :c AND id_estudiante = :e');
        return $stmt->execute([':c' => $idCurso, ':e' => $idEstudiante]);
    }

    public static function estaInscrito(int $idCurso, int $idEstudiante): bool
    {
        $stmt = Database::get()->prepare('SELECT 1 FROM inscripciones WHERE id_curso = :c AND id_estudiante = :e');
        $stmt->execute([':c' => $idCurso, ':e' => $idEstudiante]);
        return (bool) $stmt->fetch();
    }

    public static function progresoEstudiante(int $idCurso, int $idEstudiante): int
    {
        $stmt = Database::get()->prepare('SELECT progreso FROM inscripciones WHERE id_curso = :c AND id_estudiante = :e');
        $stmt->execute([':c' => $idCurso, ':e' => $idEstudiante]);
        $r = $stmt->fetch();
        return $r ? (int) $r['progreso'] : 0;
    }

    public static function contarPorDocente(int $idDocente, ?string $estado = 'activo'): int
    {
        $sql = 'SELECT COUNT(*) AS n FROM cursos WHERE docente_id = :d';
        $params = [':d' => $idDocente];
        if ($estado) { $sql .= ' AND estado = :e'; $params[':e'] = $estado; }
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['n'];
    }

    public static function contarInscritos(int $idEstudiante, ?string $estado = 'activo'): int
    {
        $sql = 'SELECT COUNT(*) AS n FROM inscripciones i JOIN cursos c ON c.id_curso = i.id_curso WHERE i.id_estudiante = :e';
        $params = [':e' => $idEstudiante];
        if ($estado) { $sql .= ' AND c.estado = :s'; $params[':s'] = $estado; }
        $stmt = Database::get()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetch()['n'];
    }
}
