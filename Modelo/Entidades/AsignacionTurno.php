<?php
class AsignacionTurno {
    private $id_asignacion;
    private $id_staff;
    private $id_turno;
    private $fecha;
    private $estado;

    public function __construct($id_asignacion = null, $id_staff = null, $id_turno = null, $fecha = null, $estado = 'activo') {
        $this->id_asignacion = $id_asignacion;
        $this->id_staff = $id_staff;
        $this->id_turno = $id_turno;
        $this->fecha = $fecha;
        $this->estado = $estado;
    }

    public function getIdAsignacion() {
        return $this->id_asignacion;
    }

    public function setIdAsignacion($id_asignacion) {
        $this->id_asignacion = $id_asignacion;
    }

    public function getIdStaff() {
        return $this->id_staff;
    }

    public function setIdStaff($id_staff) {
        $this->id_staff = $id_staff;
    }

    public function getIdTurno() {
        return $this->id_turno;
    }

    public function setIdTurno($id_turno) {
        $this->id_turno = $id_turno;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
}
?>