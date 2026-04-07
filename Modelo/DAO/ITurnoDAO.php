<?php
require_once __DIR__ . '/ICRUD.php';
require_once __DIR__ . '/../Entidades/Turno.php';

interface ITurnoDAO extends ICRUD {
    public function listarTodos();
}
?>