<?php

require_once __DIR__ . '/../../base/ICRUD.php';

interface IStaffDAO extends ICRUD {
    public function listarTodos();
}

?>
