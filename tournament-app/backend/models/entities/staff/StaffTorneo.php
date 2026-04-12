<?php

class StaffTorneo {
    private ?int $idStaffTorneo;
    private string $nombre;

    public function __construct(?int $idStaffTorneo = null, string $nombre = '') {
        $this->idStaffTorneo = $idStaffTorneo;
        $this->nombre = $nombre;
    }

    public function getIdStaffTorneo(): ?int {
        return $this->idStaffTorneo;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setIdStaffTorneo(int $idStaffTorneo): void {
        $this->idStaffTorneo = $idStaffTorneo;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
}

?>
