<?php
require_once __DIR__ . '/ICRUD.php';
require_once __DIR__ . '/../Entidades/TipoRolStaff.php';

interface ITipoRolStaffDAO extends ICRUD {
    public function listarTodos();
}
?>