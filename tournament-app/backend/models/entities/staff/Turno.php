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

    public function getId_turno() { return $this->id_turno; }
    public function getNombre() { return $this->nombre; }
    public function getHora_inicio() { return $this->hora_inicio; }
    public function getHora_fin() { return $this->hora_fin; }

    public function setId_turno($id_turno) { $this->id_turno = $id_turno; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setHora_inicio($hora_inicio) { $this->hora_inicio = $hora_inicio; }
    public function setHora_fin($hora_fin) { $this->hora_fin = $hora_fin; }
}

?>
