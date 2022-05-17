<?php

namespace App\Controller;

use PDO;

class PartieController
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }
    /**
     * Create a new user.
     *
     *
     * @return array The new user ID
     */
    public function parties(): array
    {
        $sql = "SELECT * FROM parties;";
        return $this->connection->prepare($sql)->execute();
    }

}