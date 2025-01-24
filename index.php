<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrowing System</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="main.js"></script>
</head>
<body>

    <!-- Student ID Image -->
    <img src="images/My-ID.png" alt="Student ID" class="top-right-image">

    <div class="container">
        <!-- Top Container: Display All Books -->
        <div class="top-container">
            <h3>All Books</h3>
            <input type="text" id="search-box" placeholder="Search for books..." />
            <ul id="book-list">
                <?php
                require_once 'db.php';

                // Fetch all books from the database
                $stmt = $pdo->query("SELECT * FROM books");
                $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($books) {
                    foreach ($books as $book) {
                        echo "<li><a href='#' class='book-link' data-id='" . $book['id'] . "'>" . htmlspecialchars($book['title']) . "</a></li>";
                    }
                } else {
                    echo "<li>No books available.</li>";
                }
                ?>
            </ul>
        </div>

        <div class="main-content">
            <!-- Displaying all used tokens -->
            <div class="left-sidebar">
                <h4>Used Tokens:</h4>
                <ul>
                    <?php
                    $used_tokens = json_decode(file_get_contents('u_token.json'), true);
                    if ($used_tokens) {
                        foreach ($used_tokens as $used) {
                            echo "<li>Token: " . $used['token'] . "</li>";
                        }
                    } else {
                        echo "<li>No used tokens available.</li>";
                    }
                    ?>
                </ul>
            </div>

            <div class="content-area">
                <!-- Display Book Details in content1 -->
                <div class="content1" id="book-details">
                    <h3>Select a book to view details</h3>
                </div>
                <!-- Add Books Form -->
                <div class="content2">
                    <h3>Add Books</h3>
                    <form action="insert_book.php" method="post">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre:</label>
                            <input type="text" id="genre" name="genre">
                        </div>
                        <div class="form-group">
                            <label for="isbn">ISBN:</label>
                            <input type="text" id="isbn" name="isbn" required>
                        </div>
                        <div class="form-group">
                            <label for="publication_date">Publication Date:</label>
                            <input type="date" id="publication_date" name="publication_date">
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price (TK):</label>
                            <input type="number" id="price" name="price" step="0.01" required>
                        </div>
                        <button type="submit">Add Book</button>
                    </form>
                </div>
                <div class="small-contents" id="random-books">
                    <!-- Random books will be loaded here via AJAX -->
                </div>
            </div>

            <div class="right-sidebar">
                <h4>Recently Added Books</h4>
                <ul>
                    <?php
                    require_once 'db.php';

                    // Fetch the two most recently added books
                    $recentBooksStmt = $pdo->query("SELECT title, author, book_cover_url FROM books ORDER BY created_at DESC LIMIT 2");
                    $recentBooks = $recentBooksStmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($recentBooks) {
                        foreach ($recentBooks as $recentBook) {
                            echo "<li>";
                            echo "<strong>" . htmlspecialchars($recentBook['title']) . "</strong><br>";
                            echo "<em>by " . htmlspecialchars($recentBook['author']) . "</em><br>";
                            if (!empty($recentBook['book_cover_url'])) {
                                echo "<img src='" . htmlspecialchars($recentBook['book_cover_url']) . "' alt='" . htmlspecialchars($recentBook['title']) . "'>";
                            }
                            echo "</li>";
                        }
                    } else {
                        echo "<li>No recent books available.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div class="footer">
            <div class="borrowing-form">
                <!-- Borrowing Form -->
                <form action="process.php" method="post">
                    <h3>Borrowing Form</h3>
                    <div class="form-content">
                        <label for="student-name">Student Name:</label>
                        <input type="text" id="student-name" name="student_name" required>

                        <label for="student-id">Student ID:</label>
                        <input type="text" id="student-id" name="student_id" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>

                        <label for="book-title">Book Title:</label>
                        <select id="book-title" name="book_title" required>
                            <option value="">Select a Book</option>
                            <?php
                            foreach ($books as $book) {
                                echo "<option value='" . htmlspecialchars($book['title']) . "'>" . htmlspecialchars($book['title']) . "</option>";
                            }
                            ?>
                        </select>

                        <label for="borrowing-date">Borrowing Date:</label>
                        <input type="date" id="borrowing-date" name="borrowing_date" required>

                        <label for="token">Token:</label>
                        <input type="text" id="token" name="token">

                        <label for="return-date">Return Date:</label>
                        <input type="date" id="return-date" name="return_date" required>

                        <label for="fees">Fees (in TK):</label>
                        <span id="book-price">Select a book to view price</span>
                        <input type="hidden" id="fees" name="fees" value="" required>

                        <button type="submit">Submit</button>
                    </div>
                </form>
            </div>
            <!-- Displaying all tokens -->
            <div class="footer-right">
                <h4>Available Tokens:</h4>
                <ul>
                    <?php
                    $tokens = json_decode(file_get_contents('token.json'), true);
                    if ($tokens) {
                        foreach ($tokens as $valid) {
                            echo "<li>Token: " . $valid['token'] . "</li>";
                        }
                    } else {
                        echo "<li>No tokens available.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
