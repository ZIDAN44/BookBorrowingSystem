<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $publication_date = $_POST['publication_date'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Validation
    $errors = [];

    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
        $errors[] = "Invalid book title.";
    }

    if (!preg_match('/^[a-zA-Z\s]+$/', $author)) {
        $errors[] = "Invalid author name.";
    }

    if (!preg_match('/^\d{13}$/', $isbn)) {
        $errors[] = "ISBN must be 13 digits.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO books (title, author, genre, isbn, publication_date, quantity, price)
                VALUES (:title, :author, :genre, :isbn, :publication_date, :quantity, :price)
            ");
            $stmt->execute([
                ':title' => $title,
                ':author' => $author,
                ':genre' => $genre,
                ':isbn' => $isbn,
                ':publication_date' => $publication_date,
                ':quantity' => $quantity,
                ':price' => $price,
            ]);
            echo "Book successfully added to the database!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "<h2>Errors:</h2><ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
}
?>
