<?php
include './inc/header.php';
$edit_question = isset($_GET['edit_question']) ? $_GET['edit_question'] : null;

if ($edit_question) {
  $edit_query = "SELECT * FROM questions WHERE question = '$edit_question'";
  $edit_stmt = $conn->query($edit_query);
  $edit_result = $edit_stmt->fetch_assoc();
}

?>

<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="question" class="form-label">Question</label>
    <input type="text" class="form-control" name="question" value="<?php echo isset($_GET['edit_question']) ? $edit_result['question'] : null; ?>" placeholder="Your Question">
  </div>
  <div class="mb-3">
    <label for="question_description" class="form-label">Description</label>
    <textarea name="question_description" class="form-control" placeholder="Question Description"><?php echo isset($_GET['edit_question']) ? $edit_result['question_description'] : null; ?></textarea>
  </div>
  <div class="mb-3">
    <label for="question_category_id" class="form-label">Question Category</label>
    <select class="form-control" name="question_category_id">
      <option value="0" selected>None</option>
      <?php
      $categories = $conn->query("SELECT * FROM categories");
      foreach ($categories as $category) {
        if ($category['category_name'] == $edit_result['question_category']) {
          echo "<option value='" . $category['id'] . "' selected>" . $category['category_name'] . "</option>";
        } else {
          echo "<option value='" . $category['id'] . "'>" . $category['category_name'] . "</option>";
        }
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="<?php echo isset($_GET['edit_question']) ? 'update_question' : 'ask_question' ?>" value="<?php echo isset($_GET['edit_question']) ? 'Update Question' : 'Ask Question' ?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['id'] ?>">
    <?php if (isset($_GET['edit_question'])): ?>
      <input type="hidden" name="question_id" value="<?php echo $edit_result['id']; ?>">
    <?php endif; ?>
  </div>



</form>

<?php include './inc/footer.php' ?>