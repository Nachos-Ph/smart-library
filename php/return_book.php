<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_title = $_POST['book_title'];
    $user_id = $_SESSION['user_id']; // Assume user_id is stored in session

    // Fetch the book ID from the title
    $query = "SELECT book_id FROM books WHERE title = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $book_title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $book_id = $book['book_id'];

        // Check if the book is currently borrowed by the user
        $query = "SELECT borrow_id, due_date FROM borrowed_books WHERE book_id = ? AND user_id = ? AND status = 'borrowed' LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $book_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $borrow = $result->fetch_assoc();
            $borrow_id = $borrow['borrow_id'];
            $due_date = $borrow['due_date'];
            $return_date = date('Y-m-d');

            // Calculate fine
            $late_days = (strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24);
            $fine = 0;

            if ($late_days > 0) {
                $fine_rate = 10; // Fine rate per day in PHP
                $fine = floor($late_days) * $fine_rate; // Ensure fine is calculated correctly
            }

            // Determine the fine status
            $fine_status = (int)$fine > 0 ? 'unpaid' : 'N/A';

            // Update borrowed_books table
            $query = "UPDATE borrowed_books SET return_date = ?, fine = ?, status = 'returned' WHERE borrow_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sdi', $return_date, $fine, $borrow_id);
            if ($stmt->execute()) {
                // Update book status to 'available'
                $query = "UPDATE books SET status = 'available' WHERE book_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $book_id);
                $stmt->execute();

                // Add entry to fines table if there is a fine
                if ($fine > 0) {
                    $fine_query = "INSERT INTO fines (user_id, book_id, fine_amount, status) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($fine_query);
                    $stmt->bind_param('iids', $user_id, $book_id, $fine, $fine_status);
                    $stmt->execute();
                }

                $success = "Book returned successfully. Fine: PHP " . number_format($fine, 2);
            } else {
                $error = "Error returning book.";
            }
        } else {
            $error = "This book is not currently borrowed by you.";
        }
    } else {
        $error = "Book not found.";
    }
}

// Fetch book titles for autocomplete
if (isset($_GET['term'])) {
    $term = $_GET['term'] . '%';
    $query = "SELECT title FROM books WHERE title LIKE ? LIMIT 10";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $titles = [];
    while ($row = $result->fetch_assoc()) {
        $titles[] = $row['title'];
    }
    echo json_encode($titles);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Return Book</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="index.php?page=return_book" method="post">
            <div class="form-group">
                <label for="book_title">Book Title:</label>
                <input type="text" class="form-control" id="book_title" name="book_title" required>
            </div>
            <button type="submit" class="btn btn-primary">Return</button>
        </form>
    </div>

    <script>
        $(function() {
            $("#book_title").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "index.php?page=return_book",
                        dataType: "json",
                        data: {
                            term: request.term
                }})}})})