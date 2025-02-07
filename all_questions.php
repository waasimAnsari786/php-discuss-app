<?php include './inc/header.php';

$question_query = "SELECT * FROM questions";
$stmt = $conn->query($question_query);
$questions = $stmt->fetch_all(MYSQLI_ASSOC);

?>



<div class="row">
  <?php foreach ($questions as $question) { ?>
    <div class="col-3 mt-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title"><?php echo $question['question']; ?></h5>
          <p class="card-text"><?php echo $question['question_description']; ?></p>
          <p class="card-text">Question Category: <?php echo $question['question_category_id'] ? $question['question_category_id'] : 'None'; ?></p>
          <p class="card-text">Created At: <?php echo $question['date']; ?></p>
          <a href="detail_question.php?question_id=<?php echo $question['id']; ?>" class="btn btn-primary">View</a>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<?php include './inc/footer.php'; ?>