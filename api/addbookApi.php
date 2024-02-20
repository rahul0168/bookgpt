<?php
include 'config.php';
// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input fields
    $name = $_POST["name"];
    $bookimg = $_FILES["bookimg"]["name"];
    $bookurl = $_POST["bookurl"];
    $apiKey = 'sec_aGKuJToP6atE3QwHnJw1fptDDNOj5s4W';
    // File upload handling
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["bookimg"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["bookimg"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["bookimg"]["size"] > 5000000) {
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // File upload failed
        $response = array("success" => false, "message" => "File upload failed.");
    } else {
        // File upload successful, move file to designated directory
        if (move_uploaded_file($_FILES["bookimg"]["tmp_name"], $target_file)) {
            // Insert data into database
            $sourceId = addURLToSource($apiKey, $bookurl);
            $sql = "INSERT INTO books (name, img, book_url,source_id) VALUES ('$name', '$bookimg', '$bookurl','$sourceId')";
            if ($conn->query($sql) === TRUE) {
                $response = array("success" => true, "message" => $sourceId);
            } else {
                $response = array("success" => false, "message" => "Error: " . $sql . "<br>" . $conn->error);
            }
        } else {
            $response = array("success" => false, "message" => "Sorry, there was an error uploading your file.");
        }
    }

    echo json_encode($response);
}

$conn->close();

function addURLToSource($apiKey, $url) {
    // API endpoint URL
    $apiEndpoint = 'https://api.chatpdf.com/v1/sources/add-url';
    
    // Request data
    $requestData = array(
        'url' => $url
    );
    
    // Set cURL options
    $ch = curl_init($apiEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'x-api-key: ' . $apiKey,
        'Content-Type: application/json'
    ));
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for errors
    if($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return array('success' => false, 'message' => 'cURL error: ' . $error);
    }
    
    // Close cURL
    curl_close($ch);
    
    // Decode JSON response
    $responseData = json_decode($response, true);
    
    // Return the decoded JSON response
    return $responseData['sourceId'];
}

?>