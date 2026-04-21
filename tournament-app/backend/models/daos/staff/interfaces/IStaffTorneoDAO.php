<?php

require_once __DIR__ . '/../../base/ICRUD.php';

interface IStaffTorneoDAO extends ICRUD {
    public function listarTodos();
}

?>