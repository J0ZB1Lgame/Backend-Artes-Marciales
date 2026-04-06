<?php
class Usuario {
    private $id_usuario;
    private $username;
    private $password_hash;
    private $estado;
    private $fecha_creacion;

    // Constructor
    public function __construct($id_usuario = null, $username = null, $password_hash = null, $estado = 'activo', $fecha_creacion = null) {
        $this->id_usuario = $id_usuario;
        $this->username = $username;
        $this->password_hash = $password_hash;
        $this->estado = $estado;
        $this->fecha_creacion = $fecha_creacion;
    }

    // Getters y Setters
    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPasswordHash() {
        return $this->password_hash;
    }

    public function setPasswordHash($password_hash) {
        $this->password_hash = $password_hash;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getFechaCreacion() {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion($fecha_creacion) {
        $this->fecha_creacion = $fecha_creacion;
    }
}
?>