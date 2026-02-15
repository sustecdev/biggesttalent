<?php

namespace App\Core;

class Database
{
    private $host = DB_SERVER;
    private $user = DB_USERNAME;
    private $pass = DB_PASSWORD;
    private $dbname = DB_NAME;

    private $dbh;
    private $error;

    private static $instance = null;

    private function __construct()
    {
        $this->dbh = @new \mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->dbh->connect_errno) {
            $this->error = $this->dbh->connect_error;
            error_log('DB Connection Failed: ' . $this->error);
            trigger_error(
                'Failed to connect to MySQL: ' . $this->error,
                E_USER_WARNING
            );
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->dbh;
    }

    // Helper to run query and return result
    public function query($sql)
    {
        return $this->dbh->query($sql);
    }

    public function prepare($sql)
    {
        return $this->dbh->prepare($sql);
    }

    public function escape($string)
    {
        return $this->dbh->real_escape_string($string);
    }
}
