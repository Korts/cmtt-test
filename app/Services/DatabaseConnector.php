<?php

namespace App\Services;


use PDO;
use PDOException;

class DatabaseConnector
{

    private $dbConnection = null;

    public function __construct()
    {
        $database = $_ENV['DATABASE'] . '.db';

        try {
            $this->dbConnection = new PDO(
                "sqlite:".__DIR__."/$database"
            );
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->dbConnection;
    }
}