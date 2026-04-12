<?php

require_once __DIR__ . '/ICRUD.php';

interface ILogDAO extends ICRUD {
    public function crearEvento($evento): void;
    public function consultarHistorial(): array;
}

?>