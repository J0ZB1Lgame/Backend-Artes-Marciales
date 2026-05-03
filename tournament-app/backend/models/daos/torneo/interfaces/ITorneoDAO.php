<?php

require_once __DIR__ . '/../../base/ICRUD.php';

public interface ITorneoDAO extends ICRUD {
    public function obtenerTodos();
    public function eliminar($id);
}