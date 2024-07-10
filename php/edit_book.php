<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!$_SESSION['is_admin']){
    header("Location: index.php?page=home");
    exit;
}
$error = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publish_date = $_POST['publish_date'];
    $category = $_POST['category'];
    $summary = $_POST['summary'];
    $status = $_POST['status'];

    if (empty($status)) {
        $error = "Please select a status.";
    } else {
        // Update book details in database
        $sql = "UPDATE books SET title = ?, author = ?, published_date = ?, category = ?, summary = ?, status = ? WHERE book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $title, $author, $publish_date, $category, $summary, $status, $book_id);

        if ($stmt->execute()) {
            $success_message = "Book details updated successfully";
            // Re-fetch the updated book details
            $sql = "SELECT * FROM books WHERE book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $book = $result->fetch_assoc();
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
} else {
    $book_id = $_GET['book_id'];

    // Fetch book details from database
    $sql = "SELECT * FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Book</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="index.php?page=edit_book" method="post" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
            <div class="form-group">
                <label for="title">Book Title:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" class="form-control" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="publish_date">Publish Date:</label>
                <input type="date" class="form-control" id="publish_date" name="publish_date" value="<?php echo $book['published_date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo htmlspecialchars($book['category']); ?>" required>
            </div>
            <div class="form-group">
                <label for="summary">Summary:</label>
                <textarea class="form-control" id="summary" name="summary" maxlength="500" required><?php echo htmlspecialchars($book['summary']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="archived" <?php if ($book['status'] == 'archived') echo 'selected'; ?>>Archived</option>
                    <option value="available" <?php if ($book['status'] == 'available') echo 'selected'; ?>>Non-Archived</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cover_image">Cover Image:</label>
                <input type="file" class="form-control-file" id="cover_image" name="cover_image">
                <small>Current cover image: <?php echo htmlspecialchars($book['cover_image_path']); ?></small>
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
