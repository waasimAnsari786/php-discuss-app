<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../classes/auth.class.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  // Validate required fields exist
  $requiredFields = array_keys($data);
  $errors = [];

  // Check if all required fields are present
  foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
      $errors[] = "The $field field is required";
    }
  }

  // Only proceed if no missing fields
  if (empty($errors)) {
    // Validate field formats
    $validationRules = [
      'userName' => [
        'filter' => fn($value) => preg_match('/^[a-zA-Z0-9_]{3,20}$/', $value),
        'message' => 'Username must be 3-20 characters (letters, numbers, underscores)'
      ],
      'email' => [
        'filter' => fn($value) => filter_var($value, FILTER_VALIDATE_EMAIL),
        'message' => 'Invalid email format'
      ],
      'password' => [
        'filter' => fn($value) => strlen($value) >= 8,
        'message' => 'Password must be at least 8 characters'
      ]
    ];

    // Run validation checks
    foreach ($validationRules as $field => $rule) {
      if (!$rule['filter']($data[$field])) {
        $errors[] = $rule['message'];
      }
    }
  }

  // Handle validation results
  if (!empty($errors)) {
    $apiError = new ApiError(400, 'Validation errors', $errors);
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
    exit;
  }

  try {
    $auth = new Auth($conn);
    $result = $auth->signup($data);

    if ($result['success']) {
      // Set session variables if needed
      $_SESSION['user_data'] = $result['user'];

      // Check if session is set
      if (isset($_SESSION['user_data'])) {
        $apiResponse = new ApiResponse(201, $result['user'], 'User registered successfully');
        http_response_code($apiResponse->statusCode);
        echo json_encode($apiResponse->toArray());
      } else {
        // Handle case where session is not set
        $apiError = new ApiError(500, 'Session not set after signup');
        http_response_code($apiError->statusCode);
        echo json_encode($apiError->toArray());
      }
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
