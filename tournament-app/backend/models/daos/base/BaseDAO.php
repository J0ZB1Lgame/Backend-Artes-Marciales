<?php

class BaseDAO {
    protected $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }
}

?>