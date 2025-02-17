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
      // get user
      $user = $this->get_users($last_id);
      // remove password form user
      $user['password'] = null;
      // Return a success response
      return  ['success' => true, 'user' => $user];
    } else {
      // Return an error response
      return ['success' => false, 'error' => 'New user not registered: ' . $this->conn->error];
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

  // Get users method
  public function get_users($user_id = null)
  {
    // Prepare the SQL query based on whether user_id is provided
    if ($user_id) {
      $user_query = "SELECT * FROM users WHERE id = ?";
      $stmt = $this->conn->prepare($user_query);
      $stmt->bind_param("i", $user_id); // Bind the user_id parameter
    } else {
      $user_query = "SELECT * FROM users";
      $stmt = $this->conn->prepare($user_query);
    }

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are users
    if ($result->num_rows > 0) {
      $users = $result->fetch_all(MYSQLI_ASSOC);
      return $users; // Return the users as an associative array
    } else {
      return []; // Return an empty array if no users found
    }

    // Close statement
    $stmt->close();
  }
}
