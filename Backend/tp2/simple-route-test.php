<?php
// simple-route-test.php
header('Content-Type: application/json');

$uri = $_SERVER['QUERY_STRING'];
echo json_encode([
    'query_string' => $uri,
    'decoded' => urldecode($uri),
    'trimmed' => trim(urldecode($uri))
]);