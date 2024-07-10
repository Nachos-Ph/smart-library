<?php
include 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!$_SESSION['is_admin']){
    header("Location: index.php?page=home");
    exit;
}
// Function to generate the book ID
function generateBookID($title, $publish_date, $category, $conn) {
    $title_initials = strtoupper(substr($title, 0, 2));
    $publish_date_obj = new DateTime($publish_date);
    $publish_month = strtoupper($publish_date_obj->format('M'));
    $added_day = date('j'); // Get the current day of the month
    $publish_year = $publish_date_obj->format('Y');
    $category_code = strtoupper(substr($category, 0, 3));

    $sql = "SELECT COUNT(*) as count FROM books";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $book_count = str_pad($row['count'] + 1, 5, '0', STR_PAD_LEFT);

    return "{$title_initials}{$publish_month}{$added_day}{$publish_year}-{$category_code}{$book_count}";
}


$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publish_date = $_POST['publish_date'];
    $added_date = date('Y-m-d');
    $category = $_POST['category'];
    $summary = $_POST['summary'];
    $status = $_POST['status'];

    if (empty($status)) {
        $error = "Please select a status.";
    } else {
        // Check if book title already exists
        $check_query = "SELECT * FROM books WHERE LOWER(title) = LOWER(?)";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $title);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        $exact_match_found = false;

        while ($row = $check_result->fetch_assoc()) {
            // Compare titles case-insensitively
            if (strtolower($row['title']) === strtolower($title)) {
                $exact_match_found = true;
                break;
            }
        }

        if ($exact_match_found) {
            $error = "A book with title '$title' already exists.";
        } else {
            // Handle file upload
            $target_dir = "book_covers/";
            $target_file = $target_dir . basename($_FILES["cover_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image or fake image
            $check = getimagesize($_FILES["cover_image"]["tmp_name"]);
            if($check !== false) {
                $uploadOk = 1;
            } else {
                $error = "File is not an image.";
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["cover_image"]["size"] > 512000) {
                $error = "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Try to move the uploaded file
            if ($uploadOk == 1 && move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                // Generate book ID
                $book_id2 = generateBookID($title, $publish_date, $category, $conn);

                // Insert book into database
                $sql = "INSERT INTO books (title, author, published_date, added_date, category, cover_image_path, book_id2, summary, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssss", $title, $author, $publish_date, $added_date, $category, $target_file, $book_id2, $summary, $status);

                if ($stmt->execute()) {
                    $success_message = "New book added successfully";
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
                }

                $stmt->close();
            } else {
                // $error = "Sorry, there was an error uploading your file.";
            }
        }

        $check_stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Book</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="index.php?page=add_book" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Book Title:</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="publish_date">Publish Date:</label>
                <input type="date" class="form-control" id="publish_date" name="publish_date" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <input type="text" class="form-control" id="category" name="category" required>
            </div>
            <div class="form-group">
                <label for="summary">Summary:</label>
                <textarea class="form-control" id="summary" name="summary" maxlength="500" required></textarea>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="archived">Archived</option>
                    <option value="available">Non-Archived</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cover_image">Cover Image(must be below 500kb):</label>
                <input type="file" class="form-control-file" id="cover_image" name="cover_image" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
