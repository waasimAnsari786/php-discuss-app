<?php include './inc/header.php'; ?>

<div class="row">
  <?php if (isset($_SESSION['id'])): ?>
    <?php
    $question_query = "SELECT * FROM questions WHERE user_id =" . $_SESSION['id'];
    $stmt = $conn->query($question_query);
    $questions = $stmt->fetch_all(MYSQLI_ASSOC);
    ?>
    <?php if ($questions): ?>
      <?php foreach ($questions as $question) { ?>
        <div class="col-3 mt-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo $question['question']; ?></h5>
              <p class="card-text"><?php echo $question['question_description']; ?></p>
              <p class="card-text">Question Category: <?php echo $question['question_category'] ? $question['question_category'] : 'None'; ?></p>
              <p class="card-text">Created At: <?php echo $question['date']; ?></p>
              <a href="add_question.php?edit_question=<?php echo $question['question']; ?>" class="btn btn-primary">Edit</a>
              <a href="server/formHandling.php?delete_question=<?php echo $question['question']; ?>" class="btn btn-danger">Delete</a>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php else: ?>
      <p>No questions found</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include './inc/footer.php'; ?>