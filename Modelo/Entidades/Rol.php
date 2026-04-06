<?php
class Rol {
    private $id_rol;
    private $nombre_rol;
    private $descripcion;

    // Constructor
    public function __construct($id_rol = null, $nombre_rol = null, $descripcion = null) {
        $this->id_rol = $id_rol;
        $this->nombre_rol = $nombre_rol;
        $this->descripcion = $descripcion;
    }

    // Getters y Setters
    public function getIdRol() {
        return $this->id_rol;
    }

    public function setIdRol($id_rol) {
        $this->id_rol = $id_rol;
    }

    public function getNombreRol() {
        return $this->nombre_rol;
    }

    public function setNombreRol($nombre_rol) {
        $this->nombre_rol = $nombre_rol;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
}
?>