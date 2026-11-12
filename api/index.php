<?php

// Force SCRIPT_NAME to root so Laravel stops stripping /api from routes
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Forward Vercel requests to Laravel's public entry point
require __DIR__ . '/../public/index.php';