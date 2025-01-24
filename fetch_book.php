<?php
require_once 'db.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'fetch_random_books') {
        // Fetch 3 random books with a cover URL from the database
        $stmt = $pdo->query("SELECT title, book_cover_url FROM books WHERE book_cover_url IS NOT NULL ORDER BY RAND() LIMIT 3");
        $small_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($small_books) {
            foreach ($small_books as $small_book) {
                echo '<div class="small-content">';
                echo '<h4>' . htmlspecialchars($small_book['title']) . '</h4>';
                echo '<img src="' . htmlspecialchars($small_book['book_cover_url']) . '" alt="Book Cover" class="small-book-cover">';
                echo '</div>';
            }
        } else {
            echo '<div class="small-content">No books available</div>';
        }
        exit;
    }
}

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id");
    $stmt->execute(['id' => $book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // If the request is for JSON response (price only)
        if (isset($_GET['price_only']) && $_GET['price_only'] === '1') {
            echo json_encode(['price' => $book['price']]);
            exit;
        }

        // Render the book details form
        ?>
        <h3>Book Details</h3>
        <form id="manage_book-form">
            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
            <input type="hidden" name="action" value="">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>">
            </div>
            <div class="form-group">
                <label for="isbn">ISBN:</label>
                <input type="text" id="isbn" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>" required>
            </div>
            <div class="form-group">
                <label for="publication_date">Publication Date:</label>
                <input type="date" id="publication_date" name="publication_date" value="<?php echo $book['publication_date']; ?>">
            </div>
            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo $book['quantity']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price (TK):</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $book['price']; ?>" required>
            </div>
            <!-- Update and Delete buttons -->
            <button type="button" id="update-button">Update</button>
            <button type="button" id="delete-button">Delete</button>
        </form>
        <?php
    } else {
        echo "<p>Book not found.</p>";
    }
    exit;
}

if (isset($_GET['title']) && isset($_GET['price_only']) && $_GET['price_only'] === '1') {
    $book_title = $_GET['title'];
    $stmt = $pdo->prepare("SELECT price FROM books WHERE title = :title");
    $stmt->execute(['title' => $book_title]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        echo json_encode(['price' => $book['price']]);
    } else {
        echo json_encode(['price' => null]);
    }
    exit;
}
?>
