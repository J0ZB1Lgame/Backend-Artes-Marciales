<?php
require_once __DIR__ . '/ICRUD.php';
require_once __DIR__ . '/../Entidades/Usuario.php';

interface IUsuarioDAO extends ICRUD {
    // Métodos específicos si es necesario
    public function obtenerPorUsername($username);
}
?>