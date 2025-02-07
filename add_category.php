<?php
include './inc/header.php';
$user_id = $_SESSION['id'];

// Check if 'edit_category' is set in the URL
$edit_category = isset($_GET['edit_category']) ? $_GET['edit_category'] : null;

if ($edit_category) {
  $edit_query = "SELECT * FROM categories WHERE category_name = '$edit_category'";
  $edit_stmt = $conn->query($edit_query);
  $edit_result = $edit_stmt->fetch_assoc();
}
?>

<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="category_name" class="form-label">Category Name</label>
    <input type="text" class="form-control" name="category_name" value="<?php echo isset($_GET['edit_category']) ? $edit_result['category_name'] : null; ?>" placeholder="Your Category">
  </div>
  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" name="category_description" placeholder="Category Description"><?php echo isset($_GET['edit_category']) ? $edit_result['category_description'] : null; ?></textarea>
  </div>
  <div class="mb-3">
    <label for="parent_category_id" class="form-label">Parent Category</label>
    <select class="form-control" name="parent_category_id">
      <option value="null" selected>None</option>
      <?php
      $categories = $conn->query("SELECT * FROM categories");
      foreach ($categories as $category) {
        if ($category['category_name'] == $edit_result['parent_category']) {
          echo "<option value='" . $category['id'] . "' selected>" . $category['category_name'] . "</option>";
        } else {
          echo "<option value='" . $category['id'] . "'>" . $category['category_name'] . "</option>";
        }
      }
      ?>
    </select>

  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="<?php echo $edit_category ? 'update_category' : 'add_category'; ?>" value="<?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <?php if ($edit_category) { ?>
      <input type="hidden" name="category_id" value="<?php echo $edit_result['id']; ?>">
    <?php } ?>
  </div>
</form>

<?php include './inc/footer.php' ?>