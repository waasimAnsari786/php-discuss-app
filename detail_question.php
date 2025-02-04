<?php include './inc/header.php' ?>

<?php
$question_id = $_GET['question_id'];
$question_query = "SELECT * FROM questions WHERE id = $question_id";
$question_stmt = $conn->query($question_query);
$question = $question_stmt->fetch_assoc();

$answers_query = "SELECT * FROM answers WHERE question_id = $question_id";
$answers_stmt = $conn->query($answers_query);
$answers = $answers_stmt->fetch_all(MYSQLI_ASSOC);
?>

<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title"><?php echo $question['question']; ?></h5>
    <p class="card-text"><?php echo $question['question_description']; ?></p>
    <p class="card-text">Question Category: <?php echo $question['question_category'] ? $question['question_category'] : 'None'; ?></p>
    <p class="card-text">Created At: <?php echo $question['date']; ?></p>

    <form action="server/formHandling.php" method="POST">
      <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
      <input type="hidden" name="user_id" value="<?php echo $_SESSION['id']; ?>">
      <h6 for="answer" class="form-label">Drop Your Answer:</h6>
      <textarea name="answer" class="form-control" placeholder="Your Answer"></textarea>
      <input type="submit" class="btn btn-primary mt-3" name="answer_question" value="Answer Question">
    </form>

    <h3 class="mt-5 mb-3">People's Answers</h3>
    <?php foreach ($answers as $answer) : ?>
      <p class="border-top border-secondary"><?php echo $answer['answer']; ?></p>
    <?php endforeach; ?>
  </div>
</div>


<?php include './inc/footer.php' ?>