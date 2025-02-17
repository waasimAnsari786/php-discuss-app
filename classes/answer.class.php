<?php

class Answer
{
  private $conn;
  private $answer_query;

  // Constructor
  public function __construct($conn)
  {
    $this->conn = $conn;
  }

  // Add or update answer method
  public function add_update_answer($answerData)
  {
    // Define column types dynamically based on expected data types
    $types = '';

    foreach (array_keys($answerData) as $key) {
      if (empty($answerData[$key])) {
        $types .= 'i'; // Integer
        $answerData[$key] = NULL;
      } else if (in_array($key, ['user_id', 'question_id'])) { // Check for integer fields
        $types .= 'i'; // Integer
      } else {
        $types .= 's'; // String
      }
    }

    // Extract column names dynamically
    $columns = implode(", ", array_keys($answerData));
    $placeholders = implode(", ", array_fill(0, count($answerData), "?"));

    // Generate ON DUPLICATE KEY UPDATE clause
    $updateFields = [];
    foreach (array_keys($answerData) as $key) {
      $updateFields[] = "`$key` = VALUES(`$key`)";
    }
    $updateQuery = implode(", ", $updateFields);

    // Final SQL Query
    $answer_query = "INSERT INTO answers ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateQuery";

    // Prepare the statement
    $stmt = $this->conn->prepare($answer_query);

    // Bind parameters dynamically
    $stmt->bind_param($types, ...array_values($answerData));

    // Execute the query
    if ($stmt->execute()) {
      // Get the last inserted or updated answer ID
      $answerId = $this->conn->insert_id ? $this->conn->insert_id : $answerData['id'];
      // Fetch the created or updated answer
      $latestAnswer = $this->get_answers($answerId); // Get the latest answer

      return [
        'success' => true,
        'answer' => $latestAnswer // Return the latest answer as an associative array
      ];
    } else {
      return [
        'success' => false,
        'message' => 'Answer not added/updated: ' . $this->conn->error
      ];
    }

    // Close statement
    $stmt->close();
  }

  public function get_answers($answer_id = null, $question_id = null)
  {
    if ($answer_id) {
      $this->answer_query = "SELECT * FROM answers WHERE id =" . $answer_id;
    } else if ($question_id) {
      $this->answer_query = "SELECT * FROM answers WHERE question_id =" . $question_id;
    } else {
      $this->answer_query = "SELECT * FROM answers";
    }
    $stmt = $this->conn->query($this->answer_query);
    $answers = $stmt->fetch_all(MYSQLI_ASSOC);

    return $answers;
    // Close statement & connection
    $stmt->close();
    $this->conn->close();
  }

  public function delete_answer($answer_id)
  {
    $delete_query = "DELETE FROM answers WHERE id = ?";
    $stmt = $this->conn->prepare($delete_query);
    $stmt->bind_param("i", $answer_id);
    $stmt->execute();

    // Check if the answer was deleted
    if ($stmt->affected_rows > 0) {
      return [
        'success' => true,
        'message' => 'Answer deleted successfully.'
      ];
    } else {
      return [
        'success' => false,
        'message' => 'Answer not found or could not be deleted.'
      ];
    }

    $stmt->close();
  }
}
