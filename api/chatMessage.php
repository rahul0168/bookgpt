<?php

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $postData = file_get_contents('php://input');
    
    // Decode the JSON data
    $requestData = json_decode($postData, true);
    // var_dump($requestData); exit;
    
    // Check if the required data is present
    if (isset($requestData['sourceId']) && isset($requestData['messages'])) {
        // Process the received data
        $sourceId = $requestData['sourceId'];
        $messages = $requestData['messages'];
        
        // Perform further processing (e.g., saving messages to a database, responding to messages, etc.)
        $apiKey = 'sec_aGKuJToP6atE3QwHnJw1fptDDNOj5s4W';
       
        
        $result = sendMessage($apiKey, $sourceId, $messages); 
        // Return a success response
        $response = array(
            'success' => true,
            'message' => $result,
            
        );
    } else {
        // Return an error response if required data is missing
        $response = array(
            'success' => false,
            'message' => 'Invalid request. Missing required data.'
        );
    }
} else {
    // Return an error response for non-POST requests
    $response = array(
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    );
}

// Set the response headers
header('Content-Type: application/json');

// Send the JSON response
echo json_encode($response);

function sendMessage($apiKey, $sourceId, $message) {
    // API endpoint URL
    $apiEndpoint = 'https://api.chatpdf.com/v1/chats/message';
    
    // Request data
    $requestData = array(
        'sourceId' => $sourceId,
        'messages' => array(
            array(
                'role' => 'user',
                'content' => $message
            )
        )
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
    // $responseData = json_decode($response, true);
    
    // Return the decoded JSON response
    return $response;
}

?>
