<?php

class Auth
{

  private $conn;

  // Constructor
  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  // Signup Method
  public function signup($userData)
  {
    // Hash password
    $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

    // Extract column names dynamically
    $columns = implode(", ", array_keys($userData));  // `name`, `email`, `password`
    $placeholders = implode(", ", array_fill(0, count($userData), "?")); // ?, ?, ?

    // Generate ON DUPLICATE KEY UPDATE clause
    $updateFields = [];
    foreach (array_keys($userData) as $key) {
      $updateFields[] = "`$key` = VALUES(`$key`)";
    }
    $updateQuery = implode(", ", $updateFields);

    // Final SQL Query
    $signup_query = "INSERT INTO users ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateQuery";

    // Prepare the statement
    $stmt = $this->conn->prepare($signup_query);

    // Bind parameters dynamically
    $types = str_repeat("s", count($userData)); // Assuming all are strings, adjust for integers
    $stmt->bind_param($types, ...array_values($userData));

    // Execute the query
    if ($stmt->execute()) {
      // Get the last inserted user ID
      $last_id = $this->conn->insert_id;
      // Push user_id in this array
      $userData['id'] = $last_id;
      $userData['password'] = null;
      // Return a success response
      return  ['success' => true, 'user' => $userData];
    } else {
      // Return an error response
      return ['error' => 'New user not registered: ' . $this->conn->error];
    }

    // Close statement
    $stmt->close();
  }

  public function login($userData)
  {
    // Final SQL Query
    $login_query = "SELECT * FROM users WHERE email=?";
    $stmt = $this->conn->prepare($login_query);
    $stmt->bind_param('s', $userData['email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($userData['password'], $user['password'])) {
        $user['password'] = null;
        return ['success' => true, 'user' => $user];
      } else {
        // Return an error response
        return ['success' => false, 'error' => "Invalid login credintials: " . $this->conn->error];
      }
    } else {
      return ['success' => false, 'error' => "User doesn't exist:"];
    }

    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }

  public function get_users()
  {
    // Final SQL Query
    $user_query = "SELECT * FROM users";
    $result = $this->conn->query($user_query);
    // Check if there are users
    if ($result->num_rows > 0) {
      $users = $result->fetch_all(MYSQLI_ASSOC);

      return $users;
    }
  }
}
