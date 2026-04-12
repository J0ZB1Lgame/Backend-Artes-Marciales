<?php

require_once __DIR__ . '/ICRUD.php';

interface IZonaDAO extends ICRUD {
    public function listarTodos();
}

?>