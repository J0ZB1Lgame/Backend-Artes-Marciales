<?php

require_once __DIR__ . '/../../base/ICRUD.php';

interface IPermisoDAO extends ICRUD {
    public function obtenerTodos();
    public function eliminar($id);
}

?>