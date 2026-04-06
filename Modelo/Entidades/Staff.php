<?php
class Staff {
    private $id_staff;
    private $id_usuario;
    private $nombre;
    private $apellido;
    private $tipo_documento;
    private $numero_documento;
    private $telefono;
    private $email;
    private $estado;

    // Constructor
    public function __construct($id_staff = null, $id_usuario = null, $nombre = null, $apellido = null, $tipo_documento = null, $numero_documento = null, $telefono = null, $email = null, $estado = 'activo') {
        $this->id_staff = $id_staff;
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo_documento = $tipo_documento;
        $this->numero_documento = $numero_documento;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->estado = $estado;
    }

    // Getters y Setters
    public function getIdStaff() {
        return $this->id_staff;
    }

    public function setIdStaff($id_staff) {
        $this->id_staff = $id_staff;
    }

    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function getTipoDocumento() {
        return $this->tipo_documento;
    }

    public function setTipoDocumento($tipo_documento) {
        $this->tipo_documento = $tipo_documento;
    }

    public function getNumeroDocumento() {
        return $this->numero_documento;
    }

    public function setNumeroDocumento($numero_documento) {
        $this->numero_documento = $numero_documento;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
}
?>