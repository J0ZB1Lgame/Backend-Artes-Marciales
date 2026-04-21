<?php

require_once __DIR__ . '/../../base/ICRUD.php';

interface ILuchadorDAO extends ICRUD {
    public function listarTodos();
    public function buscarPorNombre($nombre);
}

?>