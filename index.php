<?php

// Scripty scripty doo!

/* $handle = curl_init();
curl_setopt($handle, CURLOPT_URL, 'http://magento.test/');
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($handle);

if ($response === false) {
    echo 'cURL error: ' . curl_error($handle);
} else {
    var_dump($response);
}

curl_close($handle);
exit; */

// Interacting with the Magento API 

$magentoUrl = 'http://magento.test';

$logged_in = true; // Let's pretend we are logged in to the main app

if (!$logged_in) die('Not logged in');

$bearerToken = $_SESSION['bearer_token'] ?? null;

if ($logged_in && !$bearerToken) {
    $bearerToken = createAdminToken();
}
else {
    die('No bearer token and not logged in');
}

echo 'bearerToken: '.($bearerToken ?? 'bearer token not found.');

// Helper functions

// Function to send a cURL request with JSON data and optional Bearer token
function sendCurlJsonRequest($url, $data = [], $bearerToken = null) {
    $jsonData = json_encode($data);
    $ch = curl_init($url);
    $headers = [
        'Content-Type: application/json',
    ];

    if ($bearerToken) {
        $headers[] = 'Authorization: Bearer ' . $bearerToken;
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }

    curl_close($ch);

    return json_decode($response, true);
}

// Create an admin token - Returns a bearer token to act as a system user
function createAdminToken() {
    $url = 'https://magento.test/rest/V1/integration/admin/token';

    $data = [
        'username' => 'john.smith',
        'password' => 'password123'
    ];

    $response = sendCurlJsonRequest($url, $data);

    if (isset($response['token'])) {
        return $response['token'];
    }

    return null;
}


