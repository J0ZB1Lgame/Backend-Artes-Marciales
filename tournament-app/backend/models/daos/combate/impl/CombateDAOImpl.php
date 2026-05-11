<?php

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../../base/ICRUD.php';

require_once __DIR__ . '/../interfaces/ICombateDAO.php';

class CombateDAOImpl extends BaseDAO implements ICRUD, ICombateDAO {

    /*
    |--------------------------------------------------------------------------
    | Tabla principal
    |--------------------------------------------------------------------------
    */

    private $table = "combates";

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

        ORDER BY id_combate DESC

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

        WHERE id_combate = ?

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
    | Crear combate
    |--------------------------------------------------------------------------
    */

    public function create($data){

        $sql = "

        INSERT INTO {$this->table}

        (

            id_torneo,

            id_luchador_1,
            id_luchador_2,

            ganador_id,

            estado,

            ronda,

            fecha_combate,

            arena,

            observaciones,
            duracion_segundos,

            puntos_luchador_1,
            puntos_luchador_2

        )

        VALUES

        (

            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?

        )

        ";

        return $this->execute(

            $sql,

            [

                $data["id_torneo"],

                $data["id_luchador_1"],
                $data["id_luchador_2"],

                $data["ganador_id"],

                $data["estado"],

                $data["ronda"],

                $data["fecha_combate"],

                $data["arena"],

                $data["observaciones"],
                $data["duracion_segundos"],
                $data["puntos_luchador_1"],
                $data["puntos_luchador_2"]

            ],

            "iiiisssssiii"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar combate
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        $sql = "

        UPDATE {$this->table}

        SET

            id_torneo = ?,

            id_luchador_1 = ?,
            id_luchador_2 = ?,

            ganador_id = ?,

            estado = ?,

            ronda = ?,

            fecha_combate = ?,

            arena = ?,

            observaciones = ?,
            duracion_segundos = ?,
            puntos_luchador_1 = ?,
            puntos_luchador_2 = ?

        WHERE id_combate = ?

        ";

        return $this->execute(

            $sql,

            [

                $data["id_torneo"],

                $data["id_luchador_1"],
                $data["id_luchador_2"],

                $data["ganador_id"],

                $data["estado"],

                $data["ronda"],

                $data["fecha_combate"],

                $data["arena"],

                $data["observaciones"],
                $data["duracion_segundos"],
                $data["puntos_luchador_1"],
                $data["puntos_luchador_2"],

                $id

            ],

            "iiiisssssiiii"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar combate
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $sql = "

        DELETE FROM {$this->table}

        WHERE id_combate = ?

        ";

        return $this->execute(

            $sql,

            [$id],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar combate
    |--------------------------------------------------------------------------
    */

    public function search($search){

        $search = "%{$search}%";

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE

            estado LIKE ?
            OR ronda LIKE ?
            OR arena LIKE ?

        ORDER BY id_combate DESC

        ";

        return $this->fetchAll(

            $sql,

            [

                $search,
                $search,
                $search

            ],

            "sss"

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
    | Obtener por torneo
    |--------------------------------------------------------------------------
    */

    public function getByTorneo($id_torneo){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE id_torneo = ?

        ORDER BY fecha_combate ASC

        ";

        return $this->fetchAll(

            $sql,

            [$id_torneo],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener activos
    |--------------------------------------------------------------------------
    */

    public function getActivos(){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE estado = 'activo'

        ORDER BY fecha_combate ASC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener finalizados
    |--------------------------------------------------------------------------
    */

    public function getFinalizados(){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE estado = 'finalizado'

        ORDER BY fecha_combate DESC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener próximos
    |--------------------------------------------------------------------------
    */

    public function getProximos(){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE fecha_combate >= NOW()

        ORDER BY fecha_combate ASC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Historial luchador
    |--------------------------------------------------------------------------
    */

    public function getHistorialLuchador($id_luchador){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE

            id_luchador_1 = ?
            OR id_luchador_2 = ?

        ORDER BY fecha_combate DESC

        ";

        return $this->fetchAll(

            $sql,

            [

                $id_luchador,
                $id_luchador

            ],

            "ii"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Contar por estado
    |--------------------------------------------------------------------------
    */

    public function countByEstado($estado){

        $sql = "

        SELECT COUNT(*) AS total

        FROM {$this->table}

        WHERE estado = ?

        ";

        return $this->fetch(

            $sql,

            [$estado],

            "s"

        );

    }

}

?>