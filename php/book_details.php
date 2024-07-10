<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$book_id = isset($_GET['book_id']) ? intval($_GET['book_id']) : 0;
if ($book_id > 0) {
    $sql = "SELECT book_id2, title, author, category, published_date, added_date, cover_image_path, status, summary FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($book_id2, $title, $author, $category, $published_date, $added_date, $cover_image_path, $status, $summary);
        $stmt->fetch();
    } else {
        $error = "Book not found.";
    }
    $stmt->close();
} else {
    $error = "Invalid book ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Book Details</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php else: ?>
            <div class="text-center mb-4">
                <?php if ($cover_image_path): ?>
                    <img src="<?php echo $cover_image_path ?>" class="img-fluid" alt="Book Cover">
                <?php else: ?>
                    <div class="alert alert-warning">Cover image not available.</div>
                <?php endif; ?>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th>Book ID</th>
                    <td><?php echo $book_id2; ?></td>
                </tr>
                <tr>
                    <th>Title</th>
                    <td><?php echo $title; ?></td>
                </tr>
                <tr>
                    <th>Author</th>
                    <td><?php echo $author; ?></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td><?php echo $category; ?></td>
                </tr>
                <tr>
                    <th>Published Date</th>
                    <td><?php echo $published_date; ?></td>
                </tr>
                <tr>
                    <th>Added Date</th>
                    <td><?php echo $added_date; ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo ucfirst($status); ?></td>
                </tr>
                <tr>
                    <th>Summary</th>
                    <td><?php echo $summary; ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
