<?php

/*
|--------------------------------------------------------------------------
| STAFF TORNEO CONTROLLER
|--------------------------------------------------------------------------
| Budokai Martial Arts Tournament System
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../models/daos/staff/impl/StaffTorneoDAOImpl.php';

class StaffTorneoController {

    /*
    |--------------------------------------------------------------------------
    | DAO
    |--------------------------------------------------------------------------
    */

    private $staffTorneoDAO;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(){

        $this->staffTorneoDAO = new StaffTorneoDAOImpl();

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener todos los miembros
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        try {

            $data = $this->staffTorneoDAO->getAll();

            return [

                "success" => true,

                "message" => "Staff obtenido correctamente",

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener miembro por ID
    |--------------------------------------------------------------------------
    */

    public function getById($id){

        try {

            $data = $this->staffTorneoDAO->getById($id);

            if(!$data){

                return [

                    "success" => false,

                    "message" => "Miembro no encontrado"

                ];

            }

            return [

                "success" => true,

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Crear miembro
    |--------------------------------------------------------------------------
    */

    public function create($data){

        try {

            /*
            |--------------------------------------------------------------------------
            | Validaciones básicas
            |--------------------------------------------------------------------------
            */

            if(empty($data["nombre"])){

                return [

                    "success" => false,

                    "message" => "El nombre es obligatorio"

                ];

            }

            if(empty($data["apellido"])){

                return [

                    "success" => false,

                    "message" => "El apellido es obligatorio"

                ];

            }

            /*
            |--------------------------------------------------------------------------
            | Crear
            |--------------------------------------------------------------------------
            */

            $id = $this->staffTorneoDAO->create($data);

            return [

                "success" => true,

                "message" => "Miembro creado correctamente",

                "id" => $id

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar miembro
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        try {

            $exists = $this->staffTorneoDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Miembro no encontrado"

                ];

            }

            $this->staffTorneoDAO->update($id, $data);

            return [

                "success" => true,

                "message" => "Miembro actualizado correctamente"

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar miembro
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        try {

            $exists = $this->staffTorneoDAO->getById($id);

            if(!$exists){

                return [

                    "success" => false,

                    "message" => "Miembro no encontrado"

                ];

            }

            $this->staffTorneoDAO->delete($id);

            return [

                "success" => true,

                "message" => "Miembro eliminado correctamente"

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar miembros
    |--------------------------------------------------------------------------
    */

    public function search($search){

        try {

            $data = $this->staffTorneoDAO->search($search);

            return [

                "success" => true,

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Contar miembros
    |--------------------------------------------------------------------------
    */

    public function countAll(){

        try {

            $data = $this->staffTorneoDAO->countAll();

            return [

                "success" => true,

                "data" => $data

            ];

        } catch(Exception $e){

            return [

                "success" => false,

                "message" => $e->getMessage()

            ];

        }

    }

    public function mostrarZona() {
        $conn = Database::getInstance()->getConnection();
        $result = $conn->query("SELECT id_zona, nombre FROM zonas ORDER BY nombre");
        if (!$result) return [];
        $zonas = [];
        while ($row = $result->fetch_assoc()) $zonas[] = $row;
        return $zonas;
    }

    public function crearZona($data) {
        $nombre = trim($data['nombre'] ?? '');
        if (!$nombre) return false;
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("INSERT INTO zonas (nombre) VALUES (?)");
        if (!$stmt) return false;
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        return $conn->insert_id;
    }

    public function eliminarZona($id) {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("DELETE FROM zonas WHERE id_zona = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function mostrarRol() {
        $conn = Database::getInstance()->getConnection();
        $result = $conn->query("SELECT id_rol, nombre FROM roles ORDER BY nombre");
        if (!$result) return [];
        $roles = [];
        while ($row = $result->fetch_assoc()) $roles[] = $row;
        return $roles;
    }

    public function buscarZona($id) {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT id_zona, nombre FROM zonas WHERE id_zona = ?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function buscarRol($id) {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT id_rol, nombre FROM roles WHERE id_rol = ?");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // ── Métodos legacy del API extended ─────────────────────────────────────
    public function listarStaffPorTorneo($ejecutor, $torneo) { return []; }
    public function registrarStaff($ejecutor, $datos)        { return $this->create($datos); }
    public function asignarStaffATorneo($ej, $staff, $torneo){ return true; }
    public function asignarZona($ej, $staff, $zona)          { return $this->staffTorneoDAO->update((int)$staff, ['id_zona' => (int)$zona]); }
    public function asignarRol($ej, $staff, $rol)            { return $this->staffTorneoDAO->update((int)$staff, ['id_rol'  => (int)$rol]);  }
    public function eliminarStaffDeTorneo($ej,$staff,$torneo){ return $this->delete((int)$staff); }
    public function mostrarLuchadores()                       { return []; }
    public function buscarLuchador($nombre)                   { return null; }
    public function crearLuchador($data)                      { return false; }
    public function actualizarInformacionLuchador($data)      { return false; }
    public function eliminarLuchador($id)                     { return false; }
    public function actualizarInformacionRol($data)           { return false; }
    public function actualizarInformacionZona($data)          { return false; }

}

?>