<?php include './inc/header.php' ?>

<form action="server/formHandling.php" method="POST" class="col-12 col-md-6 mx-auto border p-3 border-secondary rounded-3">

  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="text" class="form-control" name="email" placeholder="Your Email">
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" name="password" placeholder="Your Password">
    <input type="submit" class="btn btn-primary mt-3" name="login" value="Login">
  </div>

</form>

<?php include './inc/footer.php' ?>