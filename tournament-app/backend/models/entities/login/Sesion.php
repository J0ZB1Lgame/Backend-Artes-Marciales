<?php

class Sesion {
    private ?int $idSesion;
    private string $fechaInicio;
    private bool $estado;
    private ?Usuario $usuarioActivo;

    public function __construct(?int $idSesion = null, string $fechaInicio = '', bool $estado = true, ?Usuario $usuarioActivo = null) {
        $this->idSesion = $idSesion;
        $this->fechaInicio = $fechaInicio;
        $this->estado = $estado;
        $this->usuarioActivo = $usuarioActivo;
    }

    public function getIdSesion(): ?int {
        return $this->idSesion;
    }

    public function setIdSesion(?int $idSesion): void {
        $this->idSesion = $idSesion;
    }

    public function getFechaInicio(): string {
        return $this->fechaInicio;
    }

    public function setFechaInicio(string $fechaInicio): void {
        $this->fechaInicio = $fechaInicio;
    }

    public function getEstado(): bool {
        return $this->estado;
    }

    public function setEstado(bool $estado): void {
        $this->estado = $estado;
    }

    public function getUsuarioActivo(): ?Usuario {
        return $this->usuarioActivo;
    }

    public function setUsuarioActivo(?Usuario $usuarioActivo): void {
        $this->usuarioActivo = $usuarioActivo;
    }
}