<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../classes/answer.class.php'; // Include the Answer class

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $rawData = file_get_contents("php://input");
  $data = json_decode($rawData, true);

  // Check if ID is provided
  if (empty($data['id'])) {
    $apiError = new ApiError(400, 'Answer ID is required');
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
    exit;
  }

  try {
    $answer = new Answer($conn);
    $result = $answer->delete_answer($data['id']); // Use the method to delete answer

    // Check the result and respond accordingly
    if ($result['success']) {
      $apiResponse = new ApiResponse(200, null, $result['message']);
      http_response_code($apiResponse->statusCode);
      echo json_encode($apiResponse->toArray());
    } else {
      $apiError = new ApiError(404, 'Deletion failed', [$result['message']]);
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
