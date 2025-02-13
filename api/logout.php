<?php
session_start(); // Start the session
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../utils/api_error.utils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Destroy the session to log out the user
  session_unset(); // Unset all session variables
  session_destroy(); // Destroy the sessionS

  $apiResponse = new ApiResponse(200, null, 'Logout successful');
  http_response_code($apiResponse->statusCode);
  echo json_encode($apiResponse->toArray());
} else {
  $apiError = new ApiError(405, 'Method not allowed');
  http_response_code($apiError->statusCode);
  echo json_encode($apiError->toArray());
  exit;
}
