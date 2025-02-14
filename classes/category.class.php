<?php

class Category
{
  private $conn;
  private $category_query;

  // Constructor
  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  // Add or update category method
  public function add_update_category($categoryData)
  {
    // Define column types dynamically based on expected data types
    $types = '';

    foreach (array_keys($categoryData) as $key) {
      if (empty($categoryData[$key])) {
        $types .= 'i'; // Integer
        $categoryData[$key] = NULL;
      } else if (in_array($key, ['parent_category_id', 'user_id'])) { // Check for integer fields
        $types .= 'i'; // Integer
      } else {
        $types .= 's'; // String
      }
    }

    // Extract column names dynamically
    $columns = implode(", ", array_keys($categoryData));  // `category_name`, `category_description`
    $placeholders = implode(", ", array_fill(0, count($categoryData), "?")); // ?, ?, ?

    // Generate ON DUPLICATE KEY UPDATE clause
    $updateFields = [];
    foreach (array_keys($categoryData) as $key) {
      $updateFields[] = "`$key` = VALUES(`$key`)";
    }
    $updateQuery = implode(", ", $updateFields);

    // Final SQL Query
    $category_query = "INSERT INTO categories ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateQuery";

    // Prepare the statement
    $stmt = $this->conn->prepare($category_query);

    // Bind parameters dynamically
    $stmt->bind_param($types, ...array_values($categoryData));

    // Execute the query
    if ($stmt->execute()) {
      // Get the last inserted or updated category ID
      $categoryId = $this->conn->insert_id ? $this->conn->insert_id : $categoryData['id'];
      // Fetch the created or updated category
      return $this->get_categories($categoryId); // Return the category data
    } else {
      throw new Exception('New category not added/updated: ' . $this->conn->error);
    }

    // Close statement
    $stmt->close();
  }

  public function get_categories($category_id = null, $user_id = null)
  {
    if ($category_id) {
      $this->category_query = "SELECT * FROM categories WHERE id =" . $category_id;
    } else if ($user_id) {
      $this->category_query = "SELECT * FROM categories WHERE user_id =" . $user_id;
    } else {
      $this->category_query = "SELECT * FROM categories";
    }
    $stmt = $this->conn->query($this->category_query);
    $categories = $stmt->fetch_all(MYSQLI_ASSOC);

    return $categories;
    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }

  public function delete_category($category_id)
  {
    $delete_query = "DELETE FROM categories WHERE id = ?";
    $stmt = $this->conn->prepare($delete_query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    // Check if the category was deleted
    if ($stmt->affected_rows > 0) {
      return ['success' => true, 'message' => 'Category deleted successfully.'];
    } else {
      return ['success' => false, 'message' => 'Category not found or could not be deleted.'];
    }

    $stmt->close();
  }
}
