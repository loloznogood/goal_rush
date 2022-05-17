<?php

namespace App\Domain\Team\Repository;

use PDO;

/**
 * Repository.
 */
final class TeamCreatorRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Insert user row.
     *
     * @param array $team
     * @return int The new ID
     */
    public function insertTeam(array $team): int
    {
        $row = [
            'id' => uniqid(),
            'name' => $team['name'],
        ];

        $sql = "INSERT INTO teams SET id=:id,name=:name;";

        $this->connection->prepare($sql)->execute($row);

        return (int)$this->connection->lastInsertId();
    }
}
