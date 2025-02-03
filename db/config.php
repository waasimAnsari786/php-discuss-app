<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', null);
define('DB_NAME', 'discuss_app');


$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
  die('Database connectinf error: ' . $conn->connect_error);
}
