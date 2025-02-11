<?php
include './inc/header.php';
include './classes/category.class.php';

$user_id =  $_SESSION['user_data']['id'];

$edit_category_id = isset($_GET['edit_category_id']) ? $_GET['edit_category_id'] : null;

$category = new Category($conn);
$categories = $category->get_categories($edit_category_id);

// Check if 'edit_category_id' is set in the URL
if ($edit_category_id) {
  $edit_result = $categories[0];
}
?>


<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="category_name" class="form-label">Category Name</label>
    <input type="text" class="form-control" name="category_name" value="<?php echo isset($_GET['edit_category_id']) ? $edit_result['category_name'] : null; ?>" placeholder="Your Category">
  </div>
  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" name="category_description" placeholder="Category Description"><?php echo isset($_GET['edit_category_id']) ? $edit_result['category_description'] : null; ?></textarea>
  </div>
  <div class="mb-3">
    <label for="parent_category_id" class="form-label">Parent Category</label>
    <select class="form-control" name="parent_category_id">
      <option value="" selected>None</option>
      <?php
      $categories = $category->get_categories();
      foreach ($categories as $category) {
        if ($category['id'] == $edit_result['id']) {
          continue;
        } else if ($category['id'] == $edit_result['parent_category_id']) {
          echo "<option value='" . $category['id'] . "' selected>" . $category['category_name'] . "</option>";
        } else {
          echo "<option value='" . $category['id'] . "'>" . $category['category_name'] . "</option>";
        }
      }
      ?>
    </select>
  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="<?php echo $edit_category_id ? 'update_category' : 'add_category'; ?>" value="<?php echo $edit_category_id ? 'Update Category' : 'Add Category'; ?>">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <?php if ($edit_category_id) { ?>
      <input type="hidden" name="id" value="<?php echo $edit_category_id; ?>">
    <?php } ?>
  </div>
</form>

<?php include './inc/footer.php' ?>