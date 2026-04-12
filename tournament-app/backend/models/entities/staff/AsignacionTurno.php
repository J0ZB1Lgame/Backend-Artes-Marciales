<?php

class AsignacionTurno {
    private $id_asignacion;
    private $id_staff;
    private $id_turno;
    private $fecha;
    private $estado;

    public function __construct($id_asignacion = null, $id_staff = null, $id_turno = null, $fecha = null, $estado = null) {
        $this->id_asignacion = $id_asignacion;
        $this->id_staff = $id_staff;
        $this->id_turno = $id_turno;
        $this->fecha = $fecha;
        $this->estado = $estado;
    }

    public function getId_asignacion() { return $this->id_asignacion; }
    public function getId_staff() { return $this->id_staff; }
    public function getId_turno() { return $this->id_turno; }
    public function getFecha() { return $this->fecha; }
    public function getEstado() { return $this->estado; }

    public function setId_asignacion($id_asignacion) { $this->id_asignacion = $id_asignacion; }
    public function setId_staff($id_staff) { $this->id_staff = $id_staff; }
    public function setId_turno($id_turno) { $this->id_turno = $id_turno; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setEstado($estado) { $this->estado = $estado; }
}

?>
