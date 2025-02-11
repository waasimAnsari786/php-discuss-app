<?php
include './inc/header.php';


// Fetch all categories
$category_query;
if (isset($_GET['user_id'])) {
  $category_query = "SELECT * FROM categories WHERE user_id =" . $_GET['user_id'];
} else {
  $category_query = "SELECT * FROM categories";
}
$stmt = $conn->query($category_query);
$categories = $stmt->fetch_all(MYSQLI_ASSOC);

// Create an associative array for quick lookup of category names by ID
$categoryMap = [];
foreach ($categories as $cat) {
  $categoryMap[$cat['id']] = $cat['category_name'];
}

?>

<div class="row">
  <?php if ($categories): ?>
    <?php foreach ($categories as $category) { ?>
      <div class="col-3 mt-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo $category['category_name']; ?></h5>
            <p class="card-text"><?php echo $category['category_description']; ?></p>
            <p class="card-text">
              Parent Category:
              <?php
              echo isset($categoryMap[$category['parent_category_id']])
                ? $categoryMap[$category['parent_category_id']]
                : 'None';
              ?>
            </p>
            <p class="card-text">Created At: <?php echo $category['date']; ?></p>
            <?php if (isset($_GET['user_id'])): ?>
              <a href="add_category.php?edit_category_id=<?php echo $category['id']; ?>" class="btn btn-primary">Edit</a>
              <a href="server/formHandling.php?delete_category_id=<?php echo $category['id']; ?>" class="btn btn-danger">Delete</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php } ?>
  <?php else: ?>
    <p>No categories found</p>
  <?php endif; ?>
</div>

<?php include './inc/footer.php'; ?>