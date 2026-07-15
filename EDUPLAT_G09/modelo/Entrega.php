<?php
require_once __DIR__ . '/Database.php';


class Entrega
{
    public static function obtenerDeEstudiante(int $idTarea, int $idEstudiante): ?array
    {
        $stmt = Database::get()->prepare(
            'SELECT * FROM entregas WHERE id_tarea = :t AND id_estudiante = :e LIMIT 1'
        );
        $stmt->execute([':t' => $idTarea, ':e' => $idEstudiante]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public static function listarPorTarea(int $idTarea): array
    {
        $stmt = Database::get()->prepare(
            'SELECT en.*, u.nombres, u.apellidos, u.correo
             FROM entregas en JOIN usuarios u ON u.id_usuario = en.id_estudiante
             WHERE en.id_tarea = :t ORDER BY en.fecha_entrega DESC'
        );
        $stmt->execute([':t' => $idTarea]);
        return $stmt->fetchAll();
    }

    public static function crear(int $idTarea, int $idEstudiante, string $comentario, string $enlace, ?string $archivo): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO entregas (id_tarea, id_estudiante, comentario, enlace, archivo_nombre, estado)
             VALUES (:t, :e, :c, :l, :a, "entregada")'
        );
        $stmt->execute([
            ':t' => $idTarea, ':e' => $idEstudiante, ':c' => $comentario, ':l' => $enlace, ':a' => $archivo,
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function actualizar(int $idEntrega, string $comentario, string $enlace, ?string $archivo): bool
    {
        $sql = 'UPDATE entregas SET comentario = :c, enlace = :l, fecha_entrega = NOW()';
        $params = [':c' => $comentario, ':l' => $enlace, ':id' => $idEntrega];
        if ($archivo !== null) {
            $sql .= ', archivo_nombre = :a';
            $params[':a'] = $archivo;
        }
        $sql .= ' WHERE id_entrega = :id';
        $stmt = Database::get()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function eliminar(int $idEntrega): bool
    {
        $stmt = Database::get()->prepare('DELETE FROM entregas WHERE id_entrega = :id');
        return $stmt->execute([':id' => $idEntrega]);
    }

    public static function calificar(int $idEntrega, float $nota, string $retro): bool
    {
        $stmt = Database::get()->prepare(
            'UPDATE entregas SET nota = :n, retroalimentacion = :r, estado = "calificada" WHERE id_entrega = :id'
        );
        return $stmt->execute([':n' => $nota, ':r' => $retro, ':id' => $idEntrega]);
    }

    public static function contarEntregadasPorEstudiante(int $idEstudiante): int
    {
        $stmt = Database::get()->prepare('SELECT COUNT(*) AS n FROM entregas WHERE id_estudiante = :e');
        $stmt->execute([':e' => $idEstudiante]);
        return (int) $stmt->fetch()['n'];
    }

    public static function promedioEstudiante(int $idEstudiante): ?float
    {
        $stmt = Database::get()->prepare('SELECT AVG(nota) AS p FROM entregas WHERE id_estudiante = :e AND nota IS NOT NULL');
        $stmt->execute([':e' => $idEstudiante]);
        $r = $stmt->fetch()['p'];
        return $r !== null ? round((float) $r, 1) : null;
    }
}
