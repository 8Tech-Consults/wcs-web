<?php
$headers = getallheaders();

// Set the Content-Type header to specify that the response will be JSON
header('Content-Type: application/json');

// Prepare a response array with the headers
$response = [
    'headers' => $headers,
];

// Encode the response array as JSON and output it
echo json_encode($response);
die();
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
