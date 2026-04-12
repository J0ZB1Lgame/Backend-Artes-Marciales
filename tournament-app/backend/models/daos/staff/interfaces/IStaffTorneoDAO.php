<?php

require_once __DIR__ . '/ICRUD.php';

interface IStaffTorneoDAO extends ICRUD {
    public function listarTodos();
}

?>