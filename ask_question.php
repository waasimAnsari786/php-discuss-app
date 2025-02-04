<?php
include './inc/header.php';

?>

<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="question" class="form-label">Question</label>
    <input type="text" class="form-control" name="question" placeholder="Your Question">
  </div>
  <div class="mb-3">
    <label for="question_description" class="form-label">Description</label>
    <textarea name="question_description" class="form-control" placeholder="Question Description"></textarea>
  </div>
  <div class="mb-3">
    <label for="question_category" class="form-label">Question Category</label>
    <select class="form-control" name="question_category">
      <option value="0">None</option>
      <?php
      $categories = $conn->query("SELECT * FROM categories");
      foreach ($categories as $category) {
        // if ($category['category_name'] == $edit_result['parent_category']) {
        //   echo "<option value='" . $category['category_name'] . "' selected>" . $category['category_name'] . "</option>";
        // } else {
        //   echo "<option value='" . $category['category_name'] . "'>" . $category['category_name'] . "</option>";
        // }
        echo "<option value='" . $category['category_name'] . "'>" . $category['category_name'] . "</option>";
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="ask_question" value="Ask Question">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['id'] ?>">
  </div>



</form>

<?php include './inc/footer.php' ?>