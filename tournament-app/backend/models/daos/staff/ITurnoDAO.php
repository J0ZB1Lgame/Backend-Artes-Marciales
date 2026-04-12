<?php

require_once __DIR__ . '/ICRUD.php';

interface ITurnoDAO extends ICRUD {
    public function listarTodos();
}

?>
