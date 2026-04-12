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

    public function getId_tipo_rol() { return $this->id_tipo_rol; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }

    public function setId_tipo_rol($id_tipo_rol) { $this->id_tipo_rol = $id_tipo_rol; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
}

?>
