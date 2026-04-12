<?php

class Usuario {
    private ?int $idUsuario;
    private string $username;
    private string $password;
    private string $rol;
    private bool $estado;

    public function __construct(
        ?int $idUsuario = null,
        string $username = '',
        string $password = '',
        string $rol = '',
        bool $estado = true
    ) {
        $this->idUsuario = $idUsuario;
        $this->username = $username;
        $this->password = $password;
        $this->rol = $rol;
        $this->estado = $estado;
    }

    public function getIdUsuario(): ?int { return $this->idUsuario; }
    public function getUsername(): string { return $this->username; }
    public function getPassword(): string { return $this->password; }
    public function getRol(): string { return $this->rol; }
    public function getEstado(): bool { return $this->estado; }

    public function setIdUsuario(int $idUsuario): void { $this->idUsuario = $idUsuario; }
    public function setUsername(string $username): void { $this->username = $username; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setRol(string $rol): void { $this->rol = $rol; }
    public function setEstado(bool $estado): void { $this->estado = $estado; }
}

?>
