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

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['is_admin'];
$search_user_id = null;
$user_data = null;
$borrowed_books = [];
$fines = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $is_admin) {
    $search_user_id = $_POST['user_id'];

    // Validate user ID input
    if (!is_numeric($search_user_id)) {
        $error = "Invalid user ID.";
    } else {
        // Fetch user details
        $sql = "SELECT username, email, password, role, created_at FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $search_user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($username, $email, $password, $role, $created_at);
            $stmt->fetch();
            $user_data = [
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'created_at' => $created_at
            ];

            // Fetch borrowing history
            $borrow_sql = "SELECT bb.book_id, b.title, bb.borrow_date, bb.due_date, bb.return_date 
                          FROM borrowed_books bb 
                          INNER JOIN books b ON bb.book_id = b.book_id 
                          WHERE bb.user_id = ?";
            $borrow_stmt = $conn->prepare($borrow_sql);
            $borrow_stmt->bind_param("i", $search_user_id);
            $borrow_stmt->execute();
            $borrow_stmt->bind_result($book_id, $title, $borrow_date, $due_date, $return_date);
            while ($borrow_stmt->fetch()) {
                $borrowed_books[] = [
                    'book_id' => $book_id,
                    'title' => $title,
                    'borrow_date' => $borrow_date,
                    'due_date' => $due_date,
                    'return_date' => $return_date
                ];
            }
            $borrow_stmt->close();

            // Fetch fines
            $fines_sql = "SELECT book_id, fine_amount FROM fines WHERE user_id = ?";
            $fines_stmt = $conn->prepare($fines_sql);
            $fines_stmt->bind_param("i", $search_user_id);
            $fines_stmt->execute();
            $fines_stmt->bind_result($book_id, $fine_amount);
            while ($fines_stmt->fetch()) {
                $fines[] = [
                    'book_id' => $book_id,
                    'fine_amount' => $fine_amount
                ];
            }
            $fines_stmt->close();
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
} else {
    // Fetch logged-in user's details if not an admin
    if (!$is_admin) {
        $sql = "SELECT username, email, password, role, created_at FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($username, $email, $password, $role, $created_at);
        $stmt->fetch();
        $stmt->close();
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'created_at' => $created_at
        ];

        // Fetch borrowing history for current user
        $borrow_sql = "SELECT bb.book_id, b.title, bb.borrow_date, bb.due_date, bb.return_date 
                      FROM borrowed_books bb 
                      INNER JOIN books b ON bb.book_id = b.book_id 
                      WHERE bb.user_id = ?";
        $borrow_stmt = $conn->prepare($borrow_sql);
        $borrow_stmt->bind_param("i", $user_id);
        $borrow_stmt->execute();
        $borrow_stmt->bind_result($book_id, $title, $borrow_date, $due_date, $return_date);
        while ($borrow_stmt->fetch()) {
            $borrowed_books[] = [
                'book_id' => $book_id,
                'title' => $title,
                'borrow_date' => $borrow_date,
                'due_date' => $due_date,
                'return_date' => $return_date
            ];
        }
        $borrow_stmt->close();

        // Fetch fines for current user
        $fines_sql = "SELECT book_id, fine_amount FROM fines WHERE user_id = ?";
        $fines_stmt = $conn->prepare($fines_sql);
        $fines_stmt->bind_param("i", $user_id);
        $fines_stmt->execute();
        $fines_stmt->bind_result($book_id, $fine_amount);
        while ($fines_stmt->fetch()) {
            $fines[] = [
                'book_id' => $book_id,
                'fine_amount' => $fine_amount
            ];
        }
        $fines_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container mt-5">
        <h2>User Details</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <form action="index.php?page=user_details" method="post">
                <div class="form-group">
                    <label for="user_id">Search User ID:</label>
                    <input type="number" class="form-control" id="user_id" name="user_id" required>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        <?php endif; ?>

        <?php if ($user_data): ?>
            <h3>User Information</h3>
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <td><?php echo htmlspecialchars($user_data['username']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><?php echo str_repeat('*', strlen($user_data['password'])); ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?php echo $user_data['role'] == 1 ? 'Admin' : 'User'; ?></td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?php echo $user_data['created_at']; ?></td>
                </tr>
            </table>

            <?php if ($is_admin || !$is_admin && count($borrowed_books) > 0): ?>
                <h3>Borrowing History</h3>
                <?php if (count($borrowed_books) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Book Title</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($borrowed_books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['borrow_date']); ?></td>
                                    <td><?php echo htmlspecialchars($book['due_date']); ?></td>
                                    <td><?php echo htmlspecialchars($book['return_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No borrowing history found.</p>
                <?php endif; ?>

                <h3>Fines</h3>
                <?php if (count($fines) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Book ID</th>
                                <th>Fine Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fines as $fine): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($fine['book_id']); ?></td>
                                    <td><?php echo htmlspecialchars($fine['fine_amount']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No fines found.</p>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <a href="index.php?page=users" class="btn btn-primary mt-3">Back to Users</a>
    </div>
</body>
</html>
