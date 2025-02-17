<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../classes/answer.class.php'; // Include the Answer class

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $answer_id = isset($_GET['id']) ? intval($_GET['id']) : null;
  $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : null;

  try {
    $answer = new Answer($conn);
    $result = $answer->get_answers($answer_id, $question_id); // Use the method to get answers

    http_response_code(200);
    echo json_encode(['success' => true, 'data' => $result]);
  } catch (Exception $e) {
    $apiError = new ApiError(500, 'Server error: ' . $e->getMessage());
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
  }
} else {
  $apiError = new ApiError(405, 'Method not allowed');
  http_response_code($apiError->statusCode);
  echo json_encode($apiError->toArray());
  exit;
}
