<?php
require_once __DIR__ . '/../classes/auth.class.php';
require_once __DIR__ . '/../classes/category.class.php';
require_once __DIR__ . '/../classes/question.class.php';
require_once __DIR__ . '/../classes/answer.class.php';
require_once __DIR__ . '/../db/config.php';

session_start();

$auth = new Auth($conn);
$category = new Category($conn);
$question = new Question($conn);
$answer = new Answer($conn);

if (isset($_POST['signup'])) {
  $auth->signup($_POST);
} else if (isset($_GET['logout'])) {
  session_destroy();
  header('location:/discuss-app');
} else if (isset($_POST['login'])) {
  $auth->login($_POST);
} else if (isset($_POST['add_category']) || isset($_POST['update_category'])) {
  $category->add_update_category($_POST);
} else if (isset($_GET['delete_category_id'])) {
  $category->delete_category($_GET['delete_category_id']);
} else if (isset($_POST['ask_question']) || isset($_POST['update_question'])) {
  $question->ask_update_question($_POST);
} else if (isset($_GET['delete_question_id'])) {
  $question->delete_question($_GET['delete_question_id']);
} else if (isset($_POST['answer_question'])) {
  $answer->add_update_answer($_POST);
  // // Filter out the 'add_category' key
  // $filtered_keys = array_diff(array_keys($_POST), ['answer_question']);
  // $filtered_vals = array_diff(array_values($_POST), ['Answer Question']);

  // // Ensure values are properly wrapped in single quotes for SQL
  // $escaped_vals = array_map(function ($val) use ($conn) {
  //   return $conn->real_escape_string($val);
  // }, $filtered_vals);

  // //split the keys into columns
  // $columns = implode(', ', $filtered_keys);

  // //create placeholders(?) for the values
  // $placeholders = implode(', ', array_fill(0, count($filtered_keys), '?'));

  // //create the query
  // $answer_query = "INSERT INTO answers ($columns) VALUES ($placeholders)";

  // // Use proper prepared statement
  // $stmt = $conn->prepare($answer_query);

  // // Dynamically bind parameters to the query
  // $types = str_repeat('s', count($filtered_vals));
  // $stmt->bind_param($types, ...$escaped_vals);

  // if ($stmt->execute()) {
  //   header('location:/discuss-app/detail_question.php');
  // } else {
  //   echo 'New category not added: ' . $conn->error;
  // }

  // $stmt->close();
}
