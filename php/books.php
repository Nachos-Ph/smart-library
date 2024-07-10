<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Assuming you have a way to check if the user is an admin
$isAdmin = $_SESSION['is_admin']; // Adjust this line based on your actual admin check
?>

<div class="container mt-5 px-5">
    <div class="row ml-5">
        <?php if ($isAdmin): ?>
        <!-- Admin view -->
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=manage_books';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-book fa-3x"></i></p>
                    <h5 class="card-title mt-4">Manage Books</h5>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=view_books';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-book fa-3x"></i></p>
                    <h5 class="card-title mt-4">View Books</h5>
                </div>
            </div>
        </div>
        
        <?php if ($isAdmin): ?>
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=add_book';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-book fa-3x"></i></p>
                    <h5 class="card-title mt-4">Add Book</h5>
                </div>
            </div>
        </div>
        <!-- location.href='index.php?page=fine_management'; -->
        <div class="col-md-4 mb-4 books-card" onclick="">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-dollar-sign fa-3x"></i></p>
                    <h5 class="card-title mt-4">Fine Management</h5>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=return_book';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-undo fa-3x"></i></p>
                    <h5 class="card-title mt-4">Return Book</h5>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=borrow_book';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-solid fa-hand-holding fa-3x"></i></p>
                    <h5 class="card-title mt-4">Borrow Book</h5>
                </div> 
            </div>
        </div>
    </div>
</div>
