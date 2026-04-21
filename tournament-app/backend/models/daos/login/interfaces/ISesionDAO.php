<?php

require_once __DIR__ . '/../../base/ICRUD.php';

interface ISesionDAO extends ICRUD {
    public function crearSesion($sesion): void;
    public function buscarSesion(int $id);
    public function cerrarSesion(int $id): void;
}

?>