<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../classes/category.class.php'; // Include the Category class

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  try {
    $category = new Category($conn);
    $categories = $category->get_categories(); // Use the method to get categories

    $apiResponse = new ApiResponse(200, $categories, 'Categories retrieved successfully');
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
