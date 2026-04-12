<?php

class Luchador {
    private ?int $idLuchador;
    private string $nombre;
    private string $especie;
    private float $nivelDePoderKi;
    private string $origen;
    private bool $estado;

    public function __construct(
        ?int $idLuchador = null,
        string $nombre = '',
        string $especie = '',
        float $nivelDePoderKi = 0.0,
        string $origen = '',
        bool $estado = true
    ) {
        $this->idLuchador = $idLuchador;
        $this->nombre = $nombre;
        $this->especie = $especie;
        $this->nivelDePoderKi = $nivelDePoderKi;
        $this->origen = $origen;
        $this->estado = $estado;
    }

    public function getIdLuchador(): ?int { return $this->idLuchador; }
    public function getNombre(): string { return $this->nombre; }
    public function getEspecie(): string { return $this->especie; }
    public function getNivelDePoderKi(): float { return $this->nivelDePoderKi; }
    public function getOrigen(): string { return $this->origen; }
    public function getEstado(): bool { return $this->estado; }

    public function setIdLuchador(int $idLuchador): void { $this->idLuchador = $idLuchador; }
    public function setNombre(string $nombre): void { $this->nombre = $nombre; }
    public function setEspecie(string $especie): void { $this->especie = $especie; }
    public function setNivelDePoderKi(float $nivelDePoderKi): void { $this->nivelDePoderKi = $nivelDePoderKi; }
    public function setOrigen(string $origen): void { $this->origen = $origen; }
    public function setEstado(bool $estado): void { $this->estado = $estado; }
}

?>