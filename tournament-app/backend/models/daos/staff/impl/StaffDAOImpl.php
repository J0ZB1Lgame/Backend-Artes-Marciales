<?php

/*
|--------------------------------------------------------------------------
| STAFF DAO IMPLEMENTATION
|--------------------------------------------------------------------------
| Budokai Martial Arts Tournament System
|--------------------------------------------------------------------------
| Implementación completa del módulo STAFF
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../interfaces/IStaffDAO.php';

class StaffDAOImpl implements IStaffDAO {

    /*
    |--------------------------------------------------------------------------
    | Conexión
    |--------------------------------------------------------------------------
    */

    private $conn;

    /*
    |--------------------------------------------------------------------------
    | Tabla principal
    |--------------------------------------------------------------------------
    */

    private $table = "staff_torneo";

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(){

        $database = Database::getInstance();

        $this->conn = $database->getConnection();

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener todo el staff
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        $sql = "

        SELECT

            s.id_staff_torneo,

            s.nombre,
            s.apellido,

            s.tipo_documento,
            s.numero_documento,

            s.telefono,
            s.email,

            s.estado,

            s.id_rol,
            s.id_zona,

            r.nombre AS rol_nombre,

            z.nombre AS zona_nombre,

            s.fecha_creacion

        FROM {$this->table} s

        LEFT JOIN rol r
        ON s.id_rol = r.id_rol

        LEFT JOIN zona z
        ON s.id_zona = z.id_zona

        ORDER BY s.id_staff_torneo DESC

        ";

        $result = $this->conn->query($sql);

        $data = [];

        while($row = $result->fetch_assoc()){

            $data[] = $row;

        }

        return $data;

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener por ID
    |--------------------------------------------------------------------------
    */

    public function getById($id){

        $stmt = $this->conn->prepare("

            SELECT *

            FROM {$this->table}

            WHERE id_staff_torneo = ?

            LIMIT 1

        ");

        $stmt->bind_param("i",$id);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();

    }

    /*
    |--------------------------------------------------------------------------
    | Crear staff
    |--------------------------------------------------------------------------
    */

    public function create($data){

        $stmt = $this->conn->prepare("

            INSERT INTO {$this->table}

            (

                nombre,
                apellido,

                tipo_documento,
                numero_documento,

                telefono,
                email,

                estado,

                id_rol,
                id_zona,

                fecha_creacion

            )

            VALUES

            (

                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, NOW()

            )

        ");

        $stmt->bind_param(

            "sssssssii",

            $data["nombre"],
            $data["apellido"],

            $data["tipo_documento"],
            $data["numero_documento"],

            $data["telefono"],
            $data["email"],

            $data["estado"],

            $data["id_rol"],
            $data["id_zona"]

        );

        return $stmt->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar staff
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        $stmt = $this->conn->prepare("

            UPDATE {$this->table}

            SET

                nombre = ?,
                apellido = ?,

                tipo_documento = ?,
                numero_documento = ?,

                telefono = ?,
                email = ?,

                estado = ?,

                id_rol = ?,
                id_zona = ?

            WHERE id_staff_torneo = ?

        ");

        $stmt->bind_param(

            "sssssssiii",

            $data["nombre"],
            $data["apellido"],

            $data["tipo_documento"],
            $data["numero_documento"],

            $data["telefono"],
            $data["email"],

            $data["estado"],

            $data["id_rol"],
            $data["id_zona"],

            $id

        );

        return $stmt->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar staff
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $stmt = $this->conn->prepare("

            DELETE FROM {$this->table}

            WHERE id_staff_torneo = ?

        ");

        $stmt->bind_param("i",$id);

        return $stmt->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar staff
    |--------------------------------------------------------------------------
    */

    public function search($search){

        $search = "%{$search}%";

        $stmt = $this->conn->prepare("

            SELECT *

            FROM {$this->table}

            WHERE

                nombre LIKE ?
                OR apellido LIKE ?
                OR numero_documento LIKE ?
                OR email LIKE ?

            ORDER BY id_staff_torneo DESC

        ");

        $stmt->bind_param(

            "ssss",

            $search,
            $search,
            $search,
            $search

        );

        $stmt->execute();

        $result = $stmt->get_result();

        $data = [];

        while($row = $result->fetch_assoc()){

            $data[] = $row;

        }

        return $data;

    }

    /*
    |--------------------------------------------------------------------------
    | Contar total
    |--------------------------------------------------------------------------
    */

    public function countAll(){

        $sql = "

        SELECT COUNT(*) AS total

        FROM {$this->table}

        ";

        $result = $this->conn->query($sql);

        return $result->fetch_assoc();

    }

    /*
    |--------------------------------------------------------------------------
    | Contar por rol
    |--------------------------------------------------------------------------
    */

    public function countByRole($id_rol){

        $stmt = $this->conn->prepare("

            SELECT COUNT(*) AS total

            FROM {$this->table}

            WHERE id_rol = ?

        ");

        $stmt->bind_param("i",$id_rol);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener activos
    |--------------------------------------------------------------------------
    */

    public function getActiveStaff(){

        $stmt = $this->conn->prepare("

            SELECT *

            FROM {$this->table}

            WHERE estado = 'activo'

            ORDER BY nombre ASC

        ");

        $stmt->execute();

        $result = $stmt->get_result();

        $data = [];

        while($row = $result->fetch_assoc()){

            $data[] = $row;

        }

        return $data;

    }

    /*
    |--------------------------------------------------------------------------
    | Validar documento
    |--------------------------------------------------------------------------
    */

    public function documentExists($documento){

        $stmt = $this->conn->prepare("

            SELECT id_staff_torneo

            FROM {$this->table}

            WHERE numero_documento = ?

            LIMIT 1

        ");

        $stmt->bind_param("s",$documento);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;

    }

    /*
    |--------------------------------------------------------------------------
    | Validar email
    |--------------------------------------------------------------------------
    */

    public function emailExists($email){

        $stmt = $this->conn->prepare("

            SELECT id_staff_torneo

            FROM {$this->table}

            WHERE email = ?

            LIMIT 1

        ");

        $stmt->bind_param("s",$email);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;

    }

}

?>