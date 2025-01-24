<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    try {
        if ($action === 'update') {
            $stmt = $pdo->prepare("
                UPDATE books 
                SET title = :title, author = :author, genre = :genre, isbn = :isbn, 
                    publication_date = :publication_date, quantity = :quantity, price = :price 
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $_POST['title'],
                ':author' => $_POST['author'],
                ':genre' => $_POST['genre'],
                ':isbn' => $_POST['isbn'],
                ':publication_date' => $_POST['publication_date'],
                ':quantity' => $_POST['quantity'],
                ':price' => $_POST['price'],
                ':id' => $id,
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Book updated successfully']);
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM books WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Book deleted successfully']);
        } else {
            throw new Exception('Invalid action.');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>
