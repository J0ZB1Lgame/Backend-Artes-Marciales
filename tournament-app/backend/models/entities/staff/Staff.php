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
    private $cargo;
    private $turno;

    // Constructor
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
        $turno = null
    ) {
        $this->id_staff = $id_staff;
        $this->id_usuario = $id_usuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->tipo_documento = $tipo_documento;
        $this->numero_documento = $numero_documento;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->estado = $estado;
        $this->cargo = $cargo;
        $this->turno = $turno;
    }

    // Getters
    public function getId_staff() { return $this->id_staff; }
    public function getId_usuario() { return $this->id_usuario; }
    public function getNombre() { return $this->nombre; }
    public function getApellido() { return $this->apellido; }
    public function getTipo_documento() { return $this->tipo_documento; }
    public function getNumero_documento() { return $this->numero_documento; }
    public function getTelefono() { return $this->telefono; }
    public function getEmail() { return $this->email; }
    public function getEstado() { return $this->estado; }
    public function getCargo() { return $this->cargo; }
    public function getTurno() { return $this->turno; }

    // Setters
    public function setId_staff($id_staff) { $this->id_staff = $id_staff; }
    public function setId_usuario($id_usuario) { $this->id_usuario = $id_usuario; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setApellido($apellido) { $this->apellido = $apellido; }
    public function setTipo_documento($tipo_documento) { $this->tipo_documento = $tipo_documento; }
    public function setNumero_documento($numero_documento) { $this->numero_documento = $numero_documento; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setEmail($email) { $this->email = $email; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setCargo($cargo) { $this->cargo = $cargo; }
    public function setTurno($turno) { $this->turno = $turno; }
}

?>
