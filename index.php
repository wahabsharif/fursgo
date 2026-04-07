<?php
/**
 * Laravel Application Entry Point for Subdirectory Deployment
 * This file routes all requests to the public/index.php
 */

// Get the requested URI
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Remove the base path to get the actual file path
$basePath = '/projects/fursgo';
$requestedPath = $uri;

if (strpos($uri, $basePath) === 0) {
    $requestedPath = substr($uri, strlen($basePath));
}

// If empty path, route to Laravel
if (empty($requestedPath) || $requestedPath === '/') {
    require __DIR__ . '/public/index.php';
    exit;
}

// Build the full path to the public directory
$publicPath = __DIR__ . '/public' . $requestedPath;

// If it's a real file (but not PHP), serve it directly
if (is_file($publicPath) && !preg_match('/\.php$/', $requestedPath)) {
    // Serve the file
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'ico' => 'image/x-icon',
    ];

    $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }

    readfile($publicPath);
    exit;
}

// If it's a directory with an index file, serve that
if (is_dir($publicPath) && is_file($publicPath . '/index.php')) {
    require $publicPath . '/index.php';
    exit;
}

// Otherwise, route to public/index.php for Laravel to handle
require __DIR__ . '/public/index.php';
