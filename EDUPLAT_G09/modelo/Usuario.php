<?php
require_once __DIR__ . '/Database.php';

class Usuario
{
    public static function buscarPorCorreo(string $correo): ?array
    {
        $stmt = Database::get()->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute([':correo' => $correo]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public static function buscarPorId(int $id): ?array
    {
        $stmt = Database::get()->prepare('SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public static function crear(string $nombres, string $apellidos, string $correo, string $password, string $rol = 'estudiante'): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = Database::get()->prepare(
            'INSERT INTO usuarios (nombres, apellidos, correo, password_hash, rol)
             VALUES (:nombres, :apellidos, :correo, :hash, :rol)'
        );
        $stmt->execute([
            ':nombres'   => $nombres,
            ':apellidos' => $apellidos,
            ':correo'    => $correo,
            ':hash'      => $hash,
            ':rol'       => $rol,
        ]);
        return (int) Database::get()->lastInsertId();
    }

    public static function actualizarPerfil(int $id, string $nombres, string $apellidos, string $correo): bool
    {
        $stmt = Database::get()->prepare(
            'UPDATE usuarios SET nombres = :nombres, apellidos = :apellidos, correo = :correo WHERE id_usuario = :id'
        );
        return $stmt->execute([
            ':nombres'   => $nombres,
            ':apellidos' => $apellidos,
            ':correo'    => $correo,
            ':id'        => $id,
        ]);
    }

    public static function actualizarPassword(int $id, string $passwordNueva): bool
    {
        $hash = password_hash($passwordNueva, PASSWORD_BCRYPT);
        $stmt = Database::get()->prepare('UPDATE usuarios SET password_hash = :hash WHERE id_usuario = :id');
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public static function verificarPassword(int $id, string $password): bool
    {
        $u = self::buscarPorId($id);
        return $u && password_verify($password, $u['password_hash']);
    }

    public static function registrarLogin(?int $idUsuario, string $correo, bool $exitoso): void
    {
        $stmt = Database::get()->prepare(
            'INSERT INTO sesiones_log (id_usuario, correo_usado, exitoso, ip_origen)
             VALUES (:id_usuario, :correo, :exitoso, :ip)'
        );
        $stmt->execute([
            ':id_usuario' => $idUsuario,
            ':correo'     => $correo,
            ':exitoso'    => $exitoso ? 1 : 0,
            ':ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);
    }
}
