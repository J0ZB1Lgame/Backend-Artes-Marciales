<?php

/*
|--------------------------------------------------------------------------
| LUCHADOR DAO IMPLEMENTATION
|--------------------------------------------------------------------------
| Budokai Martial Arts Tournament System
|--------------------------------------------------------------------------
| Implementación principal del módulo LUCHADORES
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../../../../core/database.php';

require_once __DIR__ . '/../interfaces/ILuchadorDAO.php';

class LuchadorDAOImpl implements ILuchadorDAO {

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

    private $table = "luchadores";

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
    | Obtener todos
    |--------------------------------------------------------------------------
    */

    public function getAll(){

        $sql = "

        SELECT *

        FROM {$this->table}

        ORDER BY id_luchador DESC

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

            WHERE id_luchador = ?

            LIMIT 1

        ");

        $stmt->bind_param("i",$id);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();

    }

    /*
    |--------------------------------------------------------------------------
    | Crear luchador
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

                edad,
                genero,

                categoria,
                peso,

                telefono,
                email,

                victorias,
                derrotas,

                estado,

                foto,

                fecha_registro

            )

            VALUES

            (

                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, NOW()

            )

        ");

        $stmt->bind_param(

            "ssssissdssiiss",

            $data["nombre"],
            $data["apellido"],

            $data["tipo_documento"],
            $data["numero_documento"],

            $data["edad"],
            $data["genero"],

            $data["categoria"],
            $data["peso"],

            $data["telefono"],
            $data["email"],

            $data["victorias"],
            $data["derrotas"],

            $data["estado"],

            $data["foto"]

        );

        return $stmt->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar luchador
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

                edad = ?,
                genero = ?,

                categoria = ?,
                peso = ?,

                telefono = ?,
                email = ?,

                victorias = ?,
                derrotas = ?,

                estado = ?,

                foto = ?

            WHERE id_luchador = ?

        ");

        $stmt->bind_param(

            "ssssissdssiissi",

            $data["nombre"],
            $data["apellido"],

            $data["tipo_documento"],
            $data["numero_documento"],

            $data["edad"],
            $data["genero"],

            $data["categoria"],
            $data["peso"],

            $data["telefono"],
            $data["email"],

            $data["victorias"],
            $data["derrotas"],

            $data["estado"],

            $data["foto"],

            $id

        );

        return $stmt->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar luchador
    |--------------------------------------------------------------------------
    */

    public function delete($id){

        $stmt = $this->conn->prepare("

            DELETE FROM {$this->table}

            WHERE id_luchador = ?

        ");

        $stmt->bind_param("i",$id);

        return $stmt->execute();

    }

    /*
    |--------------------------------------------------------------------------
    | Buscar luchador
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
                OR categoria LIKE ?
                OR numero_documento LIKE ?

            ORDER BY id_luchador DESC

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
    | Obtener por categoría
    |--------------------------------------------------------------------------
    */

    public function getByCategoria($categoria){

        $stmt = $this->conn->prepare("

            SELECT *

            FROM {$this->table}

            WHERE categoria = ?

            ORDER BY nombre ASC

        ");

        $stmt->bind_param("s",$categoria);

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
    | Obtener activos
    |--------------------------------------------------------------------------
    */

    public function getActivos(){

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
    | Contar por categoría
    |--------------------------------------------------------------------------
    */

    public function countByCategoria($categoria){

        $stmt = $this->conn->prepare("

            SELECT COUNT(*) AS total

            FROM {$this->table}

            WHERE categoria = ?

        ");

        $stmt->bind_param("s",$categoria);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();

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

        $result = $this->conn->query($sql);

        $data = [];

        while($row = $result->fetch_assoc()){

            $data[] = $row;

        }

        return $data;

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
    | Validar documento existente
    |--------------------------------------------------------------------------
    */

    public function documentoExists($documento){

        $stmt = $this->conn->prepare("

            SELECT id_luchador

            FROM {$this->table}

            WHERE numero_documento = ?

            LIMIT 1

        ");

        $stmt->bind_param("s",$documento);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;

    }

}

?>