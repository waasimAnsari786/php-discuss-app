<?php
include './db/config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Discuss App</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <div class="container">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="index.php">Home</a>
            </li>
            <!-- Conditionally render Login/Logout & Sign Up links -->
            <?php if (isset($_SESSION['user_data'])): ?>
              <li class="nav-item">
                <a class="nav-link" href="server/formHandling.php?logout=true">Logout (<?php echo $_SESSION['user_data']['userName']; ?>)</a>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Questions
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item" href="/discuss-app/ask_question.php">Ask Question</a></li>
                  <li><a class="dropdown-item" href="/discuss-app/all_questions.php">All Questions</a></li>
                  <li><a class="dropdown-item" href="#">Latest Questions</a></li>
                </ul>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Categories
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item" href="/discuss-app/add_category.php">Add Category</a></li>
                  <li><a class="dropdown-item" href="/discuss-app/all_categories.php">All Categories</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  My Account
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item" href="/discuss-app/all_categories.php?user_id=<?= isset($_SESSION['user_data']) ? $_SESSION['user_data']['id'] : ''; ?>">My Categories</a></li>

                  <li><a class="dropdown-item" href="/discuss-app/all_questions.php?user_id=<?= isset($_SESSION['user_data']) ? $_SESSION['user_data']['id'] : ''; ?>">My Questions</a></li>
                </ul>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="/discuss-app/login.php">Login</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/discuss-app/signup.php">Sign Up</a>
              </li>
            <?php endif; ?>




          </ul>
        </div>

      </div>
    </div>
  </nav>

  <div class="container">