<?php
include './inc/header.php';
require_once __DIR__ . '/./classes/category.class.php';
require_once __DIR__ . '/./classes/auth.class.php';
require_once __DIR__ . '/./utils/create_lookup_map.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$auth = new Auth($conn);
$users = $auth->get_users();

$category = new Category($conn);
$categories = $category->get_categories(null, $user_id);

// Usage for categories
$categoryMap = createLookupMap($categories, 'id', 'category_name');

// Usage for users
$userMap = createLookupMap($users, 'id', 'userName');
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
            <p class="card-text">Created By:
              <?php
              echo isset($userMap[$category['user_id']])
                ? $userMap[$category['user_id']]
                : 'None';
              ?></p>
            <?php if ($user_id): ?>
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