<?php
include 'config.php';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch books from the database (adjust the query according to your database schema)
    $sql = "SELECT id, name, img, book_url FROM books";
    $result = $conn->query($sql);
    
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Initialize an empty array to store the books
        $books = array();
        
        // Fetch each row and add it to the books array
        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }
        
        // Return the list of books as a JSON response
        header('Content-Type: application/json');
        echo json_encode($books);
    } else {
        // Return a message if no books are found
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'No books found'));
    }
} else {
    // Return an error response for non-GET requests
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'Invalid request method. Only GET requests are allowed.'));
}

// Close database connection
$conn->close();

?>
