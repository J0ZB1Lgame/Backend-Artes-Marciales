<?php

class StaffTorneo {
    private ?int $idStaffTorneo;
    private string $nombre;
    private ?int $idRol;
    private ?int $idZona;
    private ?Rol $rol;
    private ?Zona $zona;

    public function __construct(
        ?int $idStaffTorneo = null, 
        string $nombre = '', 
        ?int $idRol = null, 
        ?int $idZona = null
    ) {
        $this->idStaffTorneo = $idStaffTorneo;
        $this->nombre = $nombre;
        $this->idRol = $idRol;
        $this->idZona = $idZona;
        $this->rol = null;
        $this->zona = null;
    }

    // Getters
    public function getIdStaffTorneo(): ?int {
        return $this->idStaffTorneo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getIdRol(): ?int {
        return $this->idRol;
    }

    public function getIdZona(): ?int {
        return $this->idZona;
    }

    public function getRol(): ?Rol {
        return $this->rol;
    }

    public function getZona(): ?Zona {
        return $this->zona;
    }

    // Setters
    public function setIdStaffTorneo(int $idStaffTorneo): void {
        $this->idStaffTorneo = $idStaffTorneo;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function setIdRol(?int $idRol): void {
        $this->idRol = $idRol;
    }

    public function setIdZona(?int $idZona): void {
        $this->idZona = $idZona;
    }

    public function setRol(?Rol $rol): void {
        $this->rol = $rol;
    }

    public function setZona(?Zona $zona): void {
        $this->zona = $zona;
    }

    // Métodos adicionales
    public function getNombreRol(): ?string {
        return $this->rol ? $this->rol->getNombre() : null;
    }

    public function getNombreZona(): ?string {
        return $this->zona ? $this->zona->getNombre() : null;
    }

    public function toArray(): array {
        return [
            'id_staff_torneo' => $this->idStaffTorneo,
            'nombre' => $this->nombre,
            'id_rol' => $this->idRol,
            'id_zona' => $this->idZona,
            'rol' => $this->rol ? $this->rol->toArray() : null,
            'zona' => $this->zona ? $this->zona->toArray() : null
        ];
    }
}

?>
