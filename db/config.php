<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', null);
define('DB_NAME', 'discuss_app');

class Database
{
  private $conn;
  public function __construct()
  {
    $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($this->conn->connect_error) {
      die("Database connection failed error: " . $this->conn->connect_error);
    }
  }
  public function getConnection()
  {
    return $this->conn;
  }
}
