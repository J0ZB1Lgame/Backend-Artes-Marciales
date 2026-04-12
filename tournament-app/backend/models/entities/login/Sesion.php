<?php

class Sesion {
    private ?int $idSesion;
    private string $fechaInicio;
    private string $fechaFin;
    private ?Usuario $usuarioActivo;

    public function __construct(?int $idSesion, string $fechaInicio, string $fechaFin, ?Usuario $usuarioActivo) {
        $this->idSesion = $idSesion;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
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

    public function getFechaFin(): string {
        return $this->fechaFin;
    }

    public function setFechaFin(string $fechaFin): void {
        $this->fechaFin = $fechaFin;
    }

    public function getUsuarioActivo(): ?Usuario {
        return $this->usuarioActivo;
    }

    public function setUsuarioActivo(?Usuario $usuarioActivo): void {
        $this->usuarioActivo = $usuarioActivo;
    }
}