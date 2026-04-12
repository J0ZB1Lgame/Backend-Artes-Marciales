<?php

require_once __DIR__ . '/ICRUD.php';

interface IStaffDAO extends ICRUD {
    public function listarTodos();
}

?>
