<?php
include(__DIR__ . "/../db/config.php");
session_start();

if (isset($_POST['submit'])) {
  // Filter out the 'submit' key
  $filtered_keys = array_diff(array_keys($_POST), ['submit']);
  $filtered_vals = array_diff(array_values($_POST), ['Sign Up']);

  // Ensure values are properly wrapped in single quotes for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return "'" . $conn->real_escape_string($val) . "'";
  }, $filtered_vals);

  //split the keys into columns
  $columns = implode(', ', $filtered_keys);

  //create placeholders(?) for the values
  $placeholders = implode(', ', array_fill(0, count($filtered_keys), '?'));

  //create the query
  $user_query = "INSERT INTO users ($columns) VALUES ($placeholders)";

  // Use proper prepared statement
  $stmt = $conn->prepare($user_query);

  // Dynamically bind parameters to the query
  $types = str_repeat('s', count($filtered_vals));
  $stmt->bind_param($types, ...$escaped_vals);

  if ($stmt->execute()) {
    //set the session variables
    $_SESSION['userName'] = $_POST['userName'];
    $_SESSION['email'] = $_POST['email'];
    header('location:/discuss-app');
  } else {
    echo 'New user not registered: ' . $conn->error;
  }

  $stmt->close();
} else if (isset($_GET['logout'])) {
  session_destroy();
  header('location:/discuss-app');
} else if (isset($_POST['login'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $login_query = "SELECT * FROM users WHERE email='?' AND password='?'";
  $stmt = $conn->prepare($login_query);
  $stmt->bind_param('ss', $email, $password);
  $result = $conn->query($login_query);

  if ($stmt->execute()) {
    foreach ($result as $row) {
      foreach ($row as $key => $val) {
        if ($key == 'userName' || $key == 'email') {
          $_SESSION[$key] = $val;
        }
      }
    }
    header('location:/discuss-app');
  } else {
    echo "User dosn't exist";
  }
} else if (isset($_POST['add_category'])) {
  print_r($_POST);
};
