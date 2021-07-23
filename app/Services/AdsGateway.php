<?php

namespace App\Services;


use PDO;
use PDOException;

class AdsGateway
{

    private PDO $db;
    private string $tableName = 'ads';

    /**
     * AdsGateway constructor.
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $id
     * @return array
     */
    public function find(int $id): array
    {
        $statement = "
            SELECT 
                *
            FROM
                $this->tableName
            WHERE `id` = :id
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindValue(':id', $id, SQLITE3_INTEGER);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getRelevant(): array
    {
        $statement = "
            SELECT
                `id`, `text`, `banner`
            FROM
                $this->tableName
            WHERE `limits` > 0
            ORDER BY `price` desc
            LIMIT 1;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC)[0];
            $this->decrementAds($result['id']);
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param int $id
     */
    private function decrementAds(int $id): void
    {
        $statement = "
            UPDATE $this->tableName 
            SET `limits` = `limits` - 1
            WHERE `id` = :id;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->bindValue(':id', $id, SQLITE3_INTEGER);
            $statement->execute();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param array $input
     * @return array
     */
    public function create(array $input): array
    {
        $statement = "
            INSERT INTO $this->tableName
                (`text`, `price`, `limits`, `banner`)
            VALUES
                (:text, :price, :limits, :banner);
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->bindValue(':text', $input['text'], SQLITE3_TEXT);
            $statement->bindValue(':price', $input['price'], SQLITE3_INTEGER);
            $statement->bindValue(':limits', $input['limits'], SQLITE3_INTEGER);
            $statement->bindValue(':banner', $input['banner'], SQLITE3_TEXT);
            $statement->execute();
            $id = $this->db->lastInsertId();
            return array_merge(['id' => (int)$id], $input);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * @param int $id
     * @param array $input
     * @return array
     */
    public function update(int $id, array $input): array
    {
        $statement = "
            UPDATE $this->tableName
            SET 
                `text` = :text,
                `price`  = :price,
                `limits` = :limits,
                `banner` = :banner
            WHERE `id` = :id
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->bindValue(':text', $input['text'], SQLITE3_TEXT);
            $statement->bindValue(':price', $input['price'], SQLITE3_INTEGER);
            $statement->bindValue(':limits', $input['limits'], SQLITE3_INTEGER);
            $statement->bindValue(':banner', $input['banner'], SQLITE3_TEXT);
            $statement->bindValue(':id', $id, SQLITE3_INTEGER);
            $statement->execute();
            return array_merge(['id' => $id], $input);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}
