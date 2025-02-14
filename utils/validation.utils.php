<?php

function validateUserData($data)
{
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
      if (!empty($data[$field]) && !$rule['filter']($data[$field])) {
        $errors[] = $rule['message'];
      }
    }
  }
  return $errors;
}

function validateData($data, $required_fields)
{
  $errors = [];

  // Check if all required fields are present
  foreach ($required_fields as $field) {
    if (empty($data[$field])) {
      $errors[] = "The $field field is required";
    }
  }

  return $errors; // Return an array of errors (empty if valid)
}
