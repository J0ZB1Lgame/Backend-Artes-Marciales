<?php

class Rol {
    private ?int $idRol;
    private string $nombre;
    private string $descripcion;

    public function __construct(?int $idRol = null, string $nombre = '', string $descripcion = '') {
        $this->idRol = $idRol;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    public function getIdRol(): ?int { return $this->idRol; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): string { return $this->descripcion; }

    public function setIdRol(int $idRol): void { $this->idRol = $idRol; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }

    public function toArray(): array {
        return [
            'id_rol' => $this->idRol,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion
        ];
    }
}

?>
