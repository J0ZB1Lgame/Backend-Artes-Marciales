<?php

class Usuario {
    private $id_usuario;
    private $username;
    private $password_hash;
    private $estado;
    private $fecha_creacion;

    public function __construct($id_usuario = null, $username = null, $password_hash = null, $estado = null, $fecha_creacion = null) {
        $this->id_usuario = $id_usuario;
        $this->username = $username;
        $this->password_hash = $password_hash;
        $this->estado = $estado;
        $this->fecha_creacion = $fecha_creacion;
    }

    public function getId_usuario() { return $this->id_usuario; }
    public function getUsername() { return $this->username; }
    public function getPassword_hash() { return $this->password_hash; }
    public function getEstado() { return $this->estado; }
    public function getFecha_creacion() { return $this->fecha_creacion; }

    public function setId_usuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function setUsername($username) { $this->username = $username; }
    public function setPassword_hash($password_hash) { $this->password_hash = $password_hash; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setFecha_creacion($fecha_creacion) { $this->fecha_creacion = $fecha_creacion; }
}

?>
