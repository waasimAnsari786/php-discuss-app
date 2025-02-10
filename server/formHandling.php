<?php
include(__DIR__ . "/../db/config.php");
session_start();

$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : '';

if (isset($_POST['submit'])) {
  // Filter out the 'submit' key
  $filtered_keys = array_diff(array_keys($_POST), ['submit']);
  $filtered_vals = array_diff(array_values($_POST), ['Sign Up']);

  // Ensure values are properly escaped for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return $conn->real_escape_string($val);
  }, $filtered_vals);

  // Split the keys into columns
  $columns = implode(', ', $filtered_keys);

  // Create placeholders (?) for the values
  $placeholders = implode(', ', array_fill(0, count($filtered_keys), '?'));

  // Create the query
  $user_query = "INSERT INTO users ($columns) VALUES ($placeholders)";

  // Use proper prepared statement
  $stmt = $conn->prepare($user_query);

  // Dynamically bind parameters to the query
  $types = str_repeat('s', count($filtered_vals));
  $stmt->bind_param($types, ...$escaped_vals);

  if ($stmt->execute()) {
    // Get the last inserted user ID
    $last_id = $conn->insert_id;

    // Set the session variables
    $_SESSION['userName'] = $_POST['userName'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['id'] = $last_id; // Store the user ID in the session

    header('location:/discuss-app');
  } else {
    echo 'New user not registered: ' . $conn->error;
  }

  $stmt->close();
} else if (isset($_GET['logout'])) {
  session_destroy();
  header('location:/discuss-app');
} else if (isset($_POST['login'])) {
  $email = $conn->real_escape_string($_POST['email']);
  $password = $conn->real_escape_string($_POST['password']);

  $login_query = "SELECT * FROM users WHERE email=? AND password=?";
  $stmt = $conn->prepare($login_query);
  $stmt->bind_param('ss', $email, $password);

  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    foreach ($result as $row) {
      foreach ($row as $key => $val) {
        if ($key == 'userName' || $key == 'email' || $key == 'id') {
          $_SESSION[$key] = $val;
        }
      }
    }
    header('location:/discuss-app');
  } else {
    echo "User doesn't exist";
  }

  $stmt->close();
} else if (isset($_POST['add_category'])) {
  // Filter out the 'add_category' key
  $filtered_keys = array_diff(array_keys($_POST), ['add_category']);
  $filtered_vals = array_diff(array_values($_POST), ['Add Category']);

  // Ensure values are properly escaped for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return empty($val) ? NULL : $conn->real_escape_string($val);
  }, $filtered_vals);

  // Split the keys into columns
  $columns = implode(', ', $filtered_keys);

  // Create placeholders for the values
  $placeholders = implode(', ', array_fill(0, count($filtered_keys), '?'));

  // Create the query
  $user_query = "INSERT INTO categories ($columns) VALUES ($placeholders)";

  // Use prepared statement
  $stmt = $conn->prepare($user_query);

  // Dynamically bind parameters to the query
  $types = str_repeat('s', count($filtered_vals)); // Assuming all are strings
  $stmt->bind_param($types, ...$escaped_vals);

  if ($stmt->execute()) {
    header("Location: /discuss-app/all_categories.php?user_id=$user_id");
  } else {
    echo 'New category not added: ' . $conn->error;
  }

  $stmt->close();
} else if (isset($_GET['delete_category_id'])) {
  $delete_category_id = $_GET['delete_category_id'];
  $delete_query = "DELETE FROM categories WHERE id = '$delete_category_id'";
  $conn->query($delete_query);
  header("Location: /discuss-app/all_categories.php?user_id=$user_id");
} else if (isset($_POST['update_category'])) {
  // Filter out the 'submit' key
  $filtered_keys = array_diff(array_keys($_POST), ['update_category', 'category_id']);
  $filtered_vals = array_diff(array_values($_POST), ['Update Category', $_POST['category_id']]);

  // Ensure values are properly wrapped in single quotes for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return empty($val) ? NULL : $conn->real_escape_string($val);
  }, $filtered_vals);

  echo '<br>';
  var_dump($escaped_vals);
  echo '<br>';

  $update_fields = [];
  foreach ($filtered_keys as $index => $column) {
    $update_fields[] = "$column = ?";
  }
  $set_clause = implode(', ', $update_fields); // Convert to SQL format: category_name=?, category_description=?, ...

  // Prepare query
  $query = "UPDATE categories SET $set_clause WHERE id =" . $_POST['category_id'];
  $stmt = $conn->prepare($query);

  // Bind parameters dynamically
  $param_types = str_repeat("s", count($escaped_vals)); // All values are strings except ID (integer)

  $stmt->bind_param($param_types, ...$escaped_vals);

  if ($stmt->execute()) {
    header("Location: /discuss-app/all_categories.php?user_id=$user_id");
  } else {
    echo "Error updating record: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
} else if (isset($_POST['ask_question'])) {
  // Filter out the 'submit' key
  $filtered_keys = array_diff(array_keys($_POST), ['ask_question']);
  $filtered_vals = array_diff(array_values($_POST), ['Ask Question']);

  // Ensure values are properly wrapped in single quotes for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return $conn->real_escape_string($val);
  }, $filtered_vals);

  //split the keys into columns
  $columns = implode(', ', $filtered_keys);

  //create placeholders(?) for the values
  $placeholders = implode(', ', array_fill(0, count($filtered_keys), '?'));

  // create the query
  $question_query = "INSERT INTO questions ($columns) VALUES ($placeholders)";

  //  Use proper prepared statement
  $stmt = $conn->prepare($question_query);

  // Dynamically bind parameters to the query
  $types = str_repeat('s', count($filtered_vals));
  $stmt->bind_param($types, ...$escaped_vals);

  if ($stmt->execute()) {
    header('location:/discuss-app/all_questions.php');
  } else {
    echo 'New category not added: ' . $conn->error;
  }

  $stmt->close();
} else if (isset($_GET['delete_question'])) {
  $delete_question = $_GET['delete_question'];
  $delete_query = "DELETE FROM questions WHERE question = '$delete_question'";
  $conn->query($delete_query);
  header('location:/discuss-app/my_questions.php');
} else if (isset($_POST['update_question'])) {
  // Filter out the 'submit' key
  $filtered_keys = array_diff(array_keys($_POST), ['update_question', 'question_id']);
  $filtered_vals = array_diff(array_values($_POST), ['Update Question', $_POST['question_id']]);

  $escaped_vals = array_map(function ($val) use ($conn) {
    return $conn->real_escape_string($val);
  }, $filtered_vals);


  $update_fields = [];
  foreach ($filtered_keys as $index => $column) {
    $update_fields[] = "$column = ?";
  }
  $set_clause = implode(', ', $update_fields); // Convert to SQL format: question=?, question_description=?, ...

  // Prepare query
  $query = "UPDATE questions SET $set_clause WHERE id =" . $_POST['question_id'];
  $stmt = $conn->prepare($query);

  // Bind parameters dynamically
  $param_types = str_repeat("s", count($filtered_vals)); // All values are strings except ID (integer)

  $stmt->bind_param($param_types, ...$escaped_vals);

  if ($stmt->execute()) {
    header('location:/discuss-app/my_questions.php');
  } else {
    echo "Error updating record: " . $conn->error;
  }

  $stmt->close();
  $conn->close();
} else if (isset($_POST['answer_question'])) {
  // Filter out the 'add_category' key
  $filtered_keys = array_diff(array_keys($_POST), ['answer_question']);
  $filtered_vals = array_diff(array_values($_POST), ['Answer Question']);

  // Ensure values are properly wrapped in single quotes for SQL
  $escaped_vals = array_map(function ($val) use ($conn) {
    return $conn->real_escape_string($val);
  }, $filtered_vals);

  //split the keys into columns
  $columns = implode(', ', $filtered_keys);

  //create placeholders(?) for the values
  $placeholders = implode(', ', array_fill(0, count($filtered_keys), '?'));

  //create the query
  $answer_query = "INSERT INTO answers ($columns) VALUES ($placeholders)";

  // Use proper prepared statement
  $stmt = $conn->prepare($answer_query);

  // Dynamically bind parameters to the query
  $types = str_repeat('s', count($filtered_vals));
  $stmt->bind_param($types, ...$escaped_vals);

  if ($stmt->execute()) {
    header('location:/discuss-app/detail_question.php');
  } else {
    echo 'New category not added: ' . $conn->error;
  }

  $stmt->close();
}
