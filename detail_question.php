<?php
include './inc/header.php';
include './classes/question.class.php';
include './classes/category.class.php';
include './classes/auth.class.php';
include './classes/answer.class.php';
include './utils/create_lookup_map.utils.php';
?>

<?php
$question_id = $_GET['question_id'];
$auth_obj = new Auth($conn);
$question_obj = new Question($conn);
$category_obj = new Category($conn);
$answer_obj = new Answer($conn);

$question = $question_obj->get_questions($question_id)[0];
$category = $category_obj->get_categories($question['question_category_id'])[0];
$answers = $answer_obj->get_answers(null, $question['id']);
$users = $auth_obj->get_users();

$userMap = createLookupMap($users, 'id', 'userName');
?>

<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title"><?php echo $question['question']; ?></h5>
    <p class="card-text"><?php echo $question['question_description']; ?></p>
    <p class="card-text">
      Question Category:
      <?php
      echo $category['category_name'];
      ?>
    </p>
    <p class="card-text">Created At: <?php echo $question['date']; ?></p>

    <form action="server/formHandling.php" method="POST">
      <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
      <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_data']['id']; ?>">
      <h6 for="answer" class="form-label">Drop Your Answer:</h6>
      <textarea name="answer" class="form-control" placeholder="Your Answer"></textarea>
      <input type="submit" class="btn btn-primary mt-3" name="answer_question" value="Answer Question">
    </form>

    <h3 class="mt-5 mb-3">People's Answers</h3>
    <?php foreach ($answers as $answer) : ?>
      <div class="d-flex justify-content-between border-top border-secondary">
        <p><?php echo $answer['answer']; ?></p>
        <p>Answered By: <?php echo $userMap[$answer['user_id']] ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>


<?php include './inc/footer.php' ?>