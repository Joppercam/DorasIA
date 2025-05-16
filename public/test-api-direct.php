<?php

// Direct API test without any UI
// Run this from command line: php public/test-api-direct.php

// First, let's make a direct curl request
$url = "http://localhost:8000/api/titles/1/comments";
echo "Testing API endpoint: $url\n\n";

// Using curl command directly
echo "=== DIRECT CURL TEST ===\n";
$cmd = "curl -s -w '\\nHTTP_CODE: %{http_code}' '$url'";
$output = shell_exec($cmd);
echo "Output:\n$output\n\n";

// Using PHP's curl
echo "=== PHP CURL TEST ===\n";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: " . substr($response, 0, 500) . "\n\n";

// Using file_get_contents
echo "=== FILE_GET_CONTENTS TEST ===\n";
$context = stream_context_create([
    'http' => [
        'header' => "Accept: application/json\r\n" .
                   "Content-Type: application/json\r\n"
    ]
]);
$result = @file_get_contents($url, false, $context);
if ($result === false) {
    echo "Failed to fetch: " . error_get_last()['message'] . "\n";
} else {
    echo "Response: " . substr($result, 0, 500) . "\n";
}

echo "\n=== DONE ===\n";