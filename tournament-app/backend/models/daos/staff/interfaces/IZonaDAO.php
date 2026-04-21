<?php

require_once __DIR__ . '/../../base/ICRUD.php';

interface IZonaDAO extends ICRUD {
    public function listarTodos();
}

?>