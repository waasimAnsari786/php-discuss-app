<?php include './inc/header.php'; ?>

<div class="row">
  <?php if (isset($_SESSION['id'])): ?>
    <?php
    $categery_query = "SELECT * FROM categories WHERE user_id =" . $_SESSION['id'];
    $stmt = $conn->query($categery_query);
    $categories = $stmt->fetch_all(MYSQLI_ASSOC);
    ?>
    <?php if ($categories): ?>
      <?php foreach ($categories as $category) { ?>
        <div class="col-3 mt-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo $category['category_name']; ?></h5>
              <p class="card-text"><?php echo $category['category_description']; ?></p>
              <p class="card-text">Parent Category: <?php echo $category['parent_category'] ? $category['parent_category'] : 'None'; ?></p>
              <p class="card-text">Created At: <?php echo $category['date']; ?></p>
              <a href="add_category.php?edit_category=<?php echo $category['category_name']; ?>" class="btn btn-primary">Edit</a>
              <a href="server/formHandling.php?delete_category=<?php echo $category['category_name']; ?>" class="btn btn-danger">Delete</a>
            </div>
          </div>
        </div>
      <?php } ?>
    <?php else: ?>
      <p>No categories found</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include './inc/footer.php'; ?>