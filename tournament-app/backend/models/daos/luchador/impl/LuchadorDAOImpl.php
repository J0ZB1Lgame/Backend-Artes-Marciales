<?php

/*
|--------------------------------------------------------------------------
| LUCHADOR DAO IMPLEMENTATION
|--------------------------------------------------------------------------
| Budokai Martial Arts Tournament System
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../../base/BaseDAO.php';
require_once __DIR__ . '/../../base/ICRUD.php';

require_once __DIR__ . '/../interfaces/ILuchadorDAO.php';

class LuchadorDAOImpl extends BaseDAO implements ICRUD, ILuchadorDAO {

    /*
    |--------------------------------------------------------------------------
    | Tabla principal
    |--------------------------------------------------------------------------
    */

    private $table = "luchadores";

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

        ORDER BY id_luchador DESC

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

        WHERE id_luchador = ?

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
    | Crear luchador
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

            edad,
            genero,

            categoria,
            peso,

            telefono,
            email,

            victorias,
            derrotas,

            estado

        )

        VALUES

        (

            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?

        )

        ";

        return $this->execute(

            $sql,

            [

                $data["nombre"] ?? '',
                $data["apellido"] ?? '',

                $data["tipo_documento"] ?? '',
                (string)($data["numero_documento"] ?? ''),

                $data["edad"] ?? 0,
                $data["genero"] ?? '',

                $data["categoria"] ?? '',
                $data["peso"] ?? 0,

                (string)($data["telefono"] ?? ''),
                $data["email"] ?? '',

                $data["victorias"] ?? $data["victory"] ?? 0,
                $data["derrotas"] ?? 0,

                $data["estado"] ?? 'activo'

            ],

            "ssssissdssiis"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar luchador
    |--------------------------------------------------------------------------
    */

    public function update($id, $data){

        $updates = [];
        $values = [];
        $types = "";

        // Build dynamic UPDATE query with only provided fields
        if(isset($data["nombre"])){
            $updates[] = "nombre = ?";
            $values[] = $data["nombre"];
            $types .= "s";
        }
        if(isset($data["apellido"])){
            $updates[] = "apellido = ?";
            $values[] = $data["apellido"];
            $types .= "s";
        }
        if(isset($data["tipo_documento"])){
            $updates[] = "tipo_documento = ?";
            $values[] = $data["tipo_documento"];
            $types .= "s";
        }
        if(isset($data["numero_documento"])){
            $updates[] = "numero_documento = ?";
            $values[] = $data["numero_documento"];
            $types .= "s";
        }
        if(isset($data["edad"])){
            $updates[] = "edad = ?";
            $values[] = $data["edad"];
            $types .= "i";
        }
        if(isset($data["genero"])){
            $updates[] = "genero = ?";
            $values[] = $data["genero"];
            $types .= "s";
        }
        if(isset($data["categoria"])){
            $updates[] = "categoria = ?";
            $values[] = $data["categoria"];
            $types .= "s";
        }
        if(isset($data["peso"])){
            $updates[] = "peso = ?";
            $values[] = $data["peso"];
            $types .= "d";
        }
        if(isset($data["telefono"])){
            $updates[] = "telefono = ?";
            $values[] = $data["telefono"];
            $types .= "s";
        }
        if(isset($data["email"])){
            $updates[] = "email = ?";
            $values[] = $data["email"];
            $types .= "s";
        }
        if(isset($data["victorias"])){
            $updates[] = "victorias = ?";
            $values[] = $data["victorias"];
            $types .= "i";
        }
        if(isset($data["derrotas"])){
            $updates[] = "derrotas = ?";
            $values[] = $data["derrotas"];
            $types .= "i";
        }
        if(isset($data["estado"])){
            $updates[] = "estado = ?";
            $values[] = $data["estado"];
            $types .= "s";
        }

        // If no fields to update, return
        if(empty($updates)){
            return true;
        }

        $sql = "UPDATE {$this->table} SET " . implode(", ", $updates) . " WHERE id_luchador = ?";
        $values[] = $id;
        $types .= "i";

        return $this->execute($sql, $values, $types);

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar luchador
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $sql = "

        DELETE FROM {$this->table}

        WHERE id_luchador = ?

        ";

        return $this->execute(

            $sql,

            [$id],

            "i"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar luchador
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
            OR categoria LIKE ?
            OR numero_documento LIKE ?

        ORDER BY id_luchador DESC

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
    | Obtener por categoría
    |--------------------------------------------------------------------------
    */

    public function getByCategoria($categoria){

        $sql = "

        SELECT *

        FROM {$this->table}

        WHERE categoria = ?

        ORDER BY nombre ASC

        ";

        return $this->fetchAll(

            $sql,

            [$categoria],

            "s"

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

        ORDER BY nombre ASC

        ";

        return $this->fetchAll($sql);

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
    | Contar por categoría
    |--------------------------------------------------------------------------
    */

    public function countByCategoria($categoria){

        $sql = "

        SELECT COUNT(*) AS total

        FROM {$this->table}

        WHERE categoria = ?

        ";

        return $this->fetch(

            $sql,

            [$categoria],

            "s"

        );

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener ranking
    |--------------------------------------------------------------------------
    */

    public function getRanking(){

        $sql = "

        SELECT *

        FROM {$this->table}

        ORDER BY victorias DESC

        LIMIT 10

        ";

        return $this->fetchAll($sql);

    }

    /*
    |--------------------------------------------------------------------------
    | Obtener top victorias
    |--------------------------------------------------------------------------
    */

    public function getTopVictorias(){

        return $this->getRanking();

    }

    /*
    |--------------------------------------------------------------------------
    | Validar documento
    |--------------------------------------------------------------------------
    */

    public function documentoExists($documento){

        $sql = "

        SELECT id_luchador

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

}

?>