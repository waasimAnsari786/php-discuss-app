<?php

class Question
{
  private $conn;
  private $question_query;

  // Constructor
  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  // ask or update question method
  public function ask_update_question($questionData)
  {
    // Remove 'ask or update' keys if exist
    if (isset($questionData['ask_question'])) {
      unset($questionData['ask_question']);
    } else {
      unset($questionData['update_question']);
    }

    // Define column types dynamically based on expected data types
    $types = '';

    foreach (array_keys($questionData) as $key) {
      if (empty($questionData[$key])) {
        $types .= 'i'; // Integer
        $questionData[$key] = NULL;
      } else if (in_array($key, ['question_category_id', 'user_id'])) { // Check for integer fields
        $types .= 'i'; // Integer
      } else {
        $types .= 's'; // String
      }
    }

    // Extract column names dynamically
    $columns = implode(", ", array_keys($questionData));
    $placeholders = implode(", ", array_fill(0, count($questionData), "?"));

    // Generate ON DUPLICATE KEY UPDATE clause
    $updateFields = [];
    foreach (array_keys($questionData) as $key) {
      $updateFields[] = "`$key` = VALUES(`$key`)";
    }
    $updateQuery = implode(", ", $updateFields);

    // Final SQL Query
    $question_query = "INSERT INTO questions ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateQuery";

    // Prepare the statement
    $stmt = $this->conn->prepare($question_query);

    // Bind parameters dynamically
    $stmt->bind_param($types, ...array_values($questionData));

    // Execute the query
    if ($stmt->execute()) {
      header("Location: /discuss-app/all_questions.php?user_id=" . $_SESSION['user_data']['id']);
    } else {
      echo 'New question not asked/updated: ' . $this->conn->error;
    }
    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }

  public function get_questions($question_id = null, $user_id = null)
  {
    if ($question_id) {
      $this->question_query = "SELECT * FROM questions WHERE id =" . $question_id;
    } else if ($user_id) {
      $this->question_query = "SELECT * FROM questions WHERE user_id =" . $user_id;
    } else {
      $this->question_query = "SELECT * FROM questions";
    }
    $stmt = $this->conn->query($this->question_query);
    $questions = $stmt->fetch_all(MYSQLI_ASSOC);

    return $questions;
    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }

  public function delete_question($question_id)
  {
    $delete_query = "DELETE FROM questions WHERE id = ?";
    $stmt = $this->conn->prepare($delete_query);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $stmt->close();
    header("Location:/discuss-app/all_questions.php?user_id=" . $_SESSION['user_data']['id']);
  }
}
