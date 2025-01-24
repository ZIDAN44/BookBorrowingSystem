<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $student_id = $_POST['student_id'];
    $email = $_POST['email'];
    $book_title = $_POST['book_title'];
    $borrowing_date = $_POST['borrowing_date'];
    $token = $_POST['token'];
    $return_date = $_POST['return_date'];
    $fees = $_POST['fees'];

    // Validation
    $errors = [];

    // Validate Student Name (only alphabets)
    if (!preg_match("/^[a-zA-Z\s]+$/", $student_name)) {
        $errors[] = "Student name should only contain alphabets and spaces.";
    }

    // Validate Student ID format (XX-XXXXX-X with all digits)
    if (!preg_match("/^\d{2}-\d{5}-\d$/", $student_id)) {
        $errors[] = "Student ID must be in the format XX-XXXXX-X (e.g., 22-47918-2).";
    }

    // Validate Email (must match the format XX-XXXXX-X@student.aiub.edu)
    if (!preg_match("/^\d{2}-\d{5}-\d@student\.aiub\.edu$/", $email)) {
        $errors[] = "Email must be in the format XX-XXXXX-X@student.aiub.edu (e.g., 22-47918-2@student.aiub.edu).";
    }

    // Validate Borrowing Date and Return Date
    try {
        $borrowing_date_obj = new DateTime($borrowing_date);
        $return_date_obj = new DateTime($return_date);
    } catch (Exception $e) {
        $errors[] = "Invalid date format.";
    }

    if (empty($errors)) {
        $interval = $borrowing_date_obj->diff($return_date_obj)->days;

        // Check if Return Date is in the future relative to Borrowing Date
        if ($return_date_obj <= $borrowing_date_obj) {
            $errors[] = "The Return Date must be a future date from the Borrowing Date.";
        }

        // Validate Borrowing Date and Return Date interval
        if ($interval > 10 && empty($token)) {
            $errors[] = "You have exceeded more than 10 days, so you need a token!";
        } elseif ($interval != 10 && $interval <= 10 && empty($token)) {
            $errors[] = "The Return Date must be exactly 10 days after the Borrowing Date.";
        }
    }

    // Check if token is valid by checking in token.json only if token is provided
    if (!empty($token)) {
        $tokens = json_decode(file_get_contents('token.json'), true);
        $valid_token = false;
        foreach ($tokens as $key => $valid) {
            if ($valid['token'] == $token) {
                $valid_token = true;

                // Remove the used token from token.json
                unset($tokens[$key]);
                file_put_contents('token.json', json_encode(array_values($tokens), JSON_PRETTY_PRINT));

                // Store the used token in u_token.json
                $used_tokens = json_decode(file_get_contents('u_token.json'), true);
                if (!$used_tokens) {
                    $used_tokens = [];
                }
                $used_tokens[] = ["token" => $token];
                file_put_contents('u_token.json', json_encode($used_tokens, JSON_PRETTY_PRINT));

                break;
            }
        }
        if (!$valid_token) {
            $errors[] = "Invalid token entered.";
        }
    }

    // Check Cookie
    if (!empty($_COOKIE['book_borrowing']) && strpos($_COOKIE['book_borrowing'], $book_title) !== false) {
        $errors[] = "This book is currently borrowed by another student. Please try again later.";
    }

    // Display errors if any
    if (!empty($errors)) {
        echo "<h2>Errors:</h2>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    } else {
        // Set a cookie to track the borrowing
        $cookie_value = "$book_title|$student_name";
        setcookie('book_borrowing', $cookie_value, time() + 40);

        // Display receipt
        echo "<h2>Receipt</h2>";
        echo "<p><strong>Student Name:</strong> $student_name</p>";
        echo "<p><strong>Student ID:</strong> $student_id</p>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Book Title:</strong> $book_title</p>";
        echo "<p><strong>Borrowing Date:</strong> $borrowing_date</p>";
        echo "<p><strong>Token:</strong> $token</p>";
        echo "<p><strong>Return Date:</strong> $return_date</p>";
        echo "<p><strong>Fees:</strong> $fees TK</p>";
    }

    // Check if any cookies are set
    echo "<br><strong>Cookie is set:</strong><br>";
    if (isset($_COOKIE['book_borrowing'])) {
        echo "Book Borrowing Cookie: " . $_COOKIE['book_borrowing'] . "<br>";
    } else {
        echo "No specific cookie for book borrowing found.<br>";
    }
}
?>
