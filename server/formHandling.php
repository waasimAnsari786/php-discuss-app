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
}
