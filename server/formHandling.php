<?php
include(__DIR__ . "/../db/config.php");
session_start();

if (isset($_POST['submit'])) {
  $filtered_keys = array_diff(array_keys($_POST), ['submit']);
  $filtered_vals = array_diff(array_values($_POST), ['Submit']);

  // Ensure values are properly wrapped in single quotes for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return "'" . $conn->real_escape_string($val) . "'";
  }, $filtered_vals);

  $columns = implode(', ', $filtered_keys);
  $values = implode(', ', $escaped_vals);

  echo "Columns: " . $columns . '<br>';
  echo "Values: " . $values . '<br>';

  $user_query = "INSERT INTO users ($columns) VALUES ($values)";

  // Use proper prepared statement
  $stmt = $conn->prepare($user_query);

  if ($stmt->execute()) {
    echo 'New user registered';
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

  $login_query = "SELECT * FROM users WHERE email='$email'";
  $stmt = $conn->prepare($login_query);
  $result = $conn->query($login_query);

  if ($stmt->execute()) {
    foreach ($result as $row) {
      foreach ($row as $key => $val) {
        if ($key == 'userName' || $key == 'email') {
          $_SESSION[$key] = $val;
        }
      }
    }
    echo '<br>Login Successfully <br>';
    header('location:/discuss-app');
  } else {
    echo "User dosn't exist";
  }
};
