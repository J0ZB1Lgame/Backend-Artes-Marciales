<?php

class Log {
    private ?int $idLog;
    private string $accion;
    private string $fecha;
    private ?int $idUsuario;

    public function __construct(?int $idLog, string $accion, string $fecha, ?int $idUsuario) {
        $this->idLog = $idLog;
        $this->accion = $accion;
        $this->fecha = $fecha;
        $this->idUsuario = $idUsuario;
    }

    public function getIdLog(): ?int {
        return $this->idLog;
    }

    public function setIdLog(?int $idLog): void {
        $this->idLog = $idLog;
    }

    public function getAccion(): string {
        return $this->accion;
    }

    public function setAccion(string $accion): void {
        $this->accion = $accion;
    }

    public function getFecha(): string {
        return $this->fecha;
    }

    public function setFecha(string $fecha): void {
        $this->fecha = $fecha;
    }

    public function getIdUsuario(): ?int {
        return $this->idUsuario;
    }

    public function setIdUsuario(?int $idUsuario): void {
        $this->idUsuario = $idUsuario;
    }
}