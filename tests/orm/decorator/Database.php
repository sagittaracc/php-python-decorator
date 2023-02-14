<?php

namespace Sagittaracc\PhpPythonDecorator\tests\orm\decorator;

use Attribute;
use PDO;
use Sagittaracc\PhpPythonDecorator\PythonDecorator;

#[Attribute]
class Database extends PythonDecorator
{
    private static null|PDO $connection = null;

    function __construct(
        private string $host,
        private string $dbname,
        private string $user,
        private string $pass
    ) {}

    public function wrapper($func, $args)
    {
        if (self::$connection === null) {
            // self::$connection = $this->connect();
        }

        $ar = (new Query('mysql'))->wrapper($func, $args);

        // $rows = $this->query($ar->rawQuery);

        return $ar;
    }

    private function connect()
    {
        return new PDO("mysql:dbname={$this->dbname};host={$this->host}", $this->user, $this->pass);
    }

    private function query($query)
    {
        $sth = self::$connection->prepare($query);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}