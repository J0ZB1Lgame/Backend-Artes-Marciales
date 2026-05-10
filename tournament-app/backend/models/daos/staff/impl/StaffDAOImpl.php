<?php

/*
|--------------------------------------------------------------------------
| STAFF DAO IMPLEMENTATION
|--------------------------------------------------------------------------
| Budokai Martial Arts Tournament System
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../../base/ICRUD.php';

require_once __DIR__ . '/../interfaces/IStaffDAO.php';

class StaffDAOImpl extends BaseDAO implements ICRUD, IStaffDAO {

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

        parent::__construct(
            $database->getConnection()
        );

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

            s.fecha_ingreso

        FROM {$this->table} s

        LEFT JOIN rol r
        ON s.id_rol = r.id_rol

        LEFT JOIN zona z
        ON s.id_zona = z.id_zona

        ORDER BY s.id_staff_torneo DESC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener por ID
    |--------------------------------------------------------------------------
    */

    public function getById($id){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE id_staff_torneo = ?

        LIMIT 1

        ";

        return $this->fetch(
            $sql,
            [$id],
            "i"
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Crear staff
    |--------------------------------------------------------------------------
    */

    public function create($data){

        $sql = "

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

            fecha_ingreso

        )

        VALUES

        (

            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, NOW()

        )

        ";

        return $this->execute(

            $sql,

            [

                $data["nombre"],
                $data["apellido"],

                $data["tipo_documento"],
                $data["numero_documento"],

                $data["telefono"],
                $data["email"],

                $data["estado"],

                $data["id_rol"],
                $data["id_zona"]

            ],

            "sssssssii"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar staff
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        $sql = "

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

        ";

        return $this->execute(

            $sql,

            [

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

            ],

            "sssssssiii"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar staff
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $sql = "

        DELETE FROM {$this->table}

        WHERE id_staff_torneo = ?

        ";

        return $this->execute(

            $sql,

            [$id],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar staff
    |--------------------------------------------------------------------------
    */

    public function search($search){

        $search = "%{$search}%";

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE

            nombre LIKE ?
            OR apellido LIKE ?
            OR numero_documento LIKE ?
            OR email LIKE ?

        ORDER BY id_staff_torneo DESC

        ";

        return $this->fetchAll(

            $sql,

            [

                $search,
                $search,
                $search,
                $search

            ],

            "ssss"

        );

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

        return $this->fetch($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Contar por rol
    |--------------------------------------------------------------------------
    */

    public function countByRole($id_rol){

        $sql = "

        SELECT COUNT(*) AS total

        FROM {$this->table}

        WHERE id_rol = ?

        ";

        return $this->fetch(

            $sql,

            [$id_rol],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener activos
    |--------------------------------------------------------------------------
    */

    public function getActiveStaff(){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE estado = 'activo'

        ORDER BY nombre ASC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Validar documento
    |--------------------------------------------------------------------------
    */

    public function documentExists($documento){

        $sql = "

        SELECT id_staff_torneo

        FROM {$this->table}

        WHERE numero_documento = ?

        LIMIT 1

        ";

        $result = $this->fetch(

            $sql,

            [$documento],

            "s"

        );

        return !empty($result);

    }

    /*
    |--------------------------------------------------------------------------
    | Validar email
    |--------------------------------------------------------------------------
    */

    public function emailExists($email){

        $sql = "

        SELECT id_staff_torneo

        FROM {$this->table}

        WHERE email = ?

        LIMIT 1

        ";

        $result = $this->fetch(

            $sql,

            [$email],

            "s"

        );

        return !empty($result);

    }

}

?>