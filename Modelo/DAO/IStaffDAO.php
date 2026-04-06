<?php
require_once __DIR__ . '/ICRUD.php';
require_once __DIR__ . '/../Entidades/Staff.php';

interface IStaffDAO extends ICRUD {
    // Métodos específicos si es necesario
    public function listarTodos();
}
?>