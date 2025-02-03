<?php
include './inc/header.php';

?>

<form action="server/formHandling.php" method="POST">
  <div class="mb-3">
    <label for="question" class="form-label">Question</label>
    <input type="text" class="form-control" name="question" placeholder="Your Question">
  </div>
  <div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <input type="text" class="form-control" name="description" placeholder="Your Description">
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" name="password" placeholder="Your Password">
  </div>

  <div class="mb-3">
    <input type="submit" class="btn btn-primary" name="submit" value="Sign Up">
  </div>



</form>

<?php include './inc/footer.php' ?>