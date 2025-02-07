<?php
include './inc/header.php';

// Fetch all categories
$categery_query = "SELECT * FROM categories";
$stmt = $conn->query($categery_query);
$categories = $stmt->fetch_all(MYSQLI_ASSOC);

// Create an associative array for quick lookup of category names by ID
$categoryMap = [];
foreach ($categories as $cat) {
  $categoryMap[$cat['id']] = $cat['category_name'];
}

?>

<div class="row">
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
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<?php include './inc/footer.php'; ?>