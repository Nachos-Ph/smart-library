<?php
include('config.php');

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
}
?>
