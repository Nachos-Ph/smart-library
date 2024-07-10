<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!$_SESSION['is_admin']){
    header("Location: index.php?page=home");
    exit;
}

include 'config.php';

$total_books = 0;
$books_borrowed = 0;
$books_available = 0;
$archived_books = 0;

// Calculate Total Books
$sql_total_books = "SELECT COUNT(*) AS total FROM books";
$result_total_books = $conn->query($sql_total_books);
if ($result_total_books->num_rows > 0) {
    $row = $result_total_books->fetch_assoc();
    $total_books = $row['total'];
}
$result_total_books->close();

// Calculate Books Borrowed
$sql_books_borrowed = "SELECT COUNT(*) AS borrowed FROM borrowed_books";
$result_books_borrowed = $conn->query($sql_books_borrowed);
if ($result_books_borrowed->num_rows > 0) {
    $row = $result_books_borrowed->fetch_assoc();
    $books_borrowed = $row['borrowed'];
}
$result_books_borrowed->close();

// Calculate Books Available
$sql_books_available = "SELECT COUNT(*) AS available FROM books WHERE status = 'available'";
$result_books_available = $conn->query($sql_books_available);
if ($result_books_available->num_rows > 0) {
    $row = $result_books_available->fetch_assoc();
    $books_available = $row['available'];
}
$result_books_available->close();

// Calculate Archived Books
$sql_archived_books = "SELECT COUNT(*) AS archived FROM books WHERE status = 'archived'";
$result_archived_books = $conn->query($sql_archived_books);
if ($result_archived_books->num_rows > 0) {
    $row = $result_archived_books->fetch_assoc();
    $archived_books = $row['archived'];
}
$result_archived_books->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Statistics</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="ml-5 mb-5 mt-5">Library Statistics</h2>
        <div class="row ml-5">
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Books</h5>
                        <p class="card-text"><?php echo $total_books; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Books Borrowed</h5>
                        <p class="card-text"><?php echo $books_borrowed; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Books Available</h5>
                        <p class="card-text"><?php echo $books_available; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Archived Books</h5>
                        <p class="card-text"><?php echo $archived_books; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
