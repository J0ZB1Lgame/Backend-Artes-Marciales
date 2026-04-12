<?php

require_once __DIR__ . '/ICRUD.php';

interface IUsuarioDAO extends ICRUD {
    public function obtenerPorUsername(string $username);
    public function listarTodos(): array;
}

?>