<?php
require_once 'ICRUD.php';
require_once '../Entidades/Usuario.php';

interface IUsuarioDAO extends ICRUD {
    // Métodos específicos si es necesario
    public function obtenerPorUsername($username);
}
?>