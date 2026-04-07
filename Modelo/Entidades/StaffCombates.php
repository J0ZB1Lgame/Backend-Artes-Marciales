<?php
require_once __DIR__ . '/Staff.php';

class StaffCombates extends Staff {
    private $id_combate;
    private $rol_en_combate;
    private $estado_asignacion;

    public function __construct($id_staff = null, $id_usuario = null, $nombre = null, $apellido = null, $tipo_documento = null, $numero_documento = null, $telefono = null, $email = null, $estado = 'activo', $id_combate = null, $rol_en_combate = null, $estado_asignacion = null) {
        parent::__construct($id_staff, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado);
        $this->id_combate = $id_combate;
        $this->rol_en_combate = $rol_en_combate;
        $this->estado_asignacion = $estado_asignacion;
    }

    public function getIdCombate() {
        return $this->id_combate;
    }

    public function setIdCombate($id_combate) {
        $this->id_combate = $id_combate;
    }

    public function getRolEnCombate() {
        return $this->rol_en_combate;
    }

    public function setRolEnCombate($rol_en_combate) {
        $this->rol_en_combate = $rol_en_combate;
    }

    public function getEstadoAsignacion() {
        return $this->estado_asignacion;
    }

    public function setEstadoAsignacion($estado_asignacion) {
        $this->estado_asignacion = $estado_asignacion;
    }
}
?>