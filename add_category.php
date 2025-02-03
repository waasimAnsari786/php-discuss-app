<?php
include './inc/header.php';
?>

<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="category" class="form-label">Category Name</label>
    <input type="text" class="form-control" name="category" placeholder="Your Category">
  </div>
  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" name="description" placeholder="Category Description"></textarea>
  </div>
  <div class="mb-3">
    <label for="parent_category" class="form-label">Parent Category</label>
    <select class="form-control" name="parent_category">
      <option value="0">None</option>
      <?php
      $categories = $conn->query("SELECT * FROM categories");
      foreach ($categories as $category) {
        echo "<option value='" . $category['id'] . "'>" . $category['catogName'] . "</option>";
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="add_category" value="Add Category">
  </div>



</form>

<?php include './inc/footer.php' ?>