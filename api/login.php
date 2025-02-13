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

  // Handle validation results
  if (!empty($errors)) {
    $apiError = new ApiError(400, 'Validation errors', $errors);
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
    exit;
  }

  try {
    $auth = new Auth($conn);
    $result = $auth->login($data); // Assuming login method returns an array with success and user data

    if ($result['success']) {
      // Set session variables if needed
      $_SESSION['user_data'] = $result['user'];

      // Check if session is set
      if (isset($_SESSION['user_data'])) {
        $apiResponse = new ApiResponse(200, $result['user'], 'Login successful');
        http_response_code($apiResponse->statusCode);
        echo json_encode($apiResponse->toArray());
      } else {
        // Handle case where session is not set
        $apiError = new ApiError(500, 'Session not set after login');
        http_response_code($apiError->statusCode);
        echo json_encode($apiError->toArray());
      }
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
