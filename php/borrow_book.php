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
    $query = "SELECT book_id, status FROM books WHERE title = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $book_title);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $book_id = $book['book_id'];
        $book_status = $book['status'];

        if ($book_status == 'available') {
            // Check if the user has already borrowed 2 books
            $query = "SELECT COUNT(*) as count FROM borrowed_books WHERE user_id = ? AND status = 'borrowed'";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];

            if ($count < 2) {
                // Initialize borrow_date and due_date variables
                $borrow_date = date('Y-m-d');
                $due_date = date('Y-m-d', strtotime('+1 week'));

                $query = "INSERT INTO borrowed_books (book_id, user_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iiss", $book_id, $user_id, $borrow_date, $due_date);
                if ($stmt->execute()) {
                    // Update book status to 'borrowed'
                    $query = "UPDATE books SET status = 'borrowed' WHERE book_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('i', $book_id);
                    $stmt->execute();

                    $success = "Book borrowed successfully.";
                } else {
                    $error = "Error borrowing book.";
                }
            } else {
                $error = "You have already borrowed 2 books.";
            }
        } else {
            $error = "Book is not available for borrowing.";
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
    <title>Borrow Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Borrow Book</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="index.php?page=borrow_book" method="post">
            <div class="form-group">
                <label for="book_title">Book Title:</label>
                <input type="text" class="form-control" id="book_title" name="book_title" required>
            </div>
            <button type="submit" class="btn btn-primary">Borrow</button>
        </form>
    </div>

    <script>
        $(function() {
            $("#book_title").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "index.php?page=borrow_book",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 1
            });
        });
    </script>
</body>
</html>
