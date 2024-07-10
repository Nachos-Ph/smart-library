<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown ml-3">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-circle mr-1"></i> User
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="signup.php">Sign Up</a>
                    <a class="dropdown-item" href="login.php">Log In</a>
                    <a class="dropdown-item" href="logout.php">Log Out</a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <a class="navbar-brand ml-5 mb-3" href="index.php?page=home"><img src="../images/bookicon.png" alt="logo" id="logo"></a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=home"><i class="fas fa-solid fa-house-user mr-3"></i>Home</a>
            </li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=statistics"><i class="fas fa-chart-bar mr-3"></i>Statistics</a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=books"><i class="fas fa-solid fa-book mr-3"></i>Books</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=users"><i class="fas fa-solid fa-users mr-3"></i>Users</a>
            </li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        include($page . '.php');
        ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
