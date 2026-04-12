<?php

require_once __DIR__ . '/Staff.php';

class StaffCombates extends Staff {
    private $id_combate;
    private $rol_en_combate;
    private $estado_asignacion;

    public function __construct(
        $id_staff = null,
        $id_usuario = null,
        $nombre = null,
        $apellido = null,
        $tipo_documento = null,
        $numero_documento = null,
        $telefono = null,
        $email = null,
        $estado = null,
        $cargo = null,
        $turno = null,
        $id_combate = null,
        $rol_en_combate = null,
        $estado_asignacion = null
    ) {
        parent::__construct($id_staff, $id_usuario, $nombre, $apellido, $tipo_documento, $numero_documento, $telefono, $email, $estado, $cargo, $turno);
        $this->id_combate = $id_combate;
        $this->rol_en_combate = $rol_en_combate;
        $this->estado_asignacion = $estado_asignacion;
    }

    public function getId_combate() { return $this->id_combate; }
    public function getRol_en_combate() { return $this->rol_en_combate; }
    public function getEstado_asignacion() { return $this->estado_asignacion; }

    public function setId_combate($id_combate) { $this->id_combate = $id_combate; }
    public function setRol_en_combate($rol_en_combate) { $this->rol_en_combate = $rol_en_combate; }
    public function setEstado_asignacion($estado_asignacion) { $this->estado_asignacion = $estado_asignacion; }
}

?>
