<?php
include 'config.php';

function fetchBooks($conn, $limit = 6) {
    $sql = "SELECT * FROM books ORDER BY book_id DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchRandomBooks($conn, $limit = 6) {
    $sql = "SELECT * FROM books ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$recentBooks = fetchBooks($conn);
$recommendedBooks = fetchRandomBooks($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="content mt-5">
        <div class="section-box">
            <div class="section-title mb-3">
                <h5>Recently Added</h5>
            </div>
            <div class="row">
                <?php if (empty($recentBooks)): ?>
                    <div class="col-12">
                        <p>No books available</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentBooks as $book): ?>
                        <div class="col-md-2 mt-3 book-item">
                            <a href="index.php?page=book_details&book_id=<?php echo $book['book_id']; ?>">
                                <img src="<?php echo $book['cover_image_path']; ?>" class="book-cover img-fluid" alt="Book Cover">
                            </a>
                            <h6 class="mt-4">
                                <a href="index.php?page=book_details&book_id=<?php echo $book['book_id']; ?>">
                                    <?php echo $book['title']; ?>
                                </a>
                            </h6>
                            <p class="text-muted">
                                <a href="index.php?page=book_details&book_id=<?php echo $book['book_id']; ?>">
                                    <?php echo $book['author']; ?>
                                </a>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="section-box">
            <div class="section-title mb-3">
                <h5>Recommended</h5>
            </div>
            <div class="row">
                <?php if (empty($recommendedBooks)): ?>
                    <div class="col-12">
                        <p>No books available</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recommendedBooks as $book): ?>
                        <div class="col-md-2 mt-3 book-item">
                            <a href="index.php?page=book_details&book_id=<?php echo $book['book_id']; ?>">
                                <img src="<?php echo $book['cover_image_path']; ?>" class="book-cover img-fluid" alt="Book Cover">
                            </a>
                            <h6 class="mt-4">
                                <a href="index.php?page=book_details&book_id=<?php echo $book['book_id']; ?>">
                                    <?php echo $book['title']; ?>
                                </a>
                            </h6>
                            <p class="text-muted">
                                <a href="index.php?page=book_details&book_id=<?php echo $book['book_id']; ?>">
                                    <?php echo $book['author']; ?>
                                </a>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
