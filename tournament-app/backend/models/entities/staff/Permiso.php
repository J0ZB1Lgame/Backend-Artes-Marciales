<?php

class Permiso {
    private ?int $idPermiso;
    private string $nombre;

    public function __construct(?int $idPermiso = null, string $nombre = '') {
        $this->idPermiso = $idPermiso;
        $this->nombre = $nombre;
    }

    public function getIdPermiso(): ?int {
        return $this->idPermiso;
    }

    public function setIdPermiso(int $idPermiso): void {
        $this->idPermiso = $idPermiso;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
}

?>