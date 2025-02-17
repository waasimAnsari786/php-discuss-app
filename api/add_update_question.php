<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../classes/question.class.php'; // Include the Question class
require_once __DIR__ . '/../utils/validation.utils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
  $data = json_decode(file_get_contents("php://input"), true);

  // Validate question data (you can implement a similar validation function)
  $errors = validateData($data, ['question', 'question_description']); // Assuming this function exists

  // Check for validation errors
  if (!empty($errors)) {
    $apiError = new ApiError(400, 'Validation errors', $errors);
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
    exit;
  }

  // Proceed with question creation or update logic using the Question class
  try {
    $question = new Question($conn);
    $result = $question->ask_update_question($data); // Use the method to ask/update question

    // Check if the result has success value true
    if ($result['success']) {
      // Prepare the response based on the request method
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $apiResponse = new ApiResponse(201, $result['question'], 'Question created successfully');
      } else {
        $apiResponse = new ApiResponse(200, $result['question'], 'Question updated successfully');
      }

      http_response_code($apiResponse->statusCode);
      echo json_encode($apiResponse->toArray());
    } else {
      $apiError = new ApiError(400, $result['message']);
      http_response_code($apiError->statusCode);
      echo json_encode($apiError->toArray());
    }
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
