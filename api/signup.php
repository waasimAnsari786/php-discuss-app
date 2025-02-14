<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../classes/auth.class.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../utils/validation.utils.php'; // Include the validation utility

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  // Validate user data
  $errors = validateUserData($data);

  // Check for validation errors
  if (!empty($errors)) {
    $apiError = new ApiError(400, 'Validation errors', $errors);
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
    exit;
  }

  // Proceed with signup logic
  try {
    $auth = new Auth($conn);
    $result = $auth->signup($data);

    if ($result['success']) {
      $_SESSION['user_data'] = $result['user'];
      $apiResponse = new ApiResponse(201, $result['user'], 'User registered successfully');
      http_response_code($apiResponse->statusCode);
      echo json_encode($apiResponse->toArray());
    } else {
      $apiError = new ApiError(500, 'Registration failed', [$result['error']]);
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
