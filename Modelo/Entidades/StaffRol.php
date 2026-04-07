<?php
class StaffRol {
    private $id_staff;
    private $id_tipo_rol;
    private $fecha_asignacion;

    public function __construct($id_staff = null, $id_tipo_rol = null, $fecha_asignacion = null) {
        $this->id_staff = $id_staff;
        $this->id_tipo_rol = $id_tipo_rol;
        $this->fecha_asignacion = $fecha_asignacion;
    }

    public function getIdStaff() {
        return $this->id_staff;
    }

    public function setIdStaff($id_staff) {
        $this->id_staff = $id_staff;
    }

    public function getIdTipoRol() {
        return $this->id_tipo_rol;
    }

    public function setIdTipoRol($id_tipo_rol) {
        $this->id_tipo_rol = $id_tipo_rol;
    }

    public function getFechaAsignacion() {
        return $this->fecha_asignacion;
    }

    public function setFechaAsignacion($fecha_asignacion) {
        $this->fecha_asignacion = $fecha_asignacion;
    }
}
?>