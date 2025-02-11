<?php
include __DIR__ . "/../db/config.php";

class Auth
{
  private $userData;
  private $conn;

  // Constructor
  public function __construct($userData, $conn)
  {
    $this->userData = $userData;
    $this->conn = $conn;
  }

  // Signup Method
  public function signup()
  {
    // Hash password
    $this->userData['password'] = password_hash($this->userData['password'], PASSWORD_DEFAULT);

    // Remove 'signup' key if exists
    unset($this->userData['signup']);

    // Extract column names dynamically
    $columns = implode(", ", array_keys($this->userData));  // `name`, `email`, `password`
    $placeholders = implode(", ", array_fill(0, count($this->userData), "?")); // ?, ?, ?

    // Generate ON DUPLICATE KEY UPDATE clause
    $updateFields = [];
    foreach (array_keys($this->userData) as $key) {
      $updateFields[] = "`$key` = VALUES(`$key`)";
    }
    $updateQuery = implode(", ", $updateFields);

    // Final SQL Query
    $signup_query = "INSERT INTO users ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateQuery";

    // Prepare the statement
    $stmt = $this->conn->prepare($signup_query);

    // Bind parameters dynamically
    $types = str_repeat("s", count($this->userData)); // Assuming all are strings, adjust for integers
    $stmt->bind_param($types, ...array_values($this->userData));

    // Execute the query
    if ($stmt->execute()) {
      // Get the last inserted user ID
      $last_id = $this->conn->insert_id;
      // push user_id in this array
      $this->userData['id'] = $last_id;
      // Set the session variables
      $_SESSION['user_data'] = $this->userData;
      header('location:/discuss-app');
    } else {
      echo 'New user not registered: ' . $this->conn->error;
    }

    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }

  public function login()
  {
    // Final SQL Query
    $login_query = "SELECT * FROM users WHERE email=?";
    $stmt = $this->conn->prepare($login_query);
    $stmt->bind_param('s', $this->userData['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($this->userData['password'], $user['password'])) {
        $_SESSION['user_data'] = $user;
        header('location:/discuss-app');
      } else {
        echo "Incorrect password";
      }
    }

    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }
}
