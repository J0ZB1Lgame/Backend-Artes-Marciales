<?php
class Turno {
    private $id_turno;
    private $nombre;
    private $hora_inicio;
    private $hora_fin;

    public function __construct($id_turno = null, $nombre = null, $hora_inicio = null, $hora_fin = null) {
        $this->id_turno = $id_turno;
        $this->nombre = $nombre;
        $this->hora_inicio = $hora_inicio;
        $this->hora_fin = $hora_fin;
    }

    public function getIdTurno() {
        return $this->id_turno;
    }

    public function setIdTurno($id_turno) {
        $this->id_turno = $id_turno;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getHoraInicio() {
        return $this->hora_inicio;
    }

    public function setHoraInicio($hora_inicio) {
        $this->hora_inicio = $hora_inicio;
    }

    public function getHoraFin() {
        return $this->hora_fin;
    }

    public function setHoraFin($hora_fin) {
        $this->hora_fin = $hora_fin;
    }
}
?>