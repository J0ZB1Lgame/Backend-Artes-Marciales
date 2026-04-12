<?php

require_once __DIR__ . '/../interfaces/IStaffDAO.php';
require_once __DIR__ . '/../../entities/staff/Staff.php';
require_once __DIR__ . '/../../../config/conexion.php';
require_once __DIR__ . '/../../base/BaseDAO.php';

class StaffDAOImpl extends BaseDAO implements IStaffDAO {

    public function __construct() {
        global $conn;
        parent::__construct($conn);
    }

    public function crear($staff) {
        global $conn;
        $sql = "INSERT INTO staff (id_usuario, nombre, apellido, tipo_documento, numero_documento, telefono, email, estado, cargo, turno) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isssssisss",
            $staff->getId_usuario(),
            $staff->getNombre(),
            $staff->getApellido(),
            $staff->getTipo_documento(),
            $staff->getNumero_documento(),
            $staff->getTelefono(),
            $staff->getEmail(),
            $staff->getEstado(),
            $staff->getCargo(),
            $staff->getTurno()
        );
        
        if ($stmt->execute()) {
            $staff->setId_staff($conn->insert_id);
            return $staff;
        }
        return false;
    }

    public function obtenerPorId($id) {
        global $conn;
        $sql = "SELECT * FROM staff WHERE id_staff = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Staff(
                $row['id_staff'],
                $row['id_usuario'],
                $row['nombre'],
                $row['apellido'],
                $row['tipo_documento'],
                $row['numero_documento'],
                $row['telefono'],
                $row['email'],
                $row['estado'],
                $row['cargo'],
                $row['turno']
            );
        }
        return null;
    }

    public function actualizar($staff) {
        global $conn;
        $sql = "UPDATE staff SET id_usuario=?, nombre=?, apellido=?, tipo_documento=?, numero_documento=?, telefono=?, email=?, estado=?, cargo=?, turno=? WHERE id_staff=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isssssissi",
            $staff->getId_usuario(),
            $staff->getNombre(),
            $staff->getApellido(),
            $staff->getTipo_documento(),
            $staff->getNumero_documento(),
            $staff->getTelefono(),
            $staff->getEmail(),
            $staff->getEstado(),
            $staff->getCargo(),
            $staff->getTurno(),
            $staff->getId_staff()
        );
        
        return $stmt->execute();
    }

    public function eliminarPorId($id) {
        global $conn;
        $sql = "DELETE FROM staff WHERE id_staff = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listarTodos() {
        global $conn;
        $sql = "SELECT * FROM staff";
        $result = $conn->query($sql);
        $staffList = [];
        
        while ($row = $result->fetch_assoc()) {
            $staffList[] = new Staff(
                $row['id_staff'],
                $row['id_usuario'],
                $row['nombre'],
                $row['apellido'],
                $row['tipo_documento'],
                $row['numero_documento'],
                $row['telefono'],
                $row['email'],
                $row['estado'],
                $row['cargo'],
                $row['turno']
            );
        }
        return $staffList;
    }
}

?>
