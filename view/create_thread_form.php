<?php
require '../includes/auth.php';
require '../config/db_connection.php';

$categoryId = $_GET['category_id'];
$userId = $_SESSION['user_id']; // Assuming the user ID is stored in session
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Thread</title>
  <link rel="icon" href="../assets/images/icon.png">
  <link rel="stylesheet" href="../assets/css/header.css">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include '../includes/header.php'; ?>

  <div class="container mt-5">
    <h2>Create New Thread</h2>
    <form action="../controller/forum_controller.php?action=createThreadAction" method="post">
      <input type="hidden" name="category_id" value="<?= $categoryId ?>">
      <input type="hidden" name="user_id" value="<?= $userId ?>"> 
      <div class="mb-3">
        <label class="form-label">Thread Title</label>
        <input type="text" class="form-control" name="title" required
      <div class="mb-3">
        <label class="form-label">Initial Post</label>
        <textarea class="form-control" name="content" rows="5" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Create Thread</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>