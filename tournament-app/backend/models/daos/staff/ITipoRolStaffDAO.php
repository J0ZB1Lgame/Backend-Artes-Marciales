<?php

require_once __DIR__ . '/ICRUD.php';

interface ITipoRolStaffDAO extends ICRUD {
    public function listarTodos();
}

?>
