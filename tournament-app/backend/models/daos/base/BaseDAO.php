<?php

class BaseDAO {

    protected $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    /**
     * Ejecutar SELECT múltiples filas
     */
    protected function fetchAll($sql, $params = [], $types = "") {

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error prepare: " . $this->connection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();

        return $data;
    }

    /**
     * Ejecutar SELECT una fila
     */
    protected function fetch($sql, $params = [], $types = "") {

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error prepare: " . $this->connection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $data = $result->fetch_assoc();

        $stmt->close();

        return $data;
    }

    /**
     * INSERT / UPDATE / DELETE
     */
    protected function execute($sql, $params = [], $types = "") {

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error prepare: " . $this->connection->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $success = $stmt->execute();

        if (!$success) {
            throw new Exception("Error execute: " . $stmt->error);
        }

        $insertId = $stmt->insert_id;

        $stmt->close();

        return $insertId ?: true;
    }
}

?>