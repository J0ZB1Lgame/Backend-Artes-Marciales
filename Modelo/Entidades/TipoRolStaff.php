<?php
class TipoRolStaff {
    private $id_tipo_rol;
    private $nombre;
    private $descripcion;

    public function __construct($id_tipo_rol = null, $nombre = null, $descripcion = null) {
        $this->id_tipo_rol = $id_tipo_rol;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    public function getIdTipoRol() {
        return $this->id_tipo_rol;
    }

    public function setIdTipoRol($id_tipo_rol) {
        $this->id_tipo_rol = $id_tipo_rol;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }
}
?>