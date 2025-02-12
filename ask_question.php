<?php
include './inc/header.php';
include './classes/question.class.php';
include './classes/category.class.php';

$user_id = $_SESSION['user_data']['id'];
$edit_question_id = isset($_GET['edit_question_id']) ? $_GET['edit_question_id'] : null;

$category = new Category($conn);
$question = new Question($conn);
$questions = $question->get_questions($edit_question_id);

// Check if 'edit_question_id' is set in the URL
if ($edit_question_id) {
  $edit_result = $questions[0];
}
?>

<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="question" class="form-label">Question</label>
    <input type="text" class="form-control" name="question" value="<?php echo isset($_GET['edit_question_id']) ? $edit_result['question'] : null; ?>" placeholder="Your Question">
  </div>
  <div class="mb-3">
    <label for="question_description" class="form-label">Description</label>
    <textarea class="form-control" name="question_description" placeholder="Question Description"><?php echo isset($_GET['edit_question_id']) ? $edit_result['question_description'] : null; ?></textarea>
  </div>
  <div class="mb-3">
    <label for="question_category_id" class="form-label">Question Category</label>
    <select class="form-control" name="question_category_id">
      <option value="" selected>None</option>
      <?php
      $categories = $category->get_categories();
      foreach ($categories as $category) {
        if ($category['id'] == $edit_result['question_category_id']) {
          echo "<option value='" . $category['id'] . "' selected>" . $category['category_name'] . "</option>";
        } else {
          echo "<option value='" . $category['id'] . "'>" . $category['category_name'] . "</option>";
        }
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="<?php echo $edit_question_id ? 'update_question' : 'ask_question'; ?>" value="<?php echo $edit_question_id ? 'Update Question' : 'Ask Question'; ?>">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <?php if ($edit_question_id) { ?>
      <input type="hidden" name="id" value="<?php echo $edit_question_id; ?>">
    <?php } ?>
  </div>
</form>

<?php include './inc/footer.php' ?>