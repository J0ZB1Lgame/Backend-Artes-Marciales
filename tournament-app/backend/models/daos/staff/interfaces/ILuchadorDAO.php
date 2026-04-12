<?php

require_once __DIR__ . '/ICRUD.php';

interface ILuchadorDAO extends ICRUD {
    public function listarTodos();
    public function buscarPorNombre($nombre);
}

?>