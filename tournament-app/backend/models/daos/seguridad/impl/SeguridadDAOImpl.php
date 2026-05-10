<?php

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../../base/ICRUD.php';

require_once __DIR__ . '/../interfaces/ISeguridadDAO.php';

class SeguridadDAOImpl extends BaseDAO implements ICRUD, ISeguridadDAO {

    /*
    |--------------------------------------------------------------------------
    | Tabla principal
    |--------------------------------------------------------------------------
    */

    private $table = "log";

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
    | Obtener todos
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        $sql = "

        SELECT *

        FROM {$this->table}

        ORDER BY id_log DESC

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

        WHERE id_log = ?

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
    | Crear log
    |--------------------------------------------------------------------------
    */

    public function create($data){

        $sql = "

    |    INSERT INTO {$this->table}

(

        id_usuario,

        accion,

        modulo,

        descripcion,

        ip_address,

        nivel,

        fecha

)

        VALUES

        (

    ?, ?, ?, ?, ?, ?, NOW()

        )

        ";

        return $this->execute(

            $sql,

            [

    $data["id_usuario"],

    $data["accion"],

    $data["modulo"],

    $data["descripcion"],

    $data["ip_address"],

    $data["nivel"]

],

            "isssss"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar log
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        $sql = "

       UPDATE {$this->table}

SET

    accion = ?,

    modulo = ?,

    descripcion = ?,

    ip_address = ?,

    nivel = ?

WHERE id_log = ?

        ";

        return $this->execute(

            $sql,

            [

                $data["accion"],
                $data["modulo"],
                $data["descripcion"],
                $data["ip_address"],
                $data["nivel"],
                $id

            ],

            "sssssi"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar log
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $sql = "

        DELETE FROM {$this->table}

        WHERE id_log = ?

        ";

        return $this->execute(

            $sql,

            [$id],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar logs
    |--------------------------------------------------------------------------
    */

    public function search($search){

        $search = "%{$search}%";

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE accion LIKE ?

        ORDER BY id_log DESC

        ";

        return $this->fetchAll(

            $sql,

            [$search],

            "s"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Contar logs
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
    | Logs recientes
    |--------------------------------------------------------------------------
    */

    public function getRecentLogs(){

        $sql = "

        SELECT *

        FROM {$this->table}

        ORDER BY fecha DESC

        LIMIT 20

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Logs por usuario
    |--------------------------------------------------------------------------
    */

    public function getLogsByUser($id_usuario){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE id_usuario = ?

        ORDER BY fecha DESC

        ";

        return $this->fetchAll(

            $sql,

            [$id_usuario],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Sesiones activas
    |--------------------------------------------------------------------------
    */

    public function getActiveSessions(){

        $sql = "

        SELECT *

        FROM sesion

        WHERE estado = 1

        ORDER BY fecha_inicio DESC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Registrar evento
    |--------------------------------------------------------------------------
    */

    public function registrarEvento($data){

        return $this->create($data);

    }

    /*
    |--------------------------------------------------------------------------
    | Contar logs
    |--------------------------------------------------------------------------
    */

    public function countLogs(){

        return $this->countAll();

    }

    /*
    |--------------------------------------------------------------------------
    | Intentos fallidos
    |--------------------------------------------------------------------------
    */

    public function getFailedAttempts(){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE accion LIKE '%fallido%'

        ORDER BY fecha DESC

        ";

        return $this->fetchAll($sql);

    }

}

?>