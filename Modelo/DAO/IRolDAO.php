<?php
require_once __DIR__ . '/ICRUD.php';
require_once __DIR__ . '/../Entidades/Rol.php';

interface IRolDAO extends ICRUD {
    public function listarTodos();
    public function asignarAUsuario($id_usuario, $id_rol);
    public function revocarDeUsuario($id_usuario, $id_rol);
    public function listarPorUsuario($id_usuario);
}
?>