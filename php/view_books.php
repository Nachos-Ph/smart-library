<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Pagination settings
$books_per_page = 10;
$page = isset($_GET['pg']) && is_numeric($_GET['pg']) ? (int)$_GET['pg'] : 1;
$offset = ($page - 1) * $books_per_page;

// Fetch total number of books
$total_books_query = "SELECT COUNT(*) as total FROM books";
$total_books_result = $conn->query($total_books_query);
$total_books_row = $total_books_result->fetch_assoc();
$total_books = $total_books_row['total'];

// Calculate total pages
$total_pages = ceil($total_books / $books_per_page);

// Fetch books for the current page
$query = "SELECT book_id, book_id2, title, author, published_date, status 
          FROM books 
          ORDER BY book_id DESC 
          LIMIT $books_per_page OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Books</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Published Year</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['book_id2']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo $row['published_date']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <a href="index.php?page=book_details&book_id=<?php echo $row['book_id']; ?>" class="btn btn-info btn-sm">View Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="index.php?page=view_books&pg=<?php echo $page - 1; ?>" class="btn btn-secondary">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=view_books&pg=<?php echo $i; ?>" class="btn btn-secondary<?php if ($i == $page) echo ' active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="index.php?page=view_books&pg=<?php echo $page + 1; ?>" class="btn btn-secondary">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
