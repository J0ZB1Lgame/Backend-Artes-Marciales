<?php

require_once __DIR__ . '/ICRUD.php';

interface IUsuarioDAO extends ICRUD {
    public function obtenerPorUsername($username);
    public function listarTodos();
}

?>
