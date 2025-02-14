<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../classes/question.class.php'; // Include the Question class

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
  $rawData = file_get_contents("php://input");
  $data = json_decode($rawData, true);

  // Validate question data (you can implement a similar validation function)
  // $errors = validateQuestionData($data); // Assuming this function exists

  // Check for validation errors
  // if (!empty($errors)) {
  //     $apiError = new ApiError(400, 'Validation errors', $errors);
  //     http_response_code($apiError->statusCode);
  //     echo json_encode($apiError->toArray());
  //     exit;
  // }

  // Proceed with question creation or update logic using the Question class
  try {
    $question = new Question($conn);
    $result = $question->ask_update_question($data); // Use the method to ask/update question

    // Determine if it was a create or update operation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $apiResponse = new ApiResponse(201, $result, 'Question created successfully');
    } else {
      $apiResponse = new ApiResponse(200, $result, 'Question updated successfully');
    }

    http_response_code($apiResponse->statusCode);
    echo json_encode($apiResponse->toArray());
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
