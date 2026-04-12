<?php

require_once __DIR__ . '/ICRUD.php';

interface IAsignacionTurnoDAO extends ICRUD {
    public function listarPorStaff($id_staff);
    public function listarTodas();
    public function eliminarPorStaff($id_staff);
}

?>
