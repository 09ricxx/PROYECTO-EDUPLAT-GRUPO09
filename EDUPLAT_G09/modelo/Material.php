<?php
require_once __DIR__ . '/Database.php';

class Material
{
    public static function listarPorCurso(int $idCurso): array
    {
        $stmt = Database::get()->prepare('SELECT * FROM materiales WHERE id_curso = :c ORDER BY fecha_publicacion DESC');
        $stmt->execute([':c' => $idCurso]);
        return $stmt->fetchAll();
    }

    public static function crear(int $idCurso, string $titulo, string $tipo, ?string $enlace): int
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO materiales (id_curso, titulo, tipo, enlace) VALUES (:c, :t, :tp, :e)'
        );
        $stmt->execute([':c' => $idCurso, ':t' => $titulo, ':tp' => $tipo, ':e' => $enlace]);
        return (int) Database::get()->lastInsertId();
    }

    public static function eliminar(int $id): bool
    {
        $stmt = Database::get()->prepare('DELETE FROM materiales WHERE id_material = :id');
        return $stmt->execute([':id' => $id]);
    }
}
