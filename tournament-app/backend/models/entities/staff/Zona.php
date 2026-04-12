<?php

class Zona {
    private ?int $idZona;
    private string $nombre;
    private string $descripcion;

    public function __construct(
        ?int $idZona = null,
        string $nombre = '',
        string $descripcion = ''
    ) {
        $this->idZona = $idZona;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    public function getIdZona(): ?int { return $this->idZona; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): string { return $this->descripcion; }

    public function setIdZona(int $idZona): void { $this->idZona = $idZona; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }
}

?>