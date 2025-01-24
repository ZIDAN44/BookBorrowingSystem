$(document).ready(function () {
    // Initialize search functionality
    $('#search-box').on('input', function () {
        const searchTerm = $(this).val().toLowerCase();
        $('#book-list li').each(function () {
            const bookTitle = $(this).text().toLowerCase();
            $(this).toggle(bookTitle.includes(searchTerm));
        });
    });

    // Fetch book details on click and update dropdown and price
    $(document).on('click', '.book-link', function (e) {
        e.preventDefault();
        const bookId = $(this).data('id');
        const bookTitle = $(this).text();
        fetchBookDetails(bookId);

        // Update the book title dropdown in the borrowing form
        $('#book-title').val(bookTitle);

        // Fetch and update the book price
        fetchBookPrice(bookId);
    });

    // Fetch book price when a book is selected from the dropdown
    $('#book-title').on('change', function () {
        const selectedBook = $(this).val();

        if (selectedBook) {
            $.ajax({
                url: 'fetch_book.php',
                type: 'GET',
                data: { title: selectedBook, price_only: 1 },
                success: function (data) {
                    try {
                        const book = JSON.parse(data);
                        if (book.price) {
                            $('#book-price').text(`${book.price} TK`);
                            $('#fees').val(book.price);
                        } else {
                            $('#book-price').text('Price not available');
                            $('#fees').val('');
                        }
                    } catch (err) {
                        $('#book-price').text('Error fetching price');
                        $('#fees').val('');
                    }
                },
                error: function () {
                    $('#book-price').text('Error fetching price');
                    $('#fees').val('');
                }
            });
        } else {
            $('#book-price').text('Select a book to view price');
            $('#fees').val('');
        }
    });

    // Handle update/delete actions
    $(document).on('click', '#update-button, #delete-button', function () {
        const action = $(this).is('#update-button') ? 'update' : 'delete';
        handleBookAction(action);
    });

    // Submit form via AJAX
    $(document).on('submit', '#manage_book-form', function (e) {
        e.preventDefault();
        submitBookForm($(this));
    });

    // Function to fetch book details via AJAX
    function fetchBookDetails(bookId) {
        $.ajax({
            url: 'fetch_book.php',
            type: 'GET',
            data: { id: bookId },
            success: function (data) {
                $('#book-details').html(data);
            },
            error: function () {
                alert('Failed to fetch book details.');
            }
        });
    }

    // Fetch and display the book price by ID
    function fetchBookPrice(bookId) {
        $.ajax({
            url: 'fetch_book.php',
            type: 'GET',
            data: { id: bookId, price_only: 1 },
            success: function (data) {
                try {
                    const book = JSON.parse(data);
                    if (book.price) {
                        $('#book-price').text(`${book.price} TK`);
                        $('#fees').val(book.price);
                    } else {
                        $('#book-price').text('Price not available');
                        $('#fees').val('');
                    }
                } catch (err) {
                    $('#book-price').text('Error fetching price');
                    $('#fees').val('');
                }
            },
            error: function () {
                $('#book-price').text('Error fetching price');
                $('#fees').val('');
            }
        });
    }

    // Function to handle update/delete actions
    function handleBookAction(action) {
        $('#manage_book-form').find('input[name="action"]').val(action);
        $('#manage_book-form').submit();
    }

    // Function to submit the book form via AJAX
    function submitBookForm(form) {
        $.ajax({
            url: 'manage_book.php',
            type: 'POST',
            data: form.serialize(),
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert(res.message);
                        resetBookDetails();
                        refreshBookList();
                    } else {
                        alert(res.message);
                    }
                } catch (err) {
                    alert('Unexpected server response.');
                }
            },
            error: function () {
                alert('Failed to update/delete book.');
            }
        });
    }

    // Fetch and display random books
    function fetchRandomBooks() {
        $.ajax({
            url: 'fetch_book.php',
            type: 'GET',
            data: { action: 'fetch_random_books' },
            success: function (data) {
                $('#random-books').html(data);
            },
            error: function () {
                $('#random-books').html('<p>Failed to fetch random books.</p>');
            }
        });
    }

    // Call the function on page load
    fetchRandomBooks();

    // Reset book details view
    function resetBookDetails() {
        $('#book-details').html('<h3>Select a book to view details</h3>');
    }

    // Refresh book list dynamically
    function refreshBookList() {
        $('#book-list').load(location.href + ' #book-list');
    }
});
