<?php

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../../base/ICRUD.php';

require_once __DIR__ . '/../interfaces/ITorneoDAO.php';

class TorneoDAOImpl extends BaseDAO implements ICRUD, ITorneoDAO {

    /*
    |--------------------------------------------------------------------------
    | Tabla principal
    |--------------------------------------------------------------------------
    */

    private $table = "torneos";

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

        ORDER BY id_torneo DESC

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

        WHERE id_torneo = ?

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
    | Crear torneo
    |--------------------------------------------------------------------------
    */

    public function create($data){

        $sql = "

        INSERT INTO {$this->table}

        (

            nombre,

            descripcion,

            fecha_inicio,
            fecha_fin,

            estado,

            ubicacion,

            tipo,

            premio,
            logo,
            capacidad_maxima,
            reglas

        )

        VALUES

        (

            ?, ?, ?, ?,
            ?, ?, ?, ?

        )

        ";

        return $this->execute(

            $sql,

            [

                $data["nombre"],

                $data["descripcion"],

                $data["fecha_inicio"],
                $data["fecha_fin"],

                $data["estado"],

                $data["ubicacion"],

                $data["tipo"],

                $data["premio"],
                $data["logo"],
                $data["capacidad_maxima"],
                $data["reglas"]

            ],

            "ssssssssis"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar torneo
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        $sql = "

        UPDATE {$this->table}

        SET

            nombre = ?,

            descripcion = ?,

            fecha_inicio = ?,
            fecha_fin = ?,

            estado = ?,

            ubicacion = ?,

            tipo = ?,

            premio = ?,
            logo = ?,
            capacidad_maxima = ?,
            reglas = ?

        WHERE id_torneo = ?

        ";

        return $this->execute(

            $sql,

            [

                $data["nombre"],

                $data["descripcion"],

                $data["fecha_inicio"],
                $data["fecha_fin"],

                $data["estado"],

                $data["ubicacion"],

                $data["tipo"],

                $data["premio"],
                $data["logo"],
                $data["capacidad_maxima"],
                $data["reglas"]
                $id

            ],

            "sssssssssisi"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar torneo
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $sql = "

        DELETE FROM {$this->table}

        WHERE id_torneo = ?

        ";

        return $this->execute(

            $sql,

            [$id],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar torneo
    |--------------------------------------------------------------------------
    */

    public function search($search){

        $search = "%{$search}%";

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE

            nombre LIKE ?
            OR descripcion LIKE ?
            OR ubicacion LIKE ?
            OR tipo LIKE ?

        ORDER BY id_torneo DESC

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
    | Obtener activos
    |--------------------------------------------------------------------------
    */

    public function getActivos(){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE estado = 'activo'

        ORDER BY fecha_inicio ASC

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

        ORDER BY fecha_fin DESC

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

        WHERE fecha_inicio >= NOW()

        ORDER BY fecha_inicio ASC

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener por nombre
    |--------------------------------------------------------------------------
    */

    public function getByNombre($nombre){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE nombre = ?

        LIMIT 1

        ";

        return $this->fetch(

            $sql,

            [$nombre],

            "s"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener participantes
    |--------------------------------------------------------------------------
    */

    public function getParticipantes($id_torneo){

        $sql = "

        SELECT *

        FROM torneo_participantes

        WHERE id_torneo = ?

        ";

        return $this->fetchAll(

            $sql,

            [$id_torneo],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener arenas
    |--------------------------------------------------------------------------
    */

    public function getArenas($id_torneo){

        $sql = "

        SELECT *

        FROM arenas

        WHERE id_torneo = ?

        ";

        return $this->fetchAll(

            $sql,

            [$id_torneo],

            "i"

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