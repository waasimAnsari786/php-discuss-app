<?php
include './inc/header.php';
require_once __DIR__ . '/./classes/question.class.php';
require_once __DIR__ . '/./classes/auth.class.php';
require_once __DIR__ . '/./classes/category.class.php';
require_once __DIR__ . '/./utils/create_lookup_map.php';

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$auth = new Auth($conn);
$users = $auth->get_users();

$question = new Question($conn);
$questions = $question->get_questions(null, $user_id);

$category = new Category($conn);
$categories = $category->get_categories();

// Create lookup maps
$userMap = createLookupMap($users, 'id', 'userName');
$categoryMap = createLookupMap($categories, 'id', 'category_name');
?>

<div class="row">
  <?php if ($questions): ?>
    <?php foreach ($questions as $question) { ?>
      <div class="col-3 mt-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo $question['question']; ?></h5>
            <p class="card-text"><?php echo $question['question_description']; ?></p>
            <p class="card-text">Category:
              <?php
              echo isset($categoryMap[$question['question_category_id']])
                ? $categoryMap[$question['question_category_id']]
                : 'None';
              ?>
            </p>
            <p class="card-text">Created At: <?php echo $question['date']; ?></p>
            <p class="card-text">Created By:
              <?php echo isset($userMap[$question['user_id']]) ? $userMap[$question['user_id']] : 'Unknown'; ?>
            </p>
            <?php if ($user_id): ?>
              <a href="ask_question.php?edit_question_id=<?php echo $question['id']; ?>" class="btn btn-primary">Edit</a>
              <a href="server/formHandling.php?delete_question_id=<?php echo $question['id']; ?>" class="btn btn-danger">Delete</a>
            <?php else: ?>
              <a href="detail_question.php?question_id=<?php echo $question['id']; ?>" class="btn btn-primary">View</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php } ?>
  <?php else: ?>
    <p>No questions found</p>
  <?php endif; ?>
</div>

<?php include './inc/footer.php'; ?>