<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is not an admin
if (!$_SESSION['is_admin']) {
    header("Location: index.php?page=user_details");
    exit;
}
?>

<div class="container mt-5 px-5">
    <div class="row ml-5">
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=view_users';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-search fa-2x"></i><i class="fas fa-user fa-2x"></i></p>
                    <h5 class="card-title mt-4">View Users</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=add_user';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-plus fa-2x"></i><i class="fas fa-user fa-2x"></i></p>
                    <h5 class="card-title mt-4">Add User</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=edit_user';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-edit fa-2x"><i class="fas fa-user fa-1x"></i></i></p>
                    <h5 class="card-title mt-4">Edit User</h5>
                </div>
            </div>
        </div>
        <!-- location.href='index.php?page=delete_user'; -->
        <div class="col-md-4 mb-4 books-card" onclick="location.href='index.php?page=user_details';">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="card-text"><i class="fas fa-info-circle fa-2x"><i class="fas fa-user fa-1x"></i></i></p>
                    <h5 class="card-title mt-4">User Details</h5>
                </div>
            </div>
        </div>
    </div>
</div>
