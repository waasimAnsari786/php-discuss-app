<?php
session_start(); // Start the session
require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../utils/api_error.utils.php';
require_once __DIR__ . '/../utils/api_response.utils.php';
require_once __DIR__ . '/../classes/category.class.php'; // Include the Category class
require_once __DIR__ . '/../utils/validation.utils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
  $data = json_decode(file_get_contents("php://input"), true);

  // Validate category data
  $errors = validateData($data, ['category_name', 'category_description']);

  // Check for validation errors
  if (!empty($errors)) {
    $apiError = new ApiError(400, 'Validation errors', $errors);
    http_response_code($apiError->statusCode);
    echo json_encode($apiError->toArray());
    exit;
  }

  // Proceed with category creation or update logic using the Category class
  try {
    $category = new Category($conn);
    $resultCategory = $category->add_update_category($data); // Use the method to add/update category

    // Determine if it was a create or update operation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $apiResponse = new ApiResponse(201, $resultCategory, 'Category created successfully');
    } else {
      $apiResponse = new ApiResponse(200, $resultCategory, 'Category updated successfully');
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
