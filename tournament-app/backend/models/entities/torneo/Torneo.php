<?php

class Torneo {

    private $idTorneo;
    private $nombre;
    private $estado;
    //private $tipo;(opcional: Torneo, libre o versus)
    private $reglas;

    //Constructor
    public __construct($idTorneo,$nombre,$estado,$reglas = null){
        $this->$idTorneo = $idTorneo;
        $this->$nombre = $nombre;
        $this->$estado = $estado;
        $this->$reglas = $reglas;
    }
    
    public function getIdTorneo(){
        return $this->$idTorneo;
    }
    public function setIdTorneo($idTorneo){
        $this->$idTorneo = $idTorneo;
    }

    public function getNombre(){
        return $this->$nombre;
    }

    public function setNombre($nombre){
        $this->$nombre = $nombre;
    }

    public function getEstado(){
        return $this->$estado;
    }

    public function setEstado($estado){
        $this->$estado = $estado;
    }

    public function getReglas(){
        return $this->$reglas;
    }

    public function setReglas($reglas){
        $this->$reglas = $reglas;
    }
}